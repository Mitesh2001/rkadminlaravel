<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Validator;
use Helper;
use Exception;
use Auth;
use Session;
use App\Models\Products;

class ProductCategoryController extends Controller
{
    public function __construct(){      
        $this->middleware(function ($request, $next) { 
            if(auth()->user()->type == 3 || auth()->user()->type == 4)
            {   
                $getRequestUri = $request->getRequestUri();               
                $contains = Str::contains($getRequestUri, 'master');
                if($contains === true){
                    return redirect('/rkadmin')->with('success','You are not authorized to access that page.');
                }
            }
            return $next($request);
        });
    }
    public function index($companyId='', Request $request){
        try{
            $productCategory = null;$companyData = null;
			if($companyId)
            {
            $companyId = decrypt($companyId);($request->company_id);
            $companyData = Helper::getCompany($companyId);
			}
            return view('admin.product_category.index',compact('companyData','companyId','productCategory'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    public function anyData(Request $request){
        $companyId = $request->company_id;
		if(!$companyId)
			$companyId = 0;
        $products = ProductCategory::select(['id', 'name', 'created_by', 'created_at','company_id'])->where('company_id',$companyId);
        $products = $products->orderBy('id', 'desc')->get();
        
        return Datatables::of($products,$companyId)
            ->addColumn('created_by', function ($products) {
                return !empty($products->getCreatedBy->name) ? $products->getCreatedBy->name.' - '.$products->created_at->format('Y-m-d') : null; 
            })
            ->addColumn('action', function ($products) use($companyId) {
                
                $count = Products::where('category_id',$products->id)->where('company_id',$companyId)->count();
                
                $html = $edit_btn = $count_btn = '';
                if($companyId)
					$edit_btn = '<a href="'.route('admin.category.edit',encrypt($products->id)).'" data-toggle="tooltip" title="Edit" class="btn btn-link" ><i class="flaticon2-pen text-success"></i></a>';
				else
					$edit_btn = '<a href="'.route('admin.master.category.edit',encrypt($products->id)).'" data-toggle="tooltip" title="Edit" class="btn btn-link" ><i class="flaticon2-pen text-success"></i></a>';
				
                $html .= $edit_btn;        
            
                $delete_btn = '<a class="btn btn-link product-category-delete" data-toggle="tooltip" title="Delete" data-id='.encrypt($products->id).'><i class="flaticon2-trash text-danger"></i></a>';
                $html .= $delete_btn;        
            
                if($count){
                    $count_btn = '<a href="'.route('admin.products.index',encrypt($products->company_id)).'?category_id='.$products->id.'" data-toggle="tooltip" title="View Product" class="btn btn-sm btn-success" target="_blabk">'.$count.'</a>';
                }
                $html .= $count_btn;        
                    
                return $html;
            })
            ->rawColumns(['created_by','action'])
            ->make(true);
    }


    public function create($companyId='',Request $request){
        try{
			$cId = '';$companyData = '';
			if($companyId)
            {
            $cId = decrypt($companyId);
            $companyData = Helper::getCompany($cId);
			}
            $productCategory = null;
            return view('admin.product_category.create',compact('cId','companyData','productCategory'));
        }catch(Exception $e){
            abort(404);
        }
    }

    public function store(Request $request)
    {
		return $this->post_process('add', 0, $request);
    }
	
	private function post_process($action, $id, $request)
    {
		$id = null;
		if ($action == 'add') {
        $productCategory = new ProductCategory();
		$message = 'Category has been added successfully';
		}else{
			 if($request->id){
				$id = decrypt($request->id);
				$productCategory = ProductCategory::findOrFail($id);
				$message = 'Category has been updated successfully';
			}
		}
       
        $validation = Validator::make($request->all(), [
            'name' => 'string|regex:/^[A-Za-z0-9- ]+$/u||max:50',//|unique:product_categories,name,'.$id			
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        
        $productCategory->name = $request->name;
        $companyData = Helper::getCompany($request->company_id);
		if($companyData){
        $productCategory->client_id = $companyData->client_id;
        $productCategory->company_id = $companyData->id;
		}
        $productCategory->category_description = $request->category_description;
		if ($action == 'add')
        $productCategory->created_by = Auth::user()->id;
        $productCategory->save();
		if(isset($companyData->id)){
            $redirect_url = 'rkadmin/product-category/'.encrypt($companyData->id);
        }else{
            $redirect_url = 'rkadmin/master/product-category';
        }

        //return redirect('rkadmin/product-category?company_id='.encrypt($companyData->id))->with('success','Your product category is successfully'.$message);
		return redirect($redirect_url)->with('success', $message);
	}

    public function edit($id)
    {
        try{
            $pcid = decrypt($id);
            $productCategory = ProductCategory::find($pcid);
            $companyData = Helper::getCompany($productCategory->company_id);
            return view('admin.product_category.edit',compact('id','productCategory','companyData'));
        }catch(Exception $e){
            abort(404);
        }
    }
	
	/**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
		return $this->post_process('update', $id, $request);
    }
	
	/**
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request)
    {
        try{
			$id = $request->id;
            $pcid = decrypt($id);
            $productCategory = ProductCategory::find($pcid);
            if($productCategory){
				
                $productCategory->delete();
            }
            Session::flash('success','Your product is deleted successfully');
            return true;
        }catch(Exception $e){
            abort(404);
        }
    }
}
