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
				<i class="fab fa-critical-role text-primary"></i>
			</span>
			<h3 class="form_title">Roles
				@if(!empty($companyId))
					<span> | Client Name : {{isset($companyData->client_data) && isset($companyData->client_data['name']) ? $companyData->client_data['name'] : null}} | Company Name : {{$companyData->company_name}} </span>
				@endif
			</h3>
		</div>
	</div>
	<div class="card-body">
		@if($companyId)
			<div class="row">
				<div class="col-md-12 text-right mb-5">					
					<a href="{{url('rkadmin/roles/create?company_id='.encrypt($companyId))}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Role</a>
					<a href="{{url('rkadmin/clients')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-back"></i>Back</a>
				</div>
			</div>
		@else
		<div class="row">
				<div class="col-md-12 text-right mb-5">
					<a href="{{url('rkadmin/roles/create?type='.$type)}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Role</a>
				</div>
			</div>
		@endif

		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					{{-- <th>Client</th> --}}
					<th>Role</th>
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
	var companyId = "{{$companyId}}";
	var type = "{{$type}}";
	var tbl = $('#kt_datatable');
var KTDatatablesDataSourceAjaxServer = {
	init: function () {
		$("#kt_datatable").DataTable({
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
					title: 'Roles',
					//action: newexportaction,
					exportOptions: {
						columns: [0]
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
					title: 'Roles',
					//action: newexportaction,
					exportOptions: {
						columns: [0]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Roles',
					//action: newexportaction,
					exportOptions: {
						columns: [0]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.roles.data') !!}?company_id='+companyId+'&type='+type,
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
                // {data: 'clientname', name: 'clientname'},
                {data: 'namelink', name: 'name'},
                { data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		})
	}
};
$( document ).ajaxComplete(function() {
	// Required for Bootstrap tooltips in DataTables
	$('[data-toggle="tooltip"]').tooltip({
		"html": true,
		"delay": {"show": 100, "hide": 0},
	});
});
jQuery(document).ready((function () {
	KTDatatablesDataSourceAjaxServer.init()
}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection