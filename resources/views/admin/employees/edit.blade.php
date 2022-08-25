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
<h3 class="form_title">Edit @if(!empty($companyId)) Employee @else User @endif : <span>({{$employee->name}})
@php
    if($companyId){ 
        @endphp    
        | Client Name : {{ ($employee->organizationName) ? $employee->organizations['name'] : '' }} | Company Name : {{ ($employee->organizationName) ? $employee->organizationName['company_name'] : '' }}
    @php    
    }
   @endphp 
</span></h3>
<hr>
 @include('admin.layouts.alert')
    {!! Form::model($employee, [
            'method' => 'PATCH',
            'route' => ['admin.employees.update', $employee->id],
			'class' => 'ui-form',
			'id' => 'employeeForm',
			'enctype' => 'multipart/form-data'
            ]) !!}
            <?php
            if(!empty($companyId)){ 
                $btn_label = 'Update Employee'; 
            }else{
                $btn_label = 'Update User'; 
            }
            ?>
    @include('admin.employees.form', ['submitButtonText' =>  $btn_label])

    {!! Form::close() !!}

@stop
