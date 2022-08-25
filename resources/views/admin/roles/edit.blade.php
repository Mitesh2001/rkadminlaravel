{{-- Extends layout --}}
@extends('admin.layouts.default')
@section('content')
<div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fab fa-critical-role text-primary"></i>
                </span>
                <h3 class="form_title">Edit role : <span>({{$role->name}}) @if(!empty($companyId))
					<span> | Client Name : {{isset($companyData->client_data) && isset($companyData->client_data['name']) ? $companyData->client_data['name'] : null}} | Company Name : {{$companyData->company_name}} </span>
				@endif</span></h3>
            </div>
        </div>
</div>
    <div class="card-body pl-0 pr-0">
        @include('admin.layouts.alert')
        {!! Form::model($role, [
                'method' => 'PATCH',
                'route' => ['admin.roles.update', $role->id],
				'class' => 'ui-form',
                'id' => 'roleForm'
                ]) !!}
        @include('admin.roles.form', ['submitButtonText' => __('Update role')])

        {!! Form::close() !!}
    </div>
<!--end::Card-->
@stop