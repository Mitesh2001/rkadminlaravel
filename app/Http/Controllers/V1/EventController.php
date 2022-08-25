<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;
use Validator;
use JWTAuth;

class EventController extends Controller
{
    
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $events = Event::whereRaw("find_in_set('".$user->id."',user_id)")->get()->map(function($q){
                    if($q->type == 2){
                        $q->backgroundColor = 'black';
                        $q->borderColor = 'yellow';
                        $q->color = 'white';
                    }
                    return $q;
        });
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('events')
        ]);
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'start' => 'required',
            'end' => 'required',
            'title' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        // $userId = explode(',',$request->user_id);
        $data = [];
        $createdBy = $user->id;
        $event = new Event();
        $event->title = $request->title;
        $event->type = $request->type;
        $event->start = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $event->end = Carbon::parse($request->end)->format('Y-m-d H:i:s');
        $event->user_id = $request->user_id;
        $event->description = $request->description;
        $event->created_by = $createdBy;
        $event->created_at = date('Y-m-d H:i:s');
        $event->updated_at = date('Y-m-d H:i:s');
        $event->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Event has been created successfully.'
        ]);
    }

    public function update($id,Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'start' => 'required',
            'end' => 'required',
            'title' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $event = Event::find($id);
        if(!$event){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Not found.'
            ]);
        }
        $updatedBy = $user->id;
        $event->title = $request->title;
        $event->type = $request->type;
        $event->start = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $event->end = Carbon::parse($request->end)->format('Y-m-d H:i:s');
        $event->user_id = $request->user_id;
        $event->description = $request->description;
        $event->updated_by = $updatedBy;
        $event->updated_at = date('Y-m-d H:i:s');
        $event->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Event has been updated successfully.'
        ]);
    }

    public function show($id)
    {
        $event = Event::find($id);
        if(!$event){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Not found.'
            ]);
        }
        if($event->type == 2){
            $event->backgroundColor = 'black';
            $event->borderColor = 'yellow';
            $event->color = 'white';
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('event')
        ]);
    }

    public function delete($id)
    {
        $event = Event::find($id);
        if(!$event){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Not found.'
            ]);
        }
        $event->delete();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Event has been deleted successfully.'
        ]);
    }

}
