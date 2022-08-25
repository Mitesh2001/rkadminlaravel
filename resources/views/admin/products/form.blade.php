
			<div class="form-group row">
				{{Form::hidden('company_id',$companyId)}}
				
				<?php $product_type = (isset($product)?$product->product_type:$product_type);?>
					{!!
						Form::hidden('product_type',$product_type)
					!!}
			</div>
			
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('product_type', __('Product Type'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::select('product_type',['1'=>'Product','2'=>'Service'], 
						isset($data['product_type']) ? $data['product_type'] : null, 
						['class' => 'form-control','required','placeholder'=>'Please select product type']) 
					!!}
					<span class="form-text text-muted">Please select product type</span>
				</div>
				<div class="col-lg-6">
					{!! Form::label('name', __('Name'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('name',  
						isset($data['name']) ? $data['name'] : null, 
						['class' => 'form-control','required','maxlength'=>'50']) 
					!!}
					<span class="form-text text-muted">Please enter name</span>
				</div>
			</div>
			
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('skucode', __('SKU Code'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('skucode',  
						isset($data['skucode']) ? $data['skucode'] : null, 
						['class' => 'form-control','required', 'maxlength'=>'16','pattern'=>'^[A-Za-z0-9 ]*$']) 
					!!}
					<span class="form-text text-muted">Please enter sku code</span>
				</div>
				<div class="col-lg-6">
					{!! Form::label('category_id', __('Category'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::select('category_id',$productCategory, 
						isset($data['category_id']) ? $data['category_id'] : null, 
						['class' => 'form-control searchpicker','required','placeholder'=>'Please select category']) 
					!!}
					<span class="form-text text-muted">Please select category</span>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('listprice', __('Sell price'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('listprice',  
						isset($data['listprice']) ? $data['listprice'] : null, 
						['class' => 'form-control product-price dis-cal valid-price-number','required','maxlength'=>'6']) 
					!!}
					<span class="form-text text-muted">Please enter sell price</span>
				</div>
				<div class="col-lg-6">
					{!! Form::label('unit', __('Package unit'), ['class' => '']) !!}
					{!! 
						Form::select('unit',$unit, 
						isset($data['unit']) ? $data['unit'] : null, 
						['class' => 'form-control searchpicker','placeholder'=>'Please select package unit']) 
					!!}
					<span class="form-text text-muted">Please select package unit</span>
				</div>
			</div>
			
			<div class="form-group row">
				<div class="col-lg-6 row">
					<div class="col-md-9">
						{!! Form::label('image', __('Image'), ['class' => '']) !!}
						<div class="custom-file">
							{!! 
								Form::file('image', 
								['class' => 'custom-file-input']) 
							!!}
							<label class="custom-file-label" for="customFile">Choose Image</label>
						</div>
					</div>
					<div class="col-md-3">
						@if(!empty($product['image']))							
							<img src={{asset('storage/images/'.$product['image'])}} class="img-thumbnail" alt="" height="50px" width="100px">
						@endif			
					</div>					
				</div>
				<div class="col-lg-6">
					{!! Form::label('document', __('Document'), ['class' => '']) !!}					
					<div class="custom-file">
						{!! 
							Form::file('document', 
							['class' => 'custom-file-input']) 
						!!}
						<label class="custom-file-label" for="customFile">Choose Document</label>
					</div>
					@if(!empty($product['document']))
						<a href={{asset('storage/doc/'.$product['document'])}}>{{$product['document']}}</a>
					@endif
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('description', __('Description'), ['class' => '']) !!}
					{!! 
						Form::textarea('description',  
						isset($data['description']) ? $data['description'] : null, 
						['class' => 'form-control','id'=>'kt-ckeditor-1','rows'=>4]) 
					!!}
					<span class="form-text text-muted">Please enter description</span>
				</div>
				<div class="col-lg-6">
					{!! Form::label('comment', __('Additional info'), ['class' => '']) !!}
					{!! 
						Form::textarea('comment',  
						isset($data['comment']) ? $data['comment'] : null, 
						['class' => 'form-control','id'=>'kt-ckeditor-2','rows'=>4]) 
					!!}
					<span class="form-text text-muted">Please enter additional info</span>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('description', __('Offer Description'), ['class' => '']) !!}
					{!! 
						Form::textarea('description',  
						isset($data['description']) ? $data['description'] : null, 
						['class' => 'form-control','id'=>'kt-ckeditor-3','rows'=>4]) 
					!!}
					<span class="form-text text-muted">Please enter offer description</span>
				</div>
				<div class="col-lg-6">
					@php
						$offerDate = isset($product['offer_start_date_time']) ? Carbon::parse($product['offer_start_date_time'])->format('m/d/Y h:i A'). ' - ' .Carbon::parse($product['offer_end_date_time'])->format('m/d/Y h:i A'): null;
					@endphp

					<div class="col-lg-12 p-0">
						{!! Form::label('offer_date', __('Offer Date'), ['class' => '']) !!}
						<div class="input-group" id="offer_start_date_time">
							{{Form::text('offer_date',$offerDate,['class'=>'form-control','readonly','placeholder'=>'Please select offer date','required'])}}
							<div class="input-group-append">
								<span class="input-group-text">
									<i class="la la-calendar-check-o"></i>
								</span>
							</div>
						</div>
						<span class="form-text text-muted">Please select offer date</span>
					</div>
					<div class="col-lg-12 p-0">
						{!! Form::label('offer_price', __('Offer price'), ['class' => '']) !!}
						{!! 
							Form::text('offer_price',  
							isset($data['offer_price']) ? $data['offer_price'] : null, 
							['class' => 'form-control product-price dis-cal valid-price-number','maxlength'=>'6']) 
						!!}
						<span class="form-text text-muted">Please enter offer price</span>
					</div>
				</div>
			</div>
		
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitproduct']) !!}
					@php
						if($companyId){
							$url = url('rkadmin/products/'.encrypt($companyId));
						}else{
							$url = url('rkadmin/master/products/');
						}
					@endphp
					<a href="{{$url}}" class="btn btn-md btn-primary ml-2">Cancel</a>
				</div>
			</div>
		</div>
	
{{-- Scripts Section --}}
@section('scripts')
<script>
jQuery(document).on('change','#product_type',function(e){
	var type = $(this).val();
	if(type == 1){
		jQuery("#unit").prop('disabled',false)
	}else{
		jQuery("#unit").prop('disabled',true)
	}
});

$("#offer_start_date_time").daterangepicker(
{
	buttonClasses:" btn",
	applyClass:"btn-primary",
	cancelClass:"btn-secondary",
	timePicker:!0,
	timePickerIncrement:30,
	minDate:new Date(),
	locale:{
		format:"MM/DD/YYYY h:mm A"
	}
},(function(t,a,e)
{
		$("#offer_start_date_time .form-control").val(t.format("MM/DD/YYYY h:mm A")+" - "+a.format("MM/DD/YYYY h:mm A"))
}))
</script>
<!--begin::Page Vendors(used by this page)-->
<script src="{{ asset('plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.0.6') }}"></script>
<!--end::Page Vendors-->

<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('js/pages/crud/forms/editors/ckeditor-classic.js?v=7.0.6') }}"></script>
<script src="{{ asset('js/pages/crud/forms/widgets/bootstrap-daterangepicker.js?v=7.2.6') }}" type="text/javascript"></script>
@endsection
<!--end::Page Scripts-->
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection