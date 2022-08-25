{{-- Extends layout --}}
@extends('admin.layouts.default')
@section('styles')
<style>
    #toggle-password{
        float: right;
        margin-top: -26px !important;
        margin-right: 10px;
    }
</style>
@endsection
@section('content')
@include('admin.layouts.alert')
<h3 class="form_title">Profile
</h3>
<hr>
 
    {!! Form::model($employee, [
            'method' => 'PATCH',
            'route' => ['admin.profileupdate', $employee->id],
			'class' => 'ui-form',
			'id' => 'employeeForm',
			'enctype' => 'multipart/form-data'
            ]) !!}
            <?php
                $btn_label = 'Update'; 
            ?>
    @include('admin.employees.form', ['submitButtonText' =>  $btn_label])

    {!! Form::close() !!}

@stop
