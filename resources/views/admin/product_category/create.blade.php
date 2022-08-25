@extends('admin.layouts.master')
@section('content')

<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon-list-1 text-primary"></i>
	</span>
	<h3 class="form_title">
    @if($productCategory)
        Update Product Category
	@else
        Create Product Category
    @endif
    <span>
        @if(!empty($companyData))
            | Client Name : {{$companyData->client_data['name']}} | Company Name : {{$companyData->company_name}}
        @endif
    </span>
    </h3>
</div>
</div>
<div class="card-body">

 @include('admin.layouts.alert')
 
    {!! Form::open([
            'route' => 'admin.category.store',
            'class' => 'ui-form',
			'enctype' => 'multipart/form-data'
            ]) !!}
    @include('admin.product_category.form', ['submitButtonText' => __('Create New ')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->
@stop
