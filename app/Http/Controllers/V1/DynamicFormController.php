<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactSection;
use App\Models\ContactField;
use App\Models\ContactValue;
use App\Models\EmployeeSection;
use App\Models\EmployeeField;
use App\Models\EmployeeValue;
use App\Models\ProductSection;
use App\Models\ProductField;
use App\Models\ProductValue;
use JWTAuth;
use Validator;
use Helper;

class DynamicFormController extends Controller
{
    private $moduleName;

    public function __construct()
    {
        $this->moduleName = config('module_name.modules');
    }

    // static response for dynaimc form
    public function index(Request $request,$id)
    {
        if(!isset($this->moduleName[$id]))
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Module not found."
            ]);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $modelName = Helper::sectionModel($id);
        $inputTypes = config('module_name.input_types');
        $data = [];
        $fieldData = collect($modelName->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->with('getFieldData')->orderBy('priority','asc')->orderBy('name','asc')->get())->map(function($q) use($inputTypes,$data,$id){
                            $data['section_id'] = $q->id;
                            $data['section_title'] = $q->name;
                            
                            foreach($q->getFieldData as $row)
                            {
                                if(in_array($row->input_type,[2,3,4]))
                                {
									$arr = [];
                                    $valuesData = Helper::getFieldValue($id,$row->id);
                                    if(!empty($valuesData)){
                                        foreach ($valuesData as $key => $value) {
                                            $arr[] = (object) ['value' => $value];
                                        }
                                    }
                                    $row->values = $arr;
                                }else{
                                    $row->values = [];
                                }
                                $row->input_type = strtolower($inputTypes[$row->input_type]);
                                $data['field_data'][] = $row;
                            }
                            return $data;
                        });
        if($fieldData->isEmpty())
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Field not available."
            ]);
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $fieldData
        ]);
    }

    public function getModule()
    {
        $modules = $this->moduleName;
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('modules')
        ]);
    }

    public function store(Request $request,$formType,$type,$formId=null)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $fieldValueModel = Helper::fieldValueModel($type);
        if(!$fieldValueModel)
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Module not found.'
            ]);
        }
        $fieldModel = Helper::fieldModel($type);
        $modelName = Helper::sectionModel($type);
        $sectionIds = $modelName->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
        $fieldData = $fieldModel->whereIn('section_id',$sectionIds)->get();
        if(count($fieldData) == 0)
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Module not found.'
            ]);
        }
        if($formType == 'update')
        {
            $tableName = $fieldValueModel->getTable();
            \DB::table('contact_values')->where('client_id',$user->organization_id)->where('company_id',$user->company_id) 
                                ->where('contact_id',$formId)
                                ->delete();
        }
        $contactData = [];
        $modelId = 0;
        foreach($fieldData as $key=>$row)
        {
            if($row->is_pre_field == 1){
                $contactData[$row->label_name] = $request->get($row->label_name);
                $contactData['client_id'] = $user->organization_id;
                $contactData['company_id'] = $user->company_id;
            }else{
                $valueData[$key]['name'] = $row->label_name;
                $valueData[$key]['value'] = $request->get($row->label_name);
                $valueData[$key]['field_id'] = $row->id;
                $valueData[$key]['client_id'] = $user->organization_id;
                $valueData[$key]['company_id'] = $user->company_id;
                $valueData[$key]['created_by'] = $user->id;
                if($formType == 'update'){
                    $valueData[$key]['updated_by'] = $user->id;
                }
                $valueData[$key]['created_at'] = date('Y-m-d H:i:s');
                $valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        $model = Helper::getModelName($type);
		$dataid = Helper::fieldPrimaryIdColumn($type);
        if(!empty($contactData)){
            $model = $model->updateOrCreate(['id'=>$formId],$contactData);
            $modelId = $model->id;
        }
        $valueData = array_map(function($q) use($modelId,$dataid){
            //$q['contact_id'] = $modelId;
            $q[$dataid] = $modelId;
            return $q;
        }, $valueData);
        $fieldModelData = $fieldValueModel->insert($valueData);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Your form is successfully submited.'
        ]);
    }

    public function getFormValue(Request $request,$type,$dynamicFormId)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $fieldValueModel = Helper::fieldValueModel($type);
        if(!$fieldValueModel)
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Module not found.'
            ]);
        }
        $formValueData = [];
		$dataid = Helper::fieldPrimaryIdColumn($type);
		
        $formValue = collect($fieldValueModel->where('client_id',$user->organization_id)
                                    ->where($dataid,$dynamicFormId)
                                    ->where('company_id',$user->company_id)
                                    //->where('created_by',$user->id)
                                    ->get())->map(function($q) use($formValueData){
                                        $formValueData[$q->name] = $q->value;
                                        return $formValueData;
                                    });
        return response()->json([
            'status' => 'SUCCESS',
            'data' => \Arr::collapse($formValue)
        ]);
    }

    public function allDynamicForm(Request $request,$type)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $fieldValueModel = Helper::fieldValueModel($type);
        if(!$fieldValueModel)
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Module not found.'
            ]);
        }
        $formValueData = [];
        $formValue = collect($fieldValueModel->where('client_id',$user->organization_id)
                        ->where('company_id',$user->company_id)
                        ->get());
        $formValue = $formValue->groupBy('contact_id');
        $formValueData = [];
        foreach($formValue as $key=>$row)
        {
            $formData = [];
            foreach($row as $value){
                $formData['id'] = $key;
                $formData[$value->name] = $value->value;
                $formData['email'] = $value->getContactValue->email;
                $formData['name'] = $value->getContactValue->name;
            }
            $formValueData[] = $formData;
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $formValueData
        ]);
    }
}
