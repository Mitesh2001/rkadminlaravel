<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConstructionContacts;
use App\Models\TeleCallerContactNote;
use App\Models\TeleCallerContact;
use App\Models\Contacts;
use App\Models\User;
use Validator;
use JWTAuth;
use Helper;
use DB;

class TeleCallerContactController extends Controller
{
     /**
     * Get telecaller assign or unassign contacts 
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $paginationData = Helper::paginationData($request);
        /* $cast = $request->cast;
        $houseType = $request->house_type; */
        $teleCallerId = $request->tele_caller_id;
		$user_id = $request->user_id;
        //$flat = $request->flat;
        $roleId = $request->role_id;$company_type_id = $request->company_type_id;
        $searchTxt = $request->searchTxt;$industry_id = $request->industry_id;$country_id = $request->country_id;
		$state_id = $request->state_id;$city = $request->city;$postcode = $request->postcode;
        $paginated = Contacts::select(DB::raw("*, DATE_FORMAT(created_at,'%d-%m-%Y') as date"))->where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id);
		
		if($searchTxt && $searchTxt!=''){
			$paginated = $paginated->where(function($q) use ($searchTxt){
				$q->where('name','like',"%{$searchTxt}%");
				$q->orWhere('company_name','like',"%{$searchTxt}%");
				$q->orWhere('email','like',"%{$searchTxt}%");
				$q->orWhere('secondary_email','like',"%{$searchTxt}%");
				$q->orWhere('mobile_no','like',"%{$searchTxt}%");
				$q->orWhere('secondary_mobile_no','like',"%{$searchTxt}%");
				$q->orWhere('address_line_1','like',"%{$searchTxt}%");
				$q->orWhere('address_line_2','like',"%{$searchTxt}%");
				$q->orWhere('sticky_note','like',"%{$searchTxt}%");
				$q->orWhere('notes','like',"%{$searchTxt}%");
				$q->orWhere('special_instructions','like',"%{$searchTxt}%");
			});
		}
		if($company_type_id){
			$paginated = $paginated->where('company_type_id',$company_type_id);
		}
		if($industry_id){
			$paginated = $paginated->where('industry_id',$industry_id);
		}
		if($country_id){
			$paginated = $paginated->where('country_id',$country_id);
		}
		if($state_id){
			$paginated = $paginated->where('state_id',$state_id);
		}
		if($city){
			$paginated = $paginated->where('city',$city);
		}
		if($postcode){
			$paginated = $paginated->where('postcode',$postcode);
		}
		
        /* if($cast){
            $paginated = $paginated->where('cast',$cast);
        }
        if($houseType){
            $paginated = $paginated->where('house_type',$houseType);
        }
        if($flat){
            $paginated = $paginated->where('flat_selection',$flat);
        } */
        if($roleId){
            $contactId = TeleCallerContact::where('role_id',$roleId)->pluck('contact_id','contact_id')->toArray();
            $paginated = $paginated->whereIn('id',$contactId);
        }
        
        $paginated = $paginated->with(['getAssignContact' => function ($query) use($user,$teleCallerId){
                        $query->where('user_id', $teleCallerId);
                    }])->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);  
        $contacts = $paginated->getCollection()->transform(function ($q) use($teleCallerId){
            $userIds = !empty($q->getAssignContact) ? $q->getAssignContact->pluck('user_id')->toArray() : [];
            $q->is_assign = (!empty($userIds) && in_array($teleCallerId,$userIds)) ? 1 : 0;
            unset($q->getAssignContact);
            return $q;
        });
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('contacts', 'totalRecord', 'current')
        ]);
    }

    /**
     * assign contact to telecaller
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required',
            // 'tele_caller_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $request->tele_caller_id;
        $roleId = $request->role_id ? $request->role_id : 0;
        $hiddenids = $request->hiddenIds;
        if($hiddenids){
            $hiddenids = explode(',',$hiddenids);
            if(is_array($hiddenids))
            {
                TeleCallerContact::whereIn('contact_id',$hiddenids)->delete();
            }
        }
        $contactId = explode(',',$request->contact_id);
		$oldqorking =  TeleCallerContact::whereIn('contact_id',$contactId)->where('user_id','=',$user->id)->update(['is_working'=>0, 'unlocked_by'=>$user->id, 'unlocked_date'=>date('Y-m-d H:i:s')]);
        $contactsData = [];
        $contactsData = array_map(function ($q) use ($contactId,$contactsData,$userId,$roleId,$user) {
            $contactsData[$q]['contact_id'] = $q;
            $contactsData[$q]['user_id'] = $userId;
            $contactsData[$q]['role_id'] = $roleId; 
            $contactsData[$q]['is_working'] = 0;
            //$contactsData[$q]['is_working'] = 1;
            $contactsData[$q]['created_by'] = $user->id;
            $contactsData[$q]['created_at'] = date('Y-m-d H:i:s');
            $contactsData[$q]['updated_at'] = date('Y-m-d H:i:s');
            return $contactsData;
        }, $contactId);
        $contactsData = \Arr::collapse($contactsData);
        TeleCallerContact::insert($contactsData);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully contact has been assigned to telecaller.'
        ]);
    }

    /**
     * remove contact form telecaller
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $request->tele_caller_id;
        $contactId = explode(',',$request->contact_id);
        TeleCallerContact::whereIn('contact_id',$contactId)->where('user_id',$userId)->delete();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully contact has been removed from telecaller.'
        ]);
    }

    /**
     * when tele caller working status change
     *
     * @param Request $request
     * @return void
     */
    public function workingStatus(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required',
            'is_lock' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $teleContact = TeleCallerContact::where('contact_id',$request->contact_id)->where('user_id',$user->id)->first();
        $role = $user->roles->first();
        $checkTelePermission = app('App\Http\Controllers\V1\ContactsController')->checkPermissionRule($role->id,'Contact');
        if(!$teleContact && $checkTelePermission){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Contact not found.'
            ]);
        }
        if(!$checkTelePermission){
            $teleContact = TeleCallerContact::where('contact_id',$request->contact_id)->orderBy('id','DESC')->first();
            if(!$teleContact){
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'Contact not found.'
                ]);
            }
        }
        $msg = 'unlock';
        $status = 0;
        if($request->is_lock == 'true')
        {
            $checkTeleContact = TeleCallerContact::where('contact_id',$request->contact_id)->where('is_working',1)->first();
            
            if($checkTeleContact){
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'This contact is already locked.'
                ]);
            }
            $msg = 'lock';
            $status = 1;
            $teleContact->locked_date = date('Y-m-d H:i:s');
        }else{
            $teleContact->unlocked_date = date('Y-m-d H:i:s');
            $teleContact->unlocked_by = $user->id;
            $teleContact->note = $request->note;
        }
        //$teleContact->created_by = $user->id;
        $teleContact->is_working = $status;
        $teleContact->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Your contact is successfully '.$msg
        ]);
    
    }

    /**
     * Store tele caller note for contact
     *
     * @param Request $request
     * @return void
     */
    public function addNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $teleContactNote = new TeleCallerContactNote();
        $teleContactNote->contact_id = $request->contact_id;
        $teleContactNote->user_id = $user->id;
        $teleContactNote->note = $request->note;
        $teleContactNote->is_sticky_note = $request->is_sticky_note ? $request->is_sticky_note : 0;
        $teleContactNote->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully note added in contact.'
        ]);
    }

    public function getNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $teleContactNote = TeleCallerContactNote::with('get_user')->where('contact_id',$request->contact_id)->get(['id','contact_id','user_id','note']);
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('teleContactNote')
        ]);
    }

    // get all tele caller user
    public function getTeleCallerUser()
    {
        $users = User::whereHas('roles', function ($query){
            $query->where('id', 10);
        })->get();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('users')
        ]);
    }
    
}
