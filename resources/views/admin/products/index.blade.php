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
			<h3 class="form_title">
				Product / Service
				<span>
				@if(!empty($companyData))
					| Client Name : {{$companyData->client_data['name']}} | Company Name : {{$companyData->company_name}}
				@endif
				</span>
			</h3>
		</div>
	</div>
	<div class="card-body">
		@include('admin.layouts.alert')
		<div class="row">
			<div class="col-md-3 mb-5">
				{!!
					Form::select('category_id',$productCategory,
					isset($data['category_id']) ? $data['category_id'] : null,
					['class' => 'form-control searchpicker','required','placeholder'=>'Please select category','id'=>'category_id'])
				!!}
				<span class="form-text text-muted">Please select category</span>
			</div>
			<div class="col-md-3 mb-5">
				{!!
					Form::select('product_type',['1'=>'Product','2'=>'Service'],
					isset($data['product_type']) ? $data['product_type'] : null,
					['class' => 'form-control','id'=>'product_type','placeholder'=>'Please select product type'])
				!!}
				<span class="form-text text-muted">Please select product type</span>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-primary products-filter-submit">Submit</button>
				<button type="button" class="btn btn-primary products-filter-reset">Reset</button>
			</div>
			<div class="col-md-4 text-right mb-5">
				@php
					$urlType = $product_type == 1 ? 'products' : 'services';
					if($companyId){
						$url = route('admin.'.$urlType.'.create',encrypt($companyId));
					}else{
						$url = route('admin.master.'.$urlType.'.create');
					}
				@endphp
				@if($companyId)
					<a href="{{ route('admin.clients.index') }}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-back"></i>Back</a>
				@endif
				<a href="{{$url}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Product / Serivce</a>
			</div>
		</div>
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
				@if(empty($companyData))
					<th width="5%"><label><input type="checkbox" value="1" id="selectall" /> Select<br /> All</label></th>
				@endif
					<th width="5%">Type</th>
					<th width="20%">Name</th>
					<th width="10%">SKU Code</th>
					<th width="15%">Category</th>
					<th width="10%">Price</th>
					<th width="10%">Offer Price</th>
					<th width="10%">Unit</th>
					<th width="15%" class="action-header">Actions</th>
				</tr>
			</thead>
			@if(empty($companyData))
			<tfoot>
				<tr>
					<td colspan="9"><div class="row"><div class="col-md-4">{!!
					Form::select('allcompanies',
					array(''),
					'',
					['class' => 'form-control','id'=>'allcompanies','placeholder'=>'Select Company'])
				!!}</div><div class="col-md-8"><button type="button" class="btn btn-primary" id="product_assign_btn" value="Assign Products">Assign Products</button></div></div></td>
				</tr>
			</tfoot>
			@endif
		</table>
		<!--end: Datatable-->
	</div>
	@include('admin.layouts.modal',['modalId'=>'product-delete','content'=>'Are you sure you want to delete product/service ?','title'=>'Delete'])
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
	var table = '';
var tbl = $('#kt_datatable');
function generateDataTable(category=0,product_type=0) {
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
					title: 'Products Services',
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
					title: 'Products Services',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Products Services',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.products.data') !!}'+ '?'+"product_type="+product_type+"&company_id={{$companyId}}&category_id="+category,
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
			@if(empty($companyData))
				{data: 'assign', name: 'assign', orderable: false, searchable: false},
			@endif
				{data: 'product_type', name: 'product_type'},
                {data: 'namelink', name: 'name'},
                {data: 'skucode', name: 'skucode'},
                {data: 'category', name: 'category'},
                {data: 'listprice', name: 'listprice'},
				{data: 'offer_price', name: 'offer_price'},
                {data: 'unit', name: 'unit'},
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
	@if(empty($companyData))
	jQuery('#selectall').click(function(){
		jQuery('.assignproductids').attr('checked',jQuery(this).prop("checked"));
	});
	$('#allcompanies').select2({
	  ajax: {
		url: '{!! route("admin.getallcompanies") !!}',
		dataType: 'json',
		delay: 250,
		data: function (params) {
		  return {
			q: params.term, // search term
			page: params.page
		  };
		},
		processResults: function (data) {
		  return {
			results:  $.map(data, function (item,index) {
				  return {
					  // text: item.name,
					  // id: item.id
					  text: item.company_name+' ('+item.client_data.name+' City:'+item.client_data.city+')',
					  id: item.id
				  }
			  })
		  };
		},
		cache: true
	  },
	  //tags: true,
	  minimumInputLength: 3,
	});
	jQuery(document).on('click','#product_assign_btn',function(e){
		var cId = $('#allcompanies').val();
		if(cId){
		var productids = $('input[name="productids[]"]').map(function(){
				return $(this).val()
		}).get();
		var assignproductids = $('input[name="assignproductids[]"]:checked').map(function(){
			return $(this).val()
		}).get();
			productids = productids.join(",");
			assignproductids = assignproductids.join(",");
				$.ajax({
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:"{{route('admin.products.assign')}}",
					type: 'PATCH',
					data:{company_id:cId, assignids:assignproductids, ids:productids},//,csrf_token:$('meta[name="csrf-token"]').attr('content')
				}).done(function(data){
					//location.reload();
					if(data.success){
					alert('Products are assign successfully');
					}
				}).fail({
					
				});
		}else{
			alert('Please select any company.');
			jQuery('#allcompanies').focus();
		}
	});
	@endif
	jQuery(document).on('click','.product-delete',function(e){
		$('#product-delete').modal('show');
		$('.delete-record').data('id',$(this).data('id'));
	});
	jQuery(document).on('click','.delete-record',function(e){
		var dId = $(this).data('id');
		$.ajax({
			url:"{{URL::to('rkadmin/products/destroy')}}"+'/'+dId,
			dataType: 'json',
		}).done(function(data){
			location.reload();
		}).fail({
		});
	});
	jQuery(document).on('click','.products-filter-reset',function(e){
		table.destroy();
		jQuery("#category_id").val('{{$categoryId}}').trigger('change')
		generateDataTable('{{$categoryId}}');
		jQuery("#product_type").val('');
	});
	jQuery(document).on('click','.products-filter-submit',function(e){
		table.destroy();
		var category_id = jQuery("#category_id").val()
		var product_type = jQuery("#product_type").val()
		generateDataTable(category_id, product_type);
	});
	jQuery("#category_id").val('{{$categoryId}}').trigger('change')
	generateDataTable('{{$categoryId}}')
}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection