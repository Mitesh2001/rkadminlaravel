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
				<i class="flaticon2-email text-primary"></i>
			</span>
			<h3 class="form_title">Emails Template</h3>
		</div>
	</div>
	<div class="card-body">
	<div class="row mb-8">
	<div class="col-md-9">
	{{Form::open(['class'=>'client-filter-form'])}}
	<div class="row">
		<div class="col-md-4">
		@php
		$type = array('1'=>'Default Events', '2'=>'Marketing');
		@endphp
		{{Form::select('type',
		$type,'',['class'=>'form-control type','placeholder'=>'Please select template type','id'=>'type'])}}
		<span class="form-text text-muted ml-2">Please select template type</span>
		</div>
		<div class="col-md-4">
			<button type="button" class="btn btn-primary template-filter-submit">Submit</button>
			<button type="button" class="btn btn-primary template-filter-reset">Reset</button>
		</div>
		<div class="col-md-4">
		</div>
	</div>
	{{Form::close()}}
	</div>
	<div class="col-md-3 text-right">
	<a href="{{route('admin.emails.create')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Template </a>
	</div>
	</div>
	 @include('admin.layouts.alert')
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Name</th>
					<th>Subject</th>
					<th>Type</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
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
	var tbl = $('#kt_datatable');
	var table = '';
	function generateDataTable() {
		var type = jQuery('#type').val();
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
					title: 'Email Template',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2]
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
					title: 'Email Template',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Email Template',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.emails.index') !!}?template_type='+type,
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'name', name: 'name'},
                {data: 'subject', name: 'subject'},
                {data: 'type', name: 'type'},
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		})
	
}
jQuery(document).ready((function () {
	generateDataTable();
	jQuery(document).on('click','.template-filter-submit',function(e){
		table.destroy();
		generateDataTable();
	});
	jQuery(document).on('click','.template-filter-reset',function(e){
		table.destroy();
		jQuery('#type').val('');
		generateDataTable();
	});
}));
$( document ).ajaxComplete(function() {
	// Required for Bootstrap tooltips in DataTables
	$('[data-toggle="tooltip"]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	});
});
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection