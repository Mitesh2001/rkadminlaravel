{{-- Extends layout --}}
@extends('admin.layouts.default')

{{-- Content --}}
@section('content')

    {{-- Dashboard 1 --}}

<div class="row">
    <div class="col-lg-12">
        @include('admin.layouts.alert')
        <div class="card card-custom bg-gray-100 card-stretch gutter-b"> 
            <div class="card-spacer">
                <div class="row">
                    <div class="col-md-2 bg-light-primary px-6 py-8 rounded-xl mb-7 mr-10">
                        <span class="svg-icon svg-icon-primary d-block my-2">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </span>
                        <a href="{{url('rkadmin/clients')}}" class="text-primary font-weight-bold font-size-h6 mt-2">Clients : {{$clientsCount}}</a>
                    </div>
                    @if(Auth::user()->type == 1)
                        <div class="col-md-2 bg-light-primary px-6 py-8 rounded-xl mb-7 mr-10">
                            <span class="svg-icon svg-icon-primary d-block my-2">
                                <i class="fas fa-user-friends fa-2x text-primary"></i>
                            </span>
                            <a href="{{url('rkadmin/employees')}}" class="text-primary font-weight-bold font-size-h6 mt-2">Users : {{$employeesCount}}</a>
                        </div>
                    @endif
                    <div class="col-md-2 bg-light-primary px-6 py-8 rounded-xl mb-7 mr-10">
                        <span class="svg-icon svg-icon-primary d-block my-2">
                            <i class="flaticon2-crisp-icons fa-2x text-primary"></i>
                            <a href="{{route('admin.subscriptions.all')}}?new=1" class="text-primary font-weight-bold font-size-h6 mt-2">New</a>
                        </span>
                        <a href="{{route('admin.subscriptions.all')}}?new=1" class="text-primary font-weight-bold font-size-h6 mt-2">Subscription : {{$newSubscription}}</a>
                    </div>
                    <div class="col-md-2 bg-light-primary px-6 py-8 rounded-xl mb-7 mr-10">
                        <span class="svg-icon svg-icon-primary d-block my-2">
                            <i class="flaticon2-crisp-icons fa-2x text-primary"></i>
                            <a href="{{route('admin.subscriptions.all')}}?running=1" class="text-primary font-weight-bold font-size-h6 mt-2">Running</a>
                        </span>
                        <a href="{{route('admin.subscriptions.all')}}?running=1" class="text-primary font-weight-bold font-size-h6 mt-2">Subscription : {{$runningSubscription}}</a>
                    </div>
                    <div class="col-md-2 bg-light-primary px-6 py-8 rounded-xl mb-7 mr-10">
                        <span class="svg-icon svg-icon-primary d-block my-2">
                            <i class="flaticon2-crisp-icons fa-2x text-primary"></i>
                            <a href="{{route('admin.subscriptions.all')}}?closed=1" class="text-primary font-weight-bold font-size-h6 mt-2">Closed</a>
                        </span>
                        <a href="{{route('admin.subscriptions.all')}}?closed=1" class="text-primary font-weight-bold font-size-h6 mt-2">Subscription : {{$closeSubscription}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
