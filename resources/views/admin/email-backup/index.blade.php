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
	 @include('admin.layouts.alert')
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Name</th>
					<th>Subject</th>
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
var KTDatatablesDataSourceAjaxServer = {
	init: function () {
		$("#kt_datatable").DataTable({
			dom: 'Bfrtip',
			aaSorting: [],
			language: {
				searchPlaceholder: "Search"// all field
			},
			buttons: [
				$.extend( true, {}, fixNewLine, {
					extend: 'pdf',
					title: 'Email_Template',
					action: newexportaction,
					exportOptions: {
						columns: [0,1]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'csv',
					title: 'Email_Template',
					action: newexportaction,
					exportOptions: {
						columns: [0,1]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Email_Template',
					action: newexportaction,
					exportOptions: {
						columns: [0,1]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.emails.index') !!}',
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
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		})
	}
};
jQuery(document).ready((function () {
	KTDatatablesDataSourceAjaxServer.init()
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