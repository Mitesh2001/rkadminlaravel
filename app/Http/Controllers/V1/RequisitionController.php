<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Requisition;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $paginationData = Helper::paginationData($request);
        $paginated = Requisition::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)
                    ->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $requisition = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('requisition', 'totalRecord', 'current')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'type' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $requisition = new Requisition();
        $requisition->client_id = $user->organization_id;
        $requisition->company_id = $user->company_id;
        $requisition->type = $request->type;
        $requisition->quantity = $request->quantity;
        $requisition->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'requisition has been sent successfully.'
        ]);
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
    public function edit($id)
    {
        //
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
}
