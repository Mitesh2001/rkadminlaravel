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
			<span class="card-icon">
				<i class="flaticon-users-1 text-primary"></i>
			</span>
			<h3 class="form_title"> 
				@if(!empty($companyData)) Employees <span> | Client Name : {{$companyData->client_data['name']}} | Company Name : {{$companyData->company_name}} </span>
				@else Users @endif
			</h3>
		</div>
	</div>
	
	<div class="card-body">
	
	{{Form::open(['class'=>'client-filter-form'])}}
		<div class="row mb-8">
			@if(empty($companyData))	
			<div class="col-md-3">
				{{Form::select('type',
					$usersType,'',['class'=>'form-control type','placeholder'=>'Please select user type','id'=>'type'])}}
					<span class="form-text text-muted ml-2">Please select user type</span>	
			</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-primary employee-filter-submit">Submit</button>
				<button type="button" class="btn btn-primary employee-filter-reset">Reset</button>
			</div>
			@else
			<div class="col-md-6"></div>
			@endif
			<div class="col-md-6 text-right">
				@php
					$url = $companyId ? encrypt($companyId) : '';
				@endphp
				@if(!empty($companyData))
					@if($companyData->no_of_users > $total_employees && $companyData->expiry_date > date("Y-m-d H:i:s"))
				<a href="{{url('rkadmin/employees/create/'.$url)}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Employee </a>
				@endif
				@else 
					<a href="{{url('rkadmin/employees/create/'.$url)}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add User </a>
				@endif
				

				@if($companyId)
					<a href="{{url('rkadmin/clients')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-back"></i>Back</a>
				@endif
			</div>
		</div>
	{{Form::close()}}
	

	 @include('admin.layouts.alert')
		
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Name</th>
					<th>Designation</th>
					<th>Mobile No</th>					
					<th>Email</th>
					@if(!$companyId)
					<th>User Type</th>
					<th>Commission</th>					
					@endif
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table>
		<!--end: Datatable-->
	</div>
	@include('admin.layouts.modal',['modalId'=>'employee-delete','content'=>'Are you sure you want to delete employee ?','title'=>'Delete'])
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
	var table = '';var tbl = $('#kt_datatable');
	function generateDataTable(type=0) {
		table = $("#kt_datatable").DataTable({
			//dom: 'Bfrtip',
			dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'i><'col-sm-12 col-md-6'p>><'clear'>",
			sPaginationType: "full_numbers",
			lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 100, "All"] ],
			aaSorting: [],
			language: {
				searchPlaceholder: "Search"// all field
			},
			buttons: [
				$.extend( true, {}, fixNewLine, {
					extend: 'pdfHtml5',
					title: '{!! ($companyId)?"Employees":"Users" !!}',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3{!! (!$companyId)?',4,5':'' !!}]
					},
					customize : function(doc){
						var colCount = new Array();
						$(tbl).find('tbody tr:first-child td').each(function(){
							if($(this).attr('colspan')){
								for(var i=1;i<=$(this).attr('colspan');i++){
									colCount.push('*');
								}
							}else{ colCount.push('*'); }
						});
						doc.content[1].table.widths = colCount;
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'csv',
					title: '{!! ($companyId)?"Employees":"Users" !!}',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3{!! (!$companyId)?',4,5':'' !!}]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: '{!! ($companyId)?"Employees":"Users" !!}',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3{!! (!$companyId)?',4,5':'' !!}]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.employees.data') !!}?company_id={{$companyId}}&user_type='+type,
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
                {data: 'namelink', name: 'name'},
                {data: 'designation', name: 'designation'},
                {data: 'mobileno', name: 'mobileno'},
				{data: 'email', name: 'email'},
				@if(!$companyId)
				{data: 'type', name: 'type'},
				{data: 'commission', name: 'Commission'},
				@endif
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		})
	}
$( document ).ajaxComplete(function() {
	// Required for Bootstrap tooltips in DataTables
	$('[data-toggle="tooltip"]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	});
});
jQuery(document).ready((function () {
	generateDataTable();
	jQuery(document).on('click','.employee-delete',function(e){
		$('#employee-delete').modal('show');
		$('.delete-record').data('id',$(this).data('id'));
	});
	jQuery(document).on('click','.employee-filter-submit',function(e){
		var type = jQuery('#type').val();
		table.destroy();
		generateDataTable(type);
	});
	jQuery(document).on('click','.employee-filter-reset',function(e){
		table.destroy();
		jQuery('#type').val('');
		generateDataTable();
	});
	jQuery(document).on('click','.delete-record',function(e){
		var dId = $(this).data('id');
		var token = "{{csrf_token()}}";
		$.ajax({
			url:"{{URL::to('rkadmin/employees')}}"+'/'+dId,
			type:'DELETE',
			dataType: 'json',
			data:{_token:token}
		}).done(function(data){
			location.reload();
		}).fail({

		});
	});
}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection