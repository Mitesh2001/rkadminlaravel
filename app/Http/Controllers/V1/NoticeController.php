<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NoticeBoard;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);

        $paginated = NoticeBoard::where('user_id', $user->id)->where('company_id',$user->company_id)
		->where(function($query1) use ($query) {
			$query1->where('notice', 'LIKE', "%" . $query . "%")->orWhere('description', 'LIKE', "%" . $query . "%");
		})
		->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $notice = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('notice', 'totalRecord', 'current')
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
