<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Company;
use App\Models\FieldValue;
use App\Models\ContactSection;
use App\Models\Contacts;
use App\Models\ProductSection;
use App\Models\EmployeeSection;
use Exception;
use Helper;
use Log;
use Auth;
use View;

class DynamicFormController extends Controller
{
    private $moduleName;
    private $inputTypes;
    public function __construct()
    {
        $this->moduleName = config('module_name.modules');
        $this->inputTypes = ['1'=>'Single line text','2'=>'Dropdown','3'=>'Radio','4'=>'Checkbox','5'=>'Paragraph text','6'=>'Number','7'=>'Date'];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$companyId)
    {
		
        try{
            $cId = decrypt($companyId);
            $clients = Company::pluck('company_name','id');
            $companyData = Helper::getCompany($cId);
			$formarray = array();
			$productsection = ProductSection::where('client_id','=',$companyData->client_id)->where('company_id','=',$cId)->count();
			$contactsection = ContactSection::where('client_id','=',$companyData->client_id)->where('company_id','=',$cId)->count();
			$employeesection = EmployeeSection::where('client_id','=',$companyData->client_id)->where('company_id','=',$cId)->count();
			
			if($productsection>0){
				$formarray[] = array('name'=>'Product','action'=>'<a href="'.route('admin.custom-field.edit',['companyId'=>$companyId,'moduleType'=>'product']).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>');
			}
			if($contactsection>0){
				$formarray[] = array('name'=>'Contact','action'=>'<a href="'.route('admin.custom-field.edit',['companyId'=>$companyId,'moduleType'=>'contact']).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>');
			}
			if($employeesection>0){
				$formarray[] = array('name'=>'Employee','action'=>'<a href="'.route('admin.custom-field.edit',['companyId'=>$companyId,'moduleType'=>'employee']).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>');
			}
            
            return view('admin.dynamic.index',compact('clients','companyId','companyData','formarray'));
        }catch(Exceptio $e){
            abort($e);
        }
    }
	
	public function anyData(Request $request,$companyId){
		
	}

    /**
     * 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($companyId)
    {
        try{
            $companyId = decrypt($companyId);
            $inputTypes = $this->inputTypes;
            $companyData = Helper::getCompany($companyId);
            $client = Company::pluck('company_name','id');
            $moduleName = $this->moduleName;
			$forms = array();
			
            return view('admin.dynamic.create',compact('inputTypes','moduleName','client','companyId','companyData'));
        }catch(Exception $e){
            abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		//\DB::enableQueryLog();
        $companyId = $request->company_id;
        $moduleId = $request->module_name;
        $sectionData = $request->section;
        $fieldData = $request->field_data;
        $companyData = Company::find($companyId);
        $formType = $request->form_type;
        $sectionKeys = [];
        $fieldKeys = [];
        $mainModelId = 0;
        if(!empty($sectionData))
        {
            foreach($sectionData as $key=>$row)
            {
                $modelName = Helper::sectionModel($moduleId);
                $section = $modelName;
                if($formType == 1 && !empty($row['is_edit_section']))
                {
                    $section = $modelName->find($key);
					if($section){
					}else{
						$section = Helper::sectionModel($moduleId);
					}
                }
                $section->client_id = $companyData->client_id;
                $section->company_id = $companyId;
                $section->name = !empty($row['name']) ? $row['name'] : null;
                $section->priority = !empty($row['priority']) ? $row['priority'] : null;
                $section->created_by = Auth::user()->id;
                $section->save();
                $sectionKeys[] = $section->id;
                if(!empty($fieldData[$key])){
                    foreach($fieldData[$key] as $fieldKey=>$value)
                    {
                        $fieldModel = Helper::fieldModel($moduleId);
                        if($formType == 1 && !empty($value['is_edit_field']))
                        {
                            $fieldModel = $fieldModel->find($fieldKey);
							if($fieldModel){
								
							}else{
								$fieldModel = Helper::fieldModel($moduleId);
							}
                        }
                        // if(empty($value['pre_field'])){
                            $fieldModel->client_id = $companyData->client_id;
                            $fieldModel->company_id = $companyId;
                            $fieldModel->section_id = $section->id;
                            $fieldModel->input_type = !empty($value['input_type']) ? $value['input_type'] : null;
                            $fieldModel->label_name = !empty($value['label_name']) ? $value['label_name'] : null;
                            $fieldModel->minlength = !empty($value['minlength']) ? $value['minlength'] : null;
                            $fieldModel->maxlength = !empty($value['maxlength']) ? $value['maxlength'] : null;
                            $fieldModel->pattern = !empty($value['pattern']) ? $value['pattern'] : null;
                            $fieldModel->minvalue = !empty($value['min_value']) ? $value['min_value'] : null;
                            $fieldModel->maxvalue = !empty($value['max_value']) ? $value['max_value'] : null;
                            $fieldModel->is_required = !empty($value['is_required']) ? $value['is_required'] : 0;
                            $fieldModel->is_searchable = !empty($value['is_search']) ? $value['is_search'] : 0;
                            $fieldModel->is_pre_field = !empty($value['pre_field']) ? $value['pre_field'] : 0;
                            $fieldModel->is_select_multiple = !empty($value['is_multiple_select']) ? $value['is_multiple_select'] : 0;
                            $fieldModel->created_by = Auth::user()->id;
                            $fieldModel->save();
                            $fieldKeys[] = $fieldModel->id;
                            if(!empty($value['values']))
                            {
                                $values = $value['values'];
                                $values = str_replace("\r\n", ",",$values);
                                $values = explode(',',$values);
                                if(!empty($values))
                                {
                                    FieldValue::where('type',$moduleId)->where('type_id',$fieldModel->id)->delete();
                                    foreach($values as $fieldValue)
                                    {
                                        $fieldValueData = new FieldValue();
                                        $fieldValueData->type = $moduleId;
                                        $fieldValueData->type_id = $fieldModel->id;
                                        $fieldValueData->values = $fieldValue;
                                        $fieldValueData->save();
                                    }
                                }
                            }
                        // }
                    }
                }
            }
        }
		$message = "Your form fields are successfully added.";
        if($formType == 1)
        {
			$message = "Your form fields are successfully updated.";
            //if(!empty($sectionKeys))
            {
                $sectionModel = Helper::sectionModel($moduleId);
                $sectionModel = $sectionModel->whereNotIn('id',$sectionKeys)->update(['deleted_by'=>Auth::user()->id,'deleted_at'=>date('Y-m-d H:i:s')]);
            }
            //if(!empty($fieldKeys))
            {
                $fieldModel = Helper::fieldModel($moduleId);
                $fieldModel = $fieldModel->whereNotIn('id',$fieldKeys)->update(['deleted_by'=>Auth::user()->id,'deleted_at'=>date('Y-m-d H:i:s')]);
            }
            
        }
		//$qr = \DB::getQueryLog();
		//echo '<pre>';print_r($qr);exit;
        return redirect('rkadmin/custom-form/'.encrypt($companyId))->with('success',$message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($companyId,$moduleType)
    {
        try{
			//\DB::enableQueryLog();
            $companyId = decrypt($companyId);
            $inputTypes = $this->inputTypes;
            $companyData = Helper::getCompany($companyId);
            $client = Company::pluck('company_name','id');
            $moduleName = $this->moduleName;
			if($moduleType == 'contact')
            $formData = ContactSection::where('company_id',$companyId)->with(['getFieldData'])->get();
			if($moduleType == 'product')
				$formData = ProductSection::where('company_id',$companyId)->with(['getFieldData'])->get();
			if($moduleType == 'employee')
				$formData = EmployeeSection::where('company_id',$companyId)->with(['getFieldData'])->get();
			//echo '<pre>';print_r(\DB::getQueryLog());exit;
            return view('admin.dynamic.edit',compact('companyId','inputTypes','moduleName','client','formData','companyData','moduleType'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function storeSection($sectionData)
    {
        if(!empty($sectionData))
        {
            $modelName = Helper::sectionModel($moduleId);
            $section = $modelName;
            if($formType == 1)
            {
                $section = $modelName->find($sectionId);
            }
            $section->client_id = $clientId;
            $section->company_id = $companyId;
            $section->name = !empty($sectionData['name']) ? $sectionData['name'] : null;
            $section->priority = !empty($sectionData['priority']) ? $sectionData['priority'] : null;
            $section->created_by = Auth::user()->id;
            $section->save();
            return ['section'=>$section];
        }
    }
}
