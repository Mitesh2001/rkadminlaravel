<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cast;

class CastsController extends Controller
{   
    
    // get all casts  
    public function index()
    {
        $casts = Cast::where('status', 1)->orderBy('name', 'asc')->get();
        if ($casts->isEmpty()) {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Cast not found."
            ]);
        }
        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Cast get successfully.",
            'data' => compact('casts')
        ]);

    }

    // store the new cast if not exist in casts table
    public function store(Request $request)
    {
        $castObj = Cast::firstOrNew(array('name' => $request->name));
        $castObj->save();
        $data['id'] = $castObj->id;
        $data['name'] = $castObj->name;
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Cast has been stored successfully.',
            'data' => compact('data')
        ]);        
    }
}
