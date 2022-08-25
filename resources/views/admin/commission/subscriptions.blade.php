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
				<i class="flaticon2-crisp-icons text-primary"></i>
			</span>
			<h3 class="form_title">Subscriptions</h3>
		</div>
	</div>
	<div class="card-body">
	 @include('admin.layouts.alert')
	 
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Subscriptions ID</th>
					<th>Mode of payment</th>
					<th>Amount</th>
					<th>Date of Plan</th>
					<th>Plan Expiry Date</th>
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
					title: 'Commission',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4]
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
					title: 'Commission',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Commission',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.commissions.data') !!}?dealerDistributor={{$dealerDistributor}}&month={{$month}}&year={{$year}}&status={{$status}}',
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'subscriptions_uid', name: 'subscriptions_uid'},
                {data: 'payment_mode', name: 'payment_mode'},
				{data: 'final_amount', name: 'final_amount'},
                {data: 'created_at', name: 'created_at'},
				{data: 'expiry_date', name: 'expiry_date'},
            ]
		})
	}
};
jQuery(document).ready((function () {
	KTDatatablesDataSourceAjaxServer.init();
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