{{-- Extends layout --}}
@extends('admin.layouts.default')

{{-- Content --}}
@section('content')
<div class="row">
	<div class="col-lg-12">
	

	</div>
</div>
<div class="row">
	<div class="col-lg-12">
	<!--begin::Card-->
<div class="card card-custom">
	<div class="card-header">
		<div class="card-title">
			<h3 class="form_title">Custom Form
				@if(!empty($companyData))
					<span> | Client Name : {{$companyData->client_data['name']}} | Company Name : {{$companyData->company_name}}</span>
				@endif
			</h3>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-12 text-right">
				
				<a href="{{url('rkadmin/custom-form/create/'.$companyId)}}" class="btn btn-success btn-shadow font-weight-bold mr-2 add-custom-form d-none"><i class="flaticon2-plus"></i>Add Form</a>
				
				<a href="{{URL::to('rkadmin/clients')}}" class="btn btn-md btn-success ml-2"><i class="flaticon2-back"></i> Back</a>
			</div>
		</div>
		@include('admin.layouts.alert')
		{{-- <div class="row">
			<div class="col-md-3">
				{{Form::select('client',$clients,'',['class'=>'form-control company_id'])}}
				<span class="form-text text-muted ml-2">Please select client</span>
			</div>
		</div> --}}
		{{-- include custom form data --}}
		{{-- edit --}}
		{{-- <div class="row">
			<div class="col-md-9">
			</div>
			<div class="col-md-3 text-right">
				<a href="#" class="btn btn-primary edit-form-data d-none">Edit Form</a>
			</div>
		</div> --}}
		<div class="custom-form-data"></div>
		<!--begin: Datatable-->
		{{-- <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Name</th>
					<th>Company Name</th>
					<th>City</th>					
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table> --}}
		<!--end: Datatable-->
	</div>
</div>
<!--end::Card-->
	</div>
</div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script>
	"use strict";
	var qstring = '';
	var company_id = "{{$companyId}}";
	$(document).ready(function(){
		qstring = 'company_id='+company_id;
		getCustomFormData(qstring);

		$(document).on('change','.company_id',function(){
			company_id = $(this).val();
			qstring = 'company_id='+company_id;
			getCustomFormData(qstring);
		});
	});
	$(document).on('click','.edit-form-data',function(){
		var url = "{{URL::to('rkadmin/custom-form')}}"+'/'+company_id+'/'+'contact/edit';
		window.location.href = url;
	});
	function getCustomFormData(qstring){
		$.ajax({
			url: "{{url('rkadmin/custom-form')}}"+'/'+"{{$companyId}}"+'?'+qstring,
			dataType: 'json',
		}).done(function(data) {
			if(data.status == 1){
				$('.custom-form-data').html(data.custom_form);
				if(data.isEdit == 1){
					$('.edit-form-data').removeClass('d-none');
					$('.add-custom-form').addClass('d-none');
				}else{
					$('.edit-form-data').addClass('d-none');
					$('.add-custom-form').removeClass('d-none');
				}
			}
		}).fail(function() {

		});
	}
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection