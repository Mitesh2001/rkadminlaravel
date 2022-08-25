{{-- Extends layout --}}
@extends('admin.layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon2-supermarket text-primary"></i>
	</span>
	<h3 class="card-label">{{ __('Edit permission :permission' , ['permission' => '(' . $permission->name. ')']) }}</h3>
</div>
</div>
<div class="card-body">
 @include('admin.layouts.alert')
    {!! Form::model($permission, [
            'method' => 'PATCH',
			'class' => 'ui-form',
            'id' => 'permissionForm',
            'route' => ['admin.permissions.update', $permission->id],
            ]) !!}
    @include('admin.permissions.form', ['submitButtonText' => __('Update permission')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->
@stop