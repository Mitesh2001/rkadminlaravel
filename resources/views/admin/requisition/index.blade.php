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
				<i class="flaticon-interface-11 text-primary"></i>
			</span>
			<h3 class="form_title">Requisitions</h3>
		</div>
	</div>
	
	<div class="card-body">
	{{Form::open(['class'=>'requisitions-filter-form'])}}
		<div class="row mb-8">
			<div class="col-md-3">
				{{Form::select('client_id',$clientName,'',['class'=>'form-control client-id searchpicker','placeholder'=>'Please select client name'])}}
				<span class="form-text text-muted ml-2">Please select client name</span>
			</div>
			<div class="col-md-4">
				{{Form::select('company_name',$companyName,'',['class'=>'form-control company-id searchpicker','placeholder'=>'Please select company name'])}}
				<span class="form-text text-muted ml-2">Please select company name</span>
			</div>
			<div class="col-md-3">
				{{Form::select('status',['3'=>'Pending','1'=>'Accepted','2'=>'Rejected'],'',['class'=>'form-control status','placeholder'=>'Please select Status'])}}
				<span class="form-text text-muted ml-2">Please select status</span>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-primary requistions-filter-submit">Submit</button>
			</div>
		</div>
	{{Form::close()}}

	 @include('admin.layouts.alert')
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Client</th>
					<th>Company</th>
					<th>Type</th>
					<th>Quantity</th>
					<th>Note</th>
					<th>Status</th>
					<th>Created</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table>
		<!--end: Datatable-->
	</div>

	<!-- Modal-->
	<div class="modal fade" id="requisitions_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		{{Form::open(['route' => 'admin.requisitions.status'])}}
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Requisitions</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<i aria-hidden="true" class="ki ki-close"></i>
					</button>
				</div>
				<div class="modal-body">
					<div class="col-lg-12">
						{!! Form::label('note', __('Note'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!! 
							Form::textarea('note',
							isset($note['note']) ? $note['note'] : null, 
							['class' => 'form-control','required','rows'=>'3']) 
						!!}
						<span class="form-text text-muted">Please enter note or subscription id</span>
					</div>
					{{Form::hidden('id',null,['id'=>'req_id'])}}
					{{Form::hidden('status',null,['id'=>'req_status'])}}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
				</div>
			</div>
		</div>
		{{Form::close()}}
	</div>
	<!--End Modal-->

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
	var clientId = '';
	var companyId = '';
	var status = '';
	var table = '';var tbl = $('#kt_datatable');
	function generateDataTable(clientId,companyId,status){
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
					title: 'Requisitions',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
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
					title: 'Requisitions',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Requisitions',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
					}
				})
			],
			processing: true,
            serverSide: true,
			ajax: {
				url:'{!! route('admin.requisitions.data') !!}',
				data: function(data) {data.client_id=clientId,data.company_id=companyId,data.status=status;} 
			},
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'client', name: 'client'},
				{data: 'company', name: 'company'},
				{data: 'type', name: 'type'},
                {data: 'quantity', name: 'quantity'},
                {data: 'note', name: 'note'},
				{data: 'status', name: 'status', orderable: false, searchable: false},
				{data: 'created', name: 'created'},
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		});
	}
	jQuery(document).ready((function () {
		generateDataTable();
		jQuery(document).on('click','.requistions-filter-submit',function(){
			table.destroy();
			companyId = $('.company-id').val();
			clientId = $('.client-id').val();
			status = $('.status').val();
			generateDataTable(clientId,companyId,status);
		});
		jQuery(document).on('click','.requisition_status',function(){
			var req_id = $(this).data('id');
			var req_status = $(this).data('status');
			
			$("#req_id").val(req_id);
			$("#req_status").val(req_status);
		});
	}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection