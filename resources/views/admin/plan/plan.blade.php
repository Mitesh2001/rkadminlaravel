@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon-list-2 text-primary"></i>
            </span>
            <h3 class="form_title">
            @if(!empty($plan))
                Update Plan : <span>{{isset($plan->name) ? $plan->name : null}}</span>
            @else
                Add Plan
            @endif
            </h3>    
        </div>
    </div>
    <div class="card-body">
        @include('admin.layouts.alert')
        {!! Form::open([
                'route' => 'admin.plan.store',
                'class' => 'ui-form',
                'id' => 'planCreateForm'
                ]) !!}
        @include('admin.plan.form', ['submitButtonText' => __(!empty($plan) ? 'Update Plan' : 'Create New Plan')])

        {!! Form::close() !!}
    </div>
</div>
<!--end::Card-->
@stop
