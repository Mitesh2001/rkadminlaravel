{{-- Extends layout --}}
@extends('admin.layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon-list-1 text-primary"></i>
	</span>
    <h3 class="form_title">Edit Product / Service : <span> ({{$product->name}}) 
       @if(isset($product->client)) | Client Name : {{$product->client['name']}} | @endif  @if(isset($product->company_name)) Company Name : {{$product->company_name['company_name']}} @endif
    </span></h3>
</div>
</div>
<div class="card-body">
 @include('admin.layouts.alert')
    {!! Form::model($product, [
            'method' => 'PATCH',
            'route' => ['admin.products.update', $product->id],
			'class' => 'ui-form',
			'enctype' => 'multipart/form-data'
            ]) !!}
    @include('admin.products.form', ['submitButtonText' => __('Update')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->
@stop