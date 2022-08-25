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
				<i class="flaticon-list-1 text-primary"></i>
			</span>
			<h3 class="form_title">Product Category
				<span>
					@if(!empty($companyId))
						| Client Name : {{!empty($companyData->client_data) ? $companyData->client_data['name'] : null}} | Company Name : {{$companyData->company_name}}
					@endif
				</span>
			</h3>
		</div>
	</div>
	<div class="card-body">
		@include('admin.layouts.alert')
		
			<div class="row">
				<div class="col-md-12 text-right mb-5">
					@php
					if($companyId){
						$url = route('admin.category.create',encrypt($companyId));
					}else{
						$url = route('admin.master.category.create');
					}
					@endphp
					@if($companyId)
					<a href="{{route('admin.clients.index')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-back"></i>Back</a>
					@endif
					<a href="{{$url}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Product Category</a> {{-- url('rkadmin/product-category/create?company_id='.encrypt($companyId)) --}}
				</div>
			</div>
		
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Name</th>
					<th>Created By</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table>
		<!--end: Datatable-->
	</div>
	@include('admin.layouts.modal',['modalId'=>'product-category-delete','content'=>'Are you sure you want to delete product category ?','title'=>'Delete'])
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
	var companyId = "{{$companyId}}";
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
					title: 'Product Category',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1]
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
					title: 'Product Category',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Product Category',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.category.data') !!}?company_id='+companyId,
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'name', name: 'name'},
                {data: 'created_by', name: 'created_by'},
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
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
	KTDatatablesDataSourceAjaxServer.init();
	jQuery(document).on('click','.product-category-delete',function(e){
		$('#product-category-delete').modal('show');
		$('.delete-record').data('id',$(this).data('id'));
	});
	jQuery(document).on('click','.delete-record',function(e){
		var dId = $(this).data('id');
		var token = "{{csrf_token()}}";
		$.ajax({
			url:"{{URL::to('rkadmin/product-category')}}"+'/'+dId,
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