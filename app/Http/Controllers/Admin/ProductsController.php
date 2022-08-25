<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use DB;
use Helper;
use JsValidator;
use Validator;
use App\Models\Client;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\Company;
use Carbon\Carbon;
use Exception;
use Session;
use URL;
use Auth;

class ProductsController extends Controller
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
    public function currentUser(){
        return Auth::user();
    }

    public function index($companyId='', Request $request)//$module='', $companyId='', Request $request
    {
        try{
            $categoryId = $request->category_id;
            $companyData = null;
            $productCategory = ProductCategory::query();

            if($companyId)
            {
				$companyId = decrypt($companyId);
				$companyData = Helper::getCompany($companyId);
				$productCategory = $productCategory->where('client_id','=',$companyData->client_id)->where('company_id','=',$companyData->id);
            }else{
				 $productCategory = $productCategory->where('client_id','=',0)->where('company_id','=',0);
			}
            $productCategory = $productCategory->pluck('name','id')->toArray();
            $product_type = 1;
            return view('admin.products.index',compact('product_type','companyId','companyData','categoryId','productCategory'));
        }catch(Exception $e){
            abort(404);
        }
    }
	
	
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {	
        $products = Products::where('company_id',$request->company_id)->select(['id', 'product_type', 'name', 'skucode','unit','category_id','listprice','offer_price', 'client_id'])->with(['client']);
        
        if($request->category_id){
            $products->where('category_id',$request->category_id);
        }

        if($request->product_type!=0){
            $products->where('product_type',$request->product_type);
        }

        $products = $products->orderBy('id', 'desc')->get();
        
        return Datatables::of($products, $request)
			->addColumn('assign', function ($products) {
                return '<input type="hidden" value="'.$products->id.'" name="productids[]" /><input type="checkbox" value="'.$products->id.'" name="assignproductids[]" class="assignproductids" id="assignproductids_'.$products->id.'"/>';
            })
            ->addColumn('namelink', function ($products) {
                return $products->name;
            })
            ->addColumn('product_type', function ($products) {
				if($products->product_type)
                return ($products->product_type == '1')?'Product':'Service';
				else
				return NULL;
            })
            ->addColumn('unit', function ($products) {
                
                $unitList = Helper::unitList();
                if(array_key_exists($products->unit, $unitList)){
                    return $unitList[$products->unit];
                }else{
                    return null;
                }
				
            })
            ->addColumn('listprice', function ($products) {
				return number_format($products->listprice,2,'.','');
            })
            ->addColumn('offer_price', function ($products) {
				return number_format($products->offer_price,2,'.','');
            })
            ->addColumn('category', function ($products) {
				return !empty($products->category->name) ? $products->category->name : null;
            })
            ->addColumn('action', function ($products) use($request) {
				
                $html = $edit_btn = $delete = '';

               $pid = encrypt($products->id);
                if($request->company_id){
                    $edit_btn = '<a href="'.(($products->product_type == 1 || !$products->product_type)?route('admin.products.edit',$pid):route('admin.services.edit',$pid)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';
                    
                }else{
                    $edit_btn = '<a href="'.(($products->product_type == 1 || !$products->product_type)?route('admin.master.products.edit',$pid):route('admin.master.services.edit',$pid)).'"  class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';   
                }
                $html .= $edit_btn;
            
                $delete = '<a class="btn btn-link product-delete" data-toggle="tooltip" title="Delete" data-id='.$products->id.'><i class="flaticon2-trash text-danger"></i></a>';
              
                $html .= $delete;
                return $html;
            })
            ->rawColumns(['assign','product_type', 'namelink','skucode','category', 'action'])
            ->make(true);
    }
	
	 /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create($companyId='')//$module='',$companyId=''
    {
        try{
            $companyData = null;
            if($companyId)
            {
                $companyId = decrypt($companyId);
                $companyData = Helper::getCompany($companyId);
            }
            $product_type = null;
            //$clients = Client::pluck('name', 'id');
            $clients = Company::pluck('company_name', 'id');
            $productCategory = ProductCategory::query();
            if($companyData)
            {
                $productCategory = $productCategory->where('client_id','=',$companyData->client_id)->where('company_id','=',$companyData->id);
            }else{
				$productCategory = $productCategory->where('client_id','=',0)->where('company_id','=',0);
			}
            $productCategory = $productCategory->pluck('name','id')->toArray();
            $clients->prepend('Please Select client', '');
            $unit = Helper::unitList(null);
            return view('admin.products.create')
                ->withClients($clients)->with('product_type',$product_type)->withCompanyId($companyId)->withCompanyData($companyData)->withProductCategory($productCategory)->withUnit($unit);
        }catch(Exception $e){
            abort(404);
        }
    }
	/**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
		
		return $this->post_process('add', 0, $request);
    }
	
	private function post_process($action, $id, $request)
    {
        $validation = Validator::make($request->all(), [
            'product_type' => 'required|integer',           
            'skucode' => 'nullable|regex:/^[A-Za-z0-9 ]+$/u|max:16',//required|
            'name' => 'required|string|max:50',           
            'description' => 'string|nullable',
            'comment' => 'string|nullable',
            'category_id' => 'required|integer',
            'listprice' => 'nullable|regex:/^[0-9.]+$/u',//required
            'offer_price' => 'nullable|regex:/^[0-9.]+$/u'
        ],
        [
            'listprice.required' => 'Please enter sell price.']
        );

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
		$pstitle = 'Product / Service';
		
		$image = '';
        $offerDate = $request->offer_date;
        $offerStartDateTime = null;
        $offerEndDateTime = null;
        if($offerDate){
            $offerDate = explode('-',$request->offer_date);
            $offerStartDateTime = Carbon::parse($offerDate[0])->format('Y-m-d H:i:s');
            $offerEndDateTime = Carbon::parse($offerDate[1])->format('Y-m-d H:i:s');
        }
		$document = '';
        if ($action == 'add') {
            $product = new Products;
			$message = $pstitle.' has been added successfully';
        } else {
            $product = Products::findOrFail($id);
			$message = $pstitle.' has been updated successfully';
			$image = $product->image;
			$document = $product->document;
        }
		if ($request->hasFile('image')) {
			 if ($request->file('image')->isValid()) {
				 $validated = $request->validate([
                    'name' => 'string|max:40',
                    'image' => 'mimes:jpeg,jpg,png|max:4098',
                ]);
				 $extension = $request->image->extension();
				 $imagePath = $request->file('image');
				 $imageName = $imagePath->getClientOriginalName();
				 $imageName = time().'.'.$extension;//.'.'.$request->image->extension();  
				 $productimage= $imageName;
				 $imageName = $request->image->move(public_path('/storage/images'), $imageName);
				 $image = $productimage;
			 }
		}
		if ($request->hasFile('document')) {
			 if ($request->file('document')->isValid()) {
				 $validated = $request->validate([
                    'document' => 'mimes:doc,docx,pdf,ppt,pptx,txt|max:4098',
                ]);
				 $extension = $request->document->extension();
				 $imagePath = $request->file('document');
				 $imageName = $imagePath->getClientOriginalName();
				 $imageName = date('YmdHisu').'.'.$extension;//.'.'.$request->image->extension();  
				 $documentImage= $imageName;
				 $imageName = $request->document->move(public_path('/storage/doc'), $imageName);
				 $document = $documentImage;
			 }
		}
		$companyData = Company::find($request->company_id);
        if($companyData){
            $product->company_id = $companyData->id;
            $product->client_id = $companyData->client_id;
        }
		$product->product_type = $request->product_type;
		$product->skucode = $request->skucode;
		$product->name  = $request->name;
		$product->description  = $request->description;
		$product->comment  = $request->comment;
		$product->unit  = $request->unit;
		$product->category_id  = $request->category_id;
        $product->listprice  = $request->listprice;
        $product->offer_price  = ($request->offer_price) ? $request->offer_price : '0.00';
		$product->image  = $image;
		$product->document  = $document;
		$product->offer_description  = $request->offer_description;
		$product->offer_start_date_time  = $offerStartDateTime;
		$product->offer_end_date_time  = $offerEndDateTime;
		
		if ($action == 'add')
		$product->created_by = auth()->guard('admin')->user()->id;
        $product->save();
		
		$redirect = route('admin.products.edit',['id'=>$product->id]);
        
        if(isset($companyData->id)){
            $redirect_url = 'rkadmin/products/'.encrypt($companyData->id);
        }else{
            $redirect_url = 'rkadmin/master/products';
        }

        return redirect($redirect_url)->with('success', $message);
    }
	
	
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function show($module='',$id)
    {
		$product_type = null;
		$product = Products::with('industry_type','company_type')->find($id);//,'state','country'
		//dd($product->appointments);
        return view('admin.products.show')
            ->withProducts($product)
            ->withCompanyname(Setting::first()->company)
            ->withInvoices($this->getInvoices($product))
            ->withUsers(User::with('department')->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->with('filesystem_integration', Integration::whereApiType('file')->first())
            ->with('documents', $product->documents()->where('integration_type', get_class(GetStorageProvider::getStorage()))->get())
            ->with('lead_statuses', Status::typeOfLead()->get())
            ->with('task_statuses', Status::typeOfTask()->get())
            ->withRecentAppointments($product->appointments()->orderBy('start_at', 'desc')->where('end_at', '>', now()->subMonths(3))->limit(7)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function edit($id)
    {
		$pcid = decrypt($id);
        $product = Products::find($pcid);
		$clients = Company::pluck('company_name', 'id');
        $clients->prepend('Please Select client', '');
       
        $productCategory = ProductCategory::query();
        if($product->company_id)
        {
            $productCategory = $productCategory->where('client_id',$product->client_id)->where('company_id',$product->company_id);
        }else{
				$productCategory = $productCategory->where('client_id','=',0)->where('company_id','=',0);
			}
        $productCategory = $productCategory->pluck('name','id')->toArray();

        $unit = Helper::unitList(null);

        return view('admin.products.edit')
            ->withProduct($product)
            ->withClients($clients)
            ->withUnit($unit)
            ->withCompanyId($product->company_id)
            ->withProductCategory($productCategory)
			->with('product_type',$product->product_type);
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
        $id = $request->id;
		$pcid = decrypt($id);
		$product = Products::findOrFail($pcid);$type='';
		if($product->product_type == 1){
			if($product->company_id)
			$redirect = route('admin.products.index',encrypt($product->company_id));
			else
			$redirect = route('admin.master.products.index');
		$type='Product';
		}
		else{
			if($product->company_id)
			$redirect = route('admin.services.index',encrypt($product->company_id));
			else
			$redirect = route('admin.master.services.index');
		$type='Service';
		}
		$product->delete();
        Session::flash('success','Your '.$type.' is deleted successfully');
        return true;
    }
	
	public function assignProduct(Request $request){
		$company_id = $request->company_id;
		$companyData = Company::find($request->company_id);
        if($companyData){
		$assignids = explode(',',$request->assignids);
		foreach($assignids as $pid){
			$prod = Products::with('category')->findOrFail($pid);
			$product = new Products;
			if(isset($prod->category)){
			$cat = ProductCategory::where('name',$prod->category->name)->where('client_id','=',$companyData->client_id)->where('company_id','=',$companyData->id)->latest()->first();
				if($cat){
					$product->category_id  = $cat->id;
				}else{
					$cat = ProductCategory::where('name',$prod->category->name)->where('client_id','=',0)->where('company_id','=',0)->latest()->first();
					$newcat = new ProductCategory;
					$newcat->name = $cat->name;
					$newcat->client_id = $companyData->client_id;
					$newcat->company_id = $companyData->id;
					$newcat->category_description = $cat->category_description;
					$newcat->created_by = Auth::user()->id;
					$newcat->save();
					$product->category_id  = $newcat->id;
				}
			}
			
			$product->company_id = $companyData->id;
            $product->client_id = $companyData->client_id;
			$product->product_type = $prod->product_type;
			$product->skucode = $prod->skucode;
			$product->name  = $prod->name;
			$product->description  = $prod->description;
			$product->comment  = $prod->comment;
			$product->unit  = $prod->unit;
			$product->listprice  = $prod->listprice;
			$product->offer_price  = ($prod->offer_price) ? $prod->offer_price : '0.00';
			$product->image  = $prod->image;
			$product->document  = $prod->document;
			$product->offer_description  = $prod->offer_description;
			$product->offer_start_date_time  = $prod->offer_start_date_time;
			$product->offer_end_date_time  = $prod->offer_end_date_time;
			$product->created_by = auth()->guard('admin')->user()->id;
			$product->save();
			//$data['products'][]=$product;
		}
		}
		$data['success']=true;
		return response()->json($data);
	}
}
