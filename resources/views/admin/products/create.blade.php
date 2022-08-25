@extends('admin.layouts.master')
@section('content')

    <?php
    $data = Session::get('data');
    ?>

<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon-list-1 text-primary"></i>
	</span>
	<h3 class="form_title">{{ __('Create Product / Service') }} 
        <span>
        @if(!empty($companyData))
            &nbsp; | Client Name : {{$companyData->client_data['name']}} &nbsp; | Company Name : {{$companyData->company_name}}
        @endif
    </span>
    </h3>
</div>
</div>
<div class="card-body">

 @include('admin.layouts.alert')
 
    {!! Form::open([
            'route' => 'admin.products.store',
            'class' => 'ui-form',
            'id' => 'productCreateForm',
			'enctype' => 'multipart/form-data'
            ]) !!}
    @include('admin.products.form', ['submitButtonText' => __('Create New ')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->
@stop
