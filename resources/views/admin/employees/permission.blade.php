@extends('admin.layouts.default')
@section('styles')
    <style>
        .font-weight{
            font-weight: 600 !important;
        }
        .p-row{
            margin-left: -2.5px !important;
        }
    </style>
@endsection
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="fas fa-user-cog text-primary"></i>
            </span>
            <h3 class="card-label">{{'Employee : '.$user->name}}</h3>
        </div>
    </div>
</div>
    <div class="card-body pl-0 pr-0">
        @include('admin.layouts.alert')
        {!! Form::open(['url'=>'rkadmin/employees/permission','method' => 'POST']) !!}
            {{Form::hidden('user_id',$user->id)}}
            <div class="row">
                @php
                    $i = 0;
                    $j = 0;
                @endphp
                @foreach($roleData as $k => $value)
                    @php
                        $valueId = str_replace(" ", "_", $k);
                        $permissionCount = count($value);
                        $addedPermissionCount = isset($rolePermissions) ? array_intersect(array_keys($value), $rolePermissions) : [];
                        $addedPermissionCount = count($addedPermissionCount);
                        $isChecked = $addedPermissionCount == $permissionCount ? true : false;
                    @endphp
                    
                    <div class="col-md-4">
                        <div class="card card-custom card-stretch">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label"><input type="checkbox" class="{{'check-item select-all '.$valueId.'-main'}}" id="{{'checbox-'.$valueId}}" data-class="{{$valueId}}" @if($isChecked) checked="checked" @endif>
                                        <label for="{{'checbox-'.$valueId}}">{{$k}}</label>
                                    </h3>
                                </div>
                            </div>
                            <div class="row p-row">
                                @foreach ($value as $id=>$item)
                                    <div class="col-md-4 pt-3 pr-0">
                                        <input type="checkbox" name="permission[]" class="{{'check-item sub-permission '.$valueId.' p-'.$i}}" id="{{'checkbox-'.$j}}" data-id="{{$i}}" data-mainclass="{{$valueId.'-main'}}" value="{{$id}}" @if(isset($rolePermissions) && in_array($id, $rolePermissions)) checked="checked" @endif>
                                        <label for="{{'checkbox-'.$j}}">{{$item}}</label>
                                    </div>
                                    @php
                                        $j++;
                                    @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if(($i + 1)%3 === 0)
                        </div><div class="row mt-3">
                    @endif
                    @php
                        $i++;
                    @endphp
                @endforeach
            </div>
            <br>
            <div class="footer">
                <div class="row">
                    <div class="col-lg-12">
                        {!! Form::submit('Submit', ['class' => 'btn btn-md btn-primary']) !!}
                        
                        @php
                        if($user->company_id){
                            $url = url('rkadmin/employees/'.encrypt($user->company_id));
                        }else{
                            $url = url('rkadmin/employees/');
                        }
                        @endphp
					    <a href="{{$url}}" class="btn btn-md btn-primary ml-2">Cancel</a>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
<!--end::Card-->
@stop
