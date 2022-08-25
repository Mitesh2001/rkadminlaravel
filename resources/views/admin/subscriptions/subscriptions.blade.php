@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-crisp-icons text-primary"></i>
            </span>
            <h3 class="form_title">{{ __(!empty($subscription) ? 'Update Subscription' : 'Create Subscription') }}

            <span> {{ __(!empty($subscription) ? '| '.$subscription->subscriptions_uid : '') }} </span>

                @if(!empty($companyData))
					<span>| Client Name : {{isset($companyData->client_data['name']) ? $companyData->client_data['name'] : null}} | Company Name : {{$companyData->company_name}}</span>
				@endif
            </h3>
        </div>
    </div>
    <div class="card-body">
        @include('admin.layouts.alert')
		
		@if(isset($subscription))
			{!! Form::model($subscription, [
            'method' => 'PATCH',
            'route' => ['admin.subscriptions.update', $subscription->id],
			'enctype' => 'multipart/form-data',
			'class' => 'ui-form',
            'id' => 'subscriptionUpdateForm'
            ]) !!}
		@else
        {!! Form::open([
                'route' => 'admin.subscriptions.store',
                'class' => 'ui-form',
                'id' => 'subscriptionCreateForm'
                ]) !!}
		@endif

        @if(isset($subscription))
            @include('admin.subscriptions.update_form', ['submitButtonText' => __(!empty($subscription) ? 'Update Subscription' : 'Save Subscription')])
        @else
            @include('admin.subscriptions.form', ['submitButtonText' => __(!empty($subscription) ? 'Update Subscription' : 'Save Subscription')])
        @endif
                 
        {!! Form::close() !!}
    </div>
</div>
<!--end::Card-->
@stop
