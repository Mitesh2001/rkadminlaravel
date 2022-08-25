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
			<h3 class="form_title">Custom Field
				@if(!empty($companyData))
					<span> | Client Name : {{$companyData->client_data['name']}} | Company Name : {{$companyData->company_name}}</span>
				@endif
			</h3>
		</div>
	</div>
	<div class="card-body">
	@include('admin.layouts.alert')
		<div class="row">
			<div class="col-md-12 text-right">
				@if(count($formarray)<3)
				<a href="{{url('rkadmin/custom-form/create/'.$companyId)}}" class="btn btn-success btn-shadow font-weight-bold mr-2 add-custom-form "><i class="flaticon2-plus"></i>Add Form Field</a>
				@endif
				<a href="{{URL::to('rkadmin/clients')}}" class="btn btn-md btn-success ml-2"><i class="flaticon2-back"></i> Back</a>
			</div>
		</div>
		
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
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Form Name</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($formarray as $form)
				<tr>
					<td>{!! $form['name'] !!}</td>
					<td>{!! $form['action'] !!}</th>
				</tr>
			@endforeach
			</tbody>
		</table>
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
var tbl = $('#kt_datatable');
var KTDatatablesDataSourceAjaxServer = {
	init: function () {
		$("#kt_datatable").DataTable({
			//dom: 'Bfrtip',
			dom: "<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'i><'col-sm-12 col-md-6'p>><'clear'>",
			sPaginationType: "full_numbers",
			lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 100, "All"] ],
			aaSorting: [],
			language: {
				searchPlaceholder: "Search"// all field
			},
			columnDefs: [
				{ orderable: false, targets: 1 }
			  ],
			  order: [[0, 'asc']]
		})
	}
};
jQuery(document).ready((function () {
	KTDatatablesDataSourceAjaxServer.init();
}));

	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection