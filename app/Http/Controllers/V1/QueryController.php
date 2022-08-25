<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\QueryRule;
use Exception;
use Validator;
use Helper;
use JWTAuth;

class QueryController extends Controller
{
    
    public function addRule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required|integer',
            'rule_query' => 'required',
            'rule_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        
        $this->storeRule($user->id,$user->organization_id,$user->company_id,$request->module,$request->rule_query,$request->rule_name,$request->group_by,$request->tree,$request->selected_column);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully your query rules is added.'
        ]);
    }

    public function getRule(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $queryRule = QueryRule::where('client_id',$user->organization_id)
                                ->where('company_id',$user->company_id)
                                ->where('user_id',$user->id)
                                ->get(['id','module','rule_query','rule_name','group_by','selected_column']);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('queryRule')
        ]);
    }

    public function showRule($id)
    {
        $queryRule = QueryRule::select('id','module','rule_query','rule_name','group_by','tree','selected_column')->find($id);
        $queryRule->group_by = explode(',',$queryRule->group_by);
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('queryRule')
        ]);
    }

    public function getTableName()
    {
        $tableList = ['leads'=>'leads','contacts'=>'contacts'];
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('tableList')
        ]);
    }

    public function getColumnList($tableName)
    {
        //    $data = DB::getSchemaBuilder()->getColumnListing($tableName);
        $data = Helper::getColumnStructure($tableName);
        $columnList = [];
        $data = !empty($data) ? \Arr::collapse($data) : [];
        $j = 0;
        $data = collect($data)->map(function($q,$i)use($tableName,&$columnList,&$j){
            $columnList[$j]['name'] = $q['label'];
            $columnList[$j]['value'] = $i;
            $j++;
            return $columnList;
        });
       
       return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('columnList')
        ]);
    }

    public function updateRule(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'module' => 'required|integer',
            'rule_query' => 'required',
            'rule_name' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $queryRule = QueryRule::find($id);
        if(!$queryRule){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Rules not found.'
            ]);
        }
        $this->storeRule($user->id,$user->organization_id,$user->company_id,$request->module,$request->rule_query,$request->rule_name,$request->group_by,$request->tree,$request->selected_column,$queryRule);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully your query rules is updated.'
        ]);
    }

    private function storeRule($userId,$organizationId,$companyId,$moduleName,$ruleQuery,$ruleName,$groupBy,$tree,$selectedColumn,$queryRuleObj = null){
        $queryRule = new QueryRule();
        if($queryRuleObj){
            $queryRule = $queryRuleObj; 
        }
        $queryRule->client_id = $organizationId;
        $queryRule->company_id = $companyId;
        $queryRule->user_id = $userId;
        $queryRule->module = $moduleName;
        $queryRule->rule_query = $ruleQuery;
        $queryRule->selected_column = $selectedColumn;
        $queryRule->rule_name = $ruleName;
        $queryRule->group_by = $groupBy;
        $queryRule->tree = $tree;
        $queryRule->save();
    }

    public function deleteRule($id){
        $user = JWTAuth::parseToken()->authenticate();
        $queryRule = QueryRule::find($id);
        if(!$queryRule){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Rules not found.'
            ]);
        }
        $queryRule->deleted_by = $user->id;
        $queryRule->deleted_at = date('Y-m-d H:i:s');
        $queryRule->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully your query rules is deleted.'
        ]);
    }
    
    // get column structure
    public function columnStructure($tableName)
    {
        $columns = Helper::getColumnStructure($tableName);
 
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('columns')
        ]);
    }

    // get summary report
    public function summaryReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required',
            'rule_query' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        try{
            $rulesQuery = $request->rule_query;
            $module = $request->module;
            $groupBy = $request->group_by;
            $limit = $request->size;
            $page = $request->page ? $request->page : 0;
            $page = $page == 0 ? 2 : $page;
            $offset = (($page - 1) * ($limit));
            $selectedColumn = $request->selected_column ? $request->selected_column : '*';
            $results = 'select '.$selectedColumn.' from '.$module.' where '.$rulesQuery;
            if($groupBy){
                $results .= ' GROUP BY '.$groupBy;
            }
            if($limit && !$groupBy){
                $results.= ' LIMIT '.$limit.' offset '.$offset;
            }
            $totalRecord = 'select COUNT(*) as count from '.$module.' where '.$rulesQuery;
            if($groupBy){
                $totalRecord .= ' GROUP BY '.$groupBy;
            }
            $current = $page;
            $results = DB::select($results);
            $totalRecord = DB::select($totalRecord);
            $totalRecord = $totalRecord[0]->count;
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('results', 'totalRecord', 'current')
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'FAIL',
                'message' => "Something went to wrong"
            ]);
        }

    }

    public function groupByColumn($tableName)
    {
        $columns = [];
        if($tableName == 'leads'){
            $columnList = ['Company'=>'company_id','Contact'=>'contact_id','Is Completed'=>'is_completed','City'=>'city','State'=>'state_id','Country'=>'country_id','Company Type'=>'company_type_id','Industry'=>'industry_id'];
        }else{
            $columnList = ['Company'=>'company_id','Category'=>'category_id','Sub Category'=>'sub_category','City'=>'city','State'=>'state_id','Country'=>'country_id','Company Type'=>'company_type_id','Industry'=>'industry_id'];
        }
        $i=0;
        foreach($columnList as $key=>$row){
            $columns[$i]['name'] = $key;
            $columns[$i]['value'] = $row;
            $i++;
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('columns')
        ]);
    }
}
