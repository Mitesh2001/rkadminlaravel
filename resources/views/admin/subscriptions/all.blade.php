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
	@php
		if($running)
		$closed = 2;
	@endphp
		<div class="row">
			<div class="col-md-2 mb-5">
			{!! Form::label('payment pending', __('Payment Pending'), ['class' => '']) !!}
				<span class="text-danger">*</span>
				{!!
					Form::select('payment_pending',
					array('ALL','YES','NO'),
					null, 
					['class' => 'form-control payment-pending',
					'id' => 'payment-select'])
			!!}
				<span class="form-text text-muted">Please select payment pending status</span>
			</div>
			<div class="col-md-2 mb-5">
				{!! Form::label('payment_mode', __('Payment Mode'), ['class' => '']) !!}
					{!!
						Form::select('payment_mode',
						$payment_modes,
						null, 
						['class' => 'form-control payment-mode',
						'id' => 'payment-mode'])
				!!}
				<span class="form-text text-muted">Please select payment mode</span>
			</div>
			<div class="col-md-2 mb-5">
				{!! Form::label('expired_subscription', __('Expired Subscription'), ['class' => '']) !!}
					{!!
						Form::select('expired_subscription',
						array('ALL','YES','NO'),
						(($closed<=2)?$closed:0), 
						['class' => 'form-control expired-subscription',
						'id' => 'expired_subscription'])
				!!}
				<span class="form-text text-muted">Please select expired subscription</span>
			</div>
			
			<div class="col-md-2 mb-5 pt-7">
				<button type="button" class="btn btn-primary filter-submit">Submit</button>
				<button type="button" class="btn btn-primary filter-reset">Reset</button>
			</div>
		</div>
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Subscriptions ID</th>
					<th>Company Name</th>
					<th>Client Name</th>
					<th width="10%">Mode of payment</th>
					<th>Amount</th>
					<th width="10%">Payment Pending</th>
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
	var table = '';var tbl = $('#kt_datatable');
function generateDataTable(payment_pending,payment_mode,expired_subscription, newsubscription) {
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
				title: 'Subscriptions',
				//action: newexportaction,
				orientation:'landscape',
				exportOptions: {
					columns: [0,1,2,3,4,5,6,7]
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
				title: 'Subscriptions',
				//action: newexportaction,
				exportOptions: {
					columns: [0,1,2,3,4,5,6,7]
				}
			}),
			$.extend( true, {}, fixNewLine, {
				extend: 'print',
				title: 'Subscriptions',
				//action: newexportaction,
				exportOptions: {
					columns: [0,1,2,3,4,5,6,7]
				}
			})
		],
		processing: true,
		serverSide: true,
		ajax: '{!! route('admin.subscriptions.allData') !!}?payment_pending='+payment_pending+'&payment_mode='+payment_mode+'&expired_subscription='+expired_subscription+'&newsubscription='+newsubscription,
		name:'search',
		drawCallback: function(){
			var length_select = $(".dataTables_length");
			var select = $(".dataTables_length").find("select");
			select.addClass("tablet__select");
		},
		autoWidth: false,
		columns: [
			{data: 'subscriptions_uid', name: 'subscriptions_uid'},
			{data: 'company_name', name: 'company_name'},
			{data: 'client_name', name: 'client_name'},
			{data: 'payment_mode', name: 'payment_mode'},
			{data: 'final_amount', name: 'final_amount'},
			{data: 'is_payment_pending', name: 'is_payment_pending'},
			{data: 'created', name: 'created'},
			{data: 'expiry_date', name: 'expiry_date'}
		]
	})
}
jQuery(document).on('click','.filter-submit',function(){
	var payment_pending = $('.payment-pending').val();
	var payment_mode = $('.payment-mode').val();
	var expired_subscription = $('.expired-subscription').val();
	table.destroy();
	generateDataTable(payment_pending, payment_mode,expired_subscription,0);
});
jQuery(document).on('click','.filter-reset',function(){
	$('.payment-pending').val('0');
	$('.payment-mode').val('0');
	$('.expired-subscription').val('0');
	table.destroy();
	generateDataTable(0,0,<?php echo $closed;?>,<?php echo $new;?>);
});
jQuery(document).ready((function () {
	generateDataTable(0,0,<?php echo $closed;?>,<?php echo $new;?>);
}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection