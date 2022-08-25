<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\ProductValue;
use App\Models\ProductField;
use App\Models\ProductSection;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;  
use Helper;

class ProductsController extends Controller
{
    public function getAllProducts()
    {
		$user = JWTAuth::parseToken()->authenticate();
        $products = Products::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->with('category')->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('products')
        ]);
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);

        $paginated = Products::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)
			->where(function($query1) use ($query) {
				$query1->where('skucode', 'LIKE', "%" . $query . "%")->orWhere('name', 'LIKE', "%" . $query . "%");
			})
            ->with('category')
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $products = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('products', 'totalRecord', 'current')
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
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'skucode' => 'string|nullable',
            'name' => 'required|string',
            'description' => 'string|nullable',
            'comment' => 'string|nullable',
            'listprice' => 'nullable|numeric',
            'tax1' => 'nullable|numeric',
            'tax2' => 'nullable|numeric',
            'tax3' => 'nullable|numeric',
            //'client_id' => 'nullable|integer',
            'product_type' => 'nullable|integer',
            'document' => 'nullable|max:4098',
            'picture' => 'nullable|max:4098',
            'category_id' => 'required'
        ]);

        if (isset($request->picture) && isset($request->picture['base64'])) {
            $picturePathinfo = pathinfo($request->picture['name']);
            if(!in_array($picturePathinfo['extension'], array('jpeg','jpg','png'))){
                $validation->getMessageBag()->add('picture', 'The picture must be a file of type: jpeg, jpg, png.');
            }
        }

        if (isset($request->document) && isset($request->document['base64'])) {
            $documentPathinfo = pathinfo($request->document['name']);
            if(!in_array($documentPathinfo['extension'], array('doc','docx','pdf','ppt','pptx','txt'))){
                $validation->getMessageBag()->add('document', 'The document must be a file of type: doc, docx, pdf, ppt, pptx, txt.');
            }
        }

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }
		
		$image = null;
		$documentName = null;
		if (isset($request->picture) && isset($request->picture['base64'])) {
            $image = Helper::createImageFromBase64($request->picture['base64']);
        }

        if (isset($request->document) && isset($request->document['base64'])) {
            $documentName = Helper::createDocFromBase64($request->document['base64'], $request->document['name']);
        }

        $product = Products::create([
			'client_id' => $user->organization_id,
			'company_id' => $user->company_id,
            'product_type' => (int)$request->product_type,
            'skucode' => $request->skucode,
            'name' => $request->name,
			'description' => $request->description,
            'comment' => $request->comment,
            'listprice' => (float)$request->listprice,
		    'unit' => $request->unit,
		    'category_id' => $request->category_id,
            'image' => $image,
            'document' => $documentName,
            'tax1' => (float)$request->tax1,
            'tax2' => (float)$request->tax2,
            'tax3' => (float)$request->tax3,
			'created_by' => $user->id,
			'offer_description' => $request->offer_description,
			'offer_start_date_time' => $request->offer_start_date_time ? Carbon::parse($request->offer_start_date_time)->format('Y-m-d H:i:s') : null,
			'offer_end_date_time' => $request->offer_end_date_time ? Carbon::parse($request->offer_end_date_time)->format('Y-m-d H:i:s') : null,
        ]);
		
		$sectionIds = ProductSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
        $fieldData = ProductField::whereIn('section_id',$sectionIds)->get();
		if(count($fieldData) != 0)
        {
			$valueData = array();
			foreach($fieldData as $key=>$row)
			{
				$valueData[$key]['name'] = $row->label_name;
				$valueData[$key]['value'] = json_encode($request->get($row->label_name));
				$valueData[$key]['field_id'] = $row->id;
				$valueData[$key]['client_id'] = $user->organization_id;
				$valueData[$key]['company_id'] = $user->company_id;
				$valueData[$key]['created_by'] = $user->id;
				$valueData[$key]['product_id'] = $product->id;
				$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
				$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
			}
			if(!empty($valueData)){
				/* $productId = $product->id;
				$valueData = array_map(function($q) use($productId){
					$q['product_id'] = $productId;
					return $q;
				}, $valueData); */
				$fieldModelData = ProductValue::insert($valueData);
			}
		}

        //Add Action Log
        Helper::addActionLog($user->id, 'PRODUCT', $product->id, 'CREATEPRODUCT', [], $product->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Product has been created successfully.'
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
		$user = JWTAuth::parseToken()->authenticate();
        $product = Products::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->with('category')->find($id);
        //$product->offer_description = ($product->offer_end_date_time && $product->offer_end_date_time > date('Y-m-d H:i:s')) ? $product->offer_description : null;
        if ($product) {
			$fieldData = collect(ProductValue::where('product_id',$product->id)->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get())->map(function($q) use($product){
				$product[$q->name] = json_decode($q->value);
				return $product;
			});
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('product')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Product not found."
            ]);
        }
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
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'skucode' => 'string|nullable',
            'name' => 'required|string',
            'description' => 'string|nullable',
            'comment' => 'string|nullable',
            'listprice' => 'nullable|numeric',
            'tax1' => 'nullable|numeric',
            'tax2' => 'nullable|numeric',
            'tax3' => 'nullable|numeric',
            //'client_id' => 'nullable|numeric',
            'product_type' => 'nullable|numeric',
            'document' => 'nullable|max:4098',
            'picture' => 'nullable|max:4098',
            'category_id' => 'required'
        ]);
        
        if (isset($request->picture) && isset($request->picture['base64'])) {
            $pathinfo = pathinfo($request->picture['name']);
            if(!in_array($pathinfo['extension'], array('jpeg','jpg','png'))){
                $validation->getMessageBag()->add('picture', 'The picture must be a file of type: jpeg, jpg, png.');
            }
        }

        if (isset($request->document) && isset($request->document['base64'])) {
            $documentPathinfo = pathinfo($request->document['name']);
            if(!in_array($documentPathinfo['extension'], array('doc','docx','pdf','ppt','pptx','txt'))){
                $validation->getMessageBag()->add('document', 'The document must be a file of type: doc, docx, pdf, ppt, pptx, txt.');
            }
        }

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }
		
        $product = Products::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
		
        if ($product) {
			$image = $product->image;
			$documentName = $product->document;
			 if (isset($request->picture) && isset($request->picture['base64'])) {
				$image = Helper::createImageFromBase64($request->picture['base64']);
			}

            if(isset($request->picture) and $request->picture == 'delete'){
                $image = '';
            }
			
            if (isset($request->document) && isset($request->document['base64'])) {
                $documentName = Helper::createDocFromBase64($request->document['base64'], $request->document['name']);
            }

            if(isset($request->document) and $request->document == 'delete'){
                $documentName = '';
            }

            $oldData = $product->toArray();
            $product->update([
				'product_type' => (int)$request->product_type,
				'skucode' => $request->skucode,
				'name' => $request->name,
				'description' => $request->description,
				'comment' => $request->comment,
				'listprice' => (float)$request->listprice,
				'image' => $image,
                'unit' => $request->unit,
                'category_id' => $request->category_id,
				'document' => $documentName,
				'tax1' => (float)$request->tax1,
				'tax2' => (float)$request->tax2,
				'tax3' => (float)$request->tax3,
                'offer_description' => $request->offer_description,
			'offer_start_date_time' => $request->offer_start_date_time ? Carbon::parse($request->offer_start_date_time)->format('Y-m-d H:i:s') : null,
			'offer_end_date_time' => $request->offer_end_date_time ? Carbon::parse($request->offer_end_date_time)->format('Y-m-d H:i:s') : null,
            ]);
			
			$sectionIds = ProductSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
			$fieldData = ProductField::whereIn('section_id',$sectionIds)->get();
			if(count($fieldData) != 0)
			{
				ProductValue::where('product_id',$product->id)->delete();
				foreach($fieldData as $key=>$row)
				{
					$valueData[$key]['name'] = $row->label_name;
					$valueData[$key]['value'] = json_encode($request->get($row->label_name));
					$valueData[$key]['field_id'] = $row->id;
					$valueData[$key]['client_id'] = $user->organization_id;
					$valueData[$key]['company_id'] = $user->company_id;
					$valueData[$key]['product_id'] = $product->id;
					$valueData[$key]['created_by'] = $user->id;
					$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
					$valueData[$key]['updated_by'] = $user->id;
					$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
				}
				if(!empty($valueData)){
					/* $productId = $product->id;
					$valueData = array_map(function($q) use($productId){
						$q['product_id'] = $productId;
						return $q;
					}, $valueData); */
					$fieldModelData = ProductValue::insert($valueData);
				}
			}

            //Add Action Log
            Helper::addActionLog($user->id, 'PRODUCT', $product->id, 'UPDATEPRODUCT', $oldData, $product->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Product has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Product not found."
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $product = Products::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
        if ($product) {
            $product->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'PRODUCT', $product->id, 'UPDATEPRODUCT', [], []);
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Product has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    // get product category
    public function getProductCategory()
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $productsCategory = ProductCategory::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get(['id','name']);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('productsCategory')
        ]);
    }

    public function addProductCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>  'required|unique:product_categories,name',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();

        $productCategory = new ProductCategory();
        $productCategory->name = $request->name;
        $productCategory->client_id = $user->organization_id;
        $productCategory->company_id = $user->company_id;
        $productCategory->created_by = $user->id;
        $productCategory->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Product category has been added successfully.'
        ]);
    }
}
