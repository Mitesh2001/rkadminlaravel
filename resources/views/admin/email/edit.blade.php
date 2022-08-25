@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
<div class="card-header">
	<div class="card-title">
		<span class="card-icon">
			<i class="flaticon2-email text-primary"></i>
		</span>
		<h3 class="form_title">Email Template : <span>Update</span></h3>
	</div>
</div>
<div class="card-body">
@include('admin.layouts.alert')
	{!! Form::model($emailTemplate, [
            'method' => 'PATCH',
            'route' => ['admin.emails.update', $emailTemplate->email_template_id],
			'class' => 'ui-form',
            'id' => 'templateForm',
            'files' => true
            ]) !!}
	@include('admin.email.form', ['submitButtonText' => __('Update Template')])

	{!! Form::close() !!}
</div>
</div>
@stop