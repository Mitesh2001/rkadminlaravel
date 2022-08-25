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
				<i class="fa fa-puzzle-piece text-primary"></i>
			</span>
			<h3 class="form_title">Email SMS Log</h3>
		</div>
	</div>
	<div class="card-body">
	 @include('admin.layouts.alert')
	 <div class="row mb-8">
			<div class="col-lg-4 col-md-9 col-sm-12">
				<div class="input-group" id="kt_daterangepicker_2_range">
					{{Form::text('email_sms_log_date','',['class'=>'form-control log_date','readonly','placeholder'=>'Please select email sms log date'])}}
					<div class="input-group-append">
						<span class="input-group-text">
							<i class="la la-calendar-check-o"></i>
						</span>
					</div>
				</div>
				<span class="form-text text-muted ml-2">Please select email sms log date</span>
			</div>
			<div class="col-md-3">
				{{Form::select('type',['EMAIL'=>'EMAIL','SMS'=>'SMS'],'',['class'=>'form-control log_type','placeholder'=>'Please select Email Or SMS'])}}
				<span class="form-text text-muted ml-2">Please select type</span>
			</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-primary log-filter-submit">Submit</button>
				<button type="button" class="btn btn-primary log-filter-reset">Reset</button>
			</div>
		</div>
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Sender Name</th>
                    <th>Client Name</th>
                    <th>Client Mobile No.</th>
                    <th>Client Email</th>
					<th>Company Name</th>
					@if($type == 1)
					<!--<th>Dealer / Distributor</th>-->
					@endif
					<th>Type</th>
					<th>Status</th>
					<th>Entry Date</th>
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
	<script src="{{ asset('js/pages/crud/forms/widgets/bootstrap-daterangepicker.js?v=7.2.6') }}" type="text/javascript"></script>
    {{-- page scripts --}}
    <script>
	"use strict";
	var table = '';
//var tbl = $('#kt_datatable');
	function generateDataTable(log_date= null, log_type= null) {
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
					title: 'Email SMS Log',
					//action: newexportaction,
					orientation:'landscape',
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7]{{-- ($type == 1)?',8':'' --}}
					},
					/* customize : function(doc){
						var colCount = new Array();
						$(tbl).find('tbody tr:first-child td').each(function(){
							if($(this).attr('colspan')){
								for(var i=1;i<=$(this).attr('colspan');i++){
									colCount.push('*');
								}
							}else{ colCount.push('*'); }
						});
						doc.content[1].table.widths = colCount;
					} */
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'csv',
					title: 'Email SMS Log',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7{{-- ($type == 1)?',8':'' --}}]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Email SMS Log',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6,7{{-- ($type == 1)?',8':'' --}}]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: {
				url: '{!! route('admin.email_sms_log.data') !!}',
				data: function(data) {data.log_date=log_date,data.log_type=log_type;} 
			},
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
                {data: 'name', name: 'name'},
                {data: 'client_name', name: 'client_name'},
                {data: 'mobile_no', name: 'mobile_no'},
                {data: 'client_email', name: 'client_email'},
                {data: 'company_name', name: 'company_name'},
				@if($type == 1)
				//{data: 'dealer_distributor_name', name: 'dealer_distributor_name'},
				@endif
				{data: 'type', name: 'type'},
				{data: 'status', name: 'response'},
				{data: 'created_at', name: 'created_at'},
            ]
		})
	}

jQuery(document).ready((function () {
	var log_date = null;
	var log_type = null;

	jQuery(document).on('click','.log-filter-submit',function(){
		table.destroy();
		log_date = $('.log_date').val();
		log_type = $('.log_type').val();
		generateDataTable(log_date,log_type);
	});

	jQuery(document).on('click','.log-filter-reset',function(){
		table.destroy();
		$(".log_date, .log_type").val(null).trigger('change');
		generateDataTable();
	});

	generateDataTable();
}));


var KTBootstrapDaterangepickerRange = function () {


var demosRange = function () {
	
	$('#kt_daterangepicker_2_range').daterangepicker({
		buttonClasses: ' btn',
		applyClass: 'btn-primary',
		cancelClass: 'btn-secondary'
	},
	function(start, end, label) {
		console.log(start);
		$('#kt_daterangepicker_2_range .form-control').val( start.format('YYYY-MM-DD') + ' / ' + end.format('YYYY-MM-DD'));
	});
}

return {
	// public functions
	init: function() {
		demosRange();
		
	}
};
}();

jQuery(document).ready(function() {
KTBootstrapDaterangepickerRange.init();
});

	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection