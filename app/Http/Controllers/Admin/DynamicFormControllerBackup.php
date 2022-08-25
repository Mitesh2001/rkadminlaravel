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

class DynamicFormControllerBackup extends Controller
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
            if($request->ajax())
            {
                $inputTypes = $this->inputTypes;   
                $companyId = $request->company_id;
                $formData = ContactSection::where('company_id',$cId)->get();
                $isEdit = count($formData) ? 1 : 0;
                $data['status'] = 1;
                $data['isEdit'] = $isEdit;
                $data['custom_form'] = View::make('admin.dynamic.custom_form_data',compact('formData','inputTypes'))->render();
                return $data;
            }
            return view('admin.dynamic.index',compact('clients','companyId','companyData'));
        }catch(Exceptio $e){
            abort($e);
        }
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
                        }
                        // if(empty($value['pre_field'])){
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
        if($formType == 1)
        {
            if(!empty($sectionKeys))
            {
                $sectionModel = Helper::sectionModel($moduleId);
                $sectionModel = $sectionModel->whereNotIn('id',$sectionKeys)->update(['deleted_by'=>Auth::user()->id,'deleted_at'=>date('Y-m-d H:i:s')]);
            }
            if(!empty($fieldKeys))
            {
                $fieldModel = Helper::fieldModel($moduleId);
                $fieldModel = $fieldModel->whereNotIn('id',$fieldKeys)->update(['deleted_by'=>Auth::user()->id,'deleted_at'=>date('Y-m-d H:i:s')]);
            }
            
        }
        return redirect('rkadmin/custom-form/'.encrypt($companyId))->with('success','Your form is successfully added.');
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
            $companyId = decrypt($companyId);
            $inputTypes = $this->inputTypes;
            $companyData = Helper::getCompany($companyId);
            $client = Company::pluck('company_name','id');
            $moduleName = $this->moduleName;
            $formData = ContactSection::where('company_id',$companyId)->get();
            return view('admin.dynamic.edit',compact('companyId','inputTypes','moduleName','client','formData','companyData'));
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
