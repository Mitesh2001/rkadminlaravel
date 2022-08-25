{{-- Extends layout --}}
@extends('admin.layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon-list-1 text-primary"></i>
	</span>
    <h3 class="form_title">Edit Category : <span> ({{$productCategory->name}}) 
       @if(isset($productCategory->client)) | Client Name : {{$productCategory->client['name']}} | @endif  @if(isset($productCategory->company_name)) Company Name : {{$productCategory->company_name['company_name']}} @endif
    </span></h3>
</div>
</div>
<div class="card-body">
 @include('admin.layouts.alert')
    {!! Form::model($productCategory, [
            'method' => 'PATCH',
            'route' => ['admin.category.update', $productCategory->id],
			'class' => 'ui-form',
			'enctype' => 'multipart/form-data'
            ]) !!}
    @include('admin.product_category.form', ['submitButtonText' => __('Update')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->
@stop