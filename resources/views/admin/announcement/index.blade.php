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
				<i class="fa fa-bullhorn text-primary"></i>
			</span>
			<h3 class="form_title">Announcement</h3>
		</div>
	</div>
	<div class="card-body">
	 @include('admin.layouts.alert')
	 	<div class="row">
			<div class="col-md-12 text-right mb-5">
				<a href="{{url('rkadmin/announcement/create')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Announcement</a>
			</div>
		</div>
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Announcement</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Created By</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table>
		<!--end: Datatable-->
	</div>
	@include('admin.layouts.modal',['modalId'=>'announcement-delete','content'=>'Are you sure you want to delete announcement ?','title'=>'Delete'])
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
					title: 'Announcement',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3]
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
					title: 'Announcement',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Announcement',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.announcement.index') !!}',
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'announcement', name: 'announcement'},
                {data: 'start_date_time', name: 'start_date_time'},
				{data: 'end_date_time', name: 'end_date_time'},
                {data: 'craetedBy', name: 'craetedBy'},
                {data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
            ]
		})
	}
};
jQuery(document).ready((function () {
	KTDatatablesDataSourceAjaxServer.init();

	jQuery(document).on('click','.announcement-delete',function(e){
		$('#announcement-delete').modal('show');
		$('.delete-record').data('id',$(this).data('id'));
	});	
	jQuery(document).on('click','.delete-record',function(e){
		var dId = $(this).data('id');
		$.ajax({
			url:"{{URL::to('rkadmin/announcement/delete')}}"+'/'+dId,
			type:'GET',
			dataType: 'json',
		}).done(function(data){
			location.reload();
		}).fail({

		});
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