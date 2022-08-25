@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-email text-primary"></i>
            </span>
            <h3 class="form_title">Email Template : <span>Create</span></h3>
        </div>
    </div>
    <div class="card-body">
	@include('admin.layouts.alert')
	 {!! Form::open([
            'route' => 'admin.emails.store',
            'class' => 'ui-form',
            'id' => 'templateCreateForm',
            'files' => true
            ]) !!}
    @include('admin.email.form', ['submitButtonText' => __('Create New Template')])

    {!! Form::close() !!}
	</div>
</div>
@stop