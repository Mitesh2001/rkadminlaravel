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
				<i class="flaticon-list-2 text-primary"></i>
			</span>
			<h3 class="form_title">Plans</h3>
		</div>
	</div>
	<div class="card-body">
	 @include('admin.layouts.alert')
	 <div class="row">
			<div class="col-md-12 text-right mb-5">
				<a href="{{url('rkadmin/plan/create')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Plan</a>
			</div>
		</div>
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Name</th>
					<th>Price</th>
					<th>No Of Users</th>
					<th>Email</th>
					<th>SMS</th>
					<th>Duration In Months</th>
					<th class="action-header">Actions</th>
				</tr>
			</thead>
		</table>
		<!--end: Datatable-->
	</div>
	@include('admin.layouts.modal',['modalId'=>'plan-delete','content'=>'Are you sure you want to delete plan ?','title'=>'Delete'])
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
	fixNewLine = null;
	var table = '';
	var errorMsg = 'Something went wrong please try again.';
var tbl = $('#kt_datatable');
	function generateDataTable  () {

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
					title: 'Plan',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5]
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
					title: 'Plan',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5]
					}
				}),
				$.extend( true, {}, fixNewLine, {
					extend: 'print',
					title: 'Plan',
					//action: newexportaction,
					exportOptions: {
						columns: [0,1,2,3,4,5]
					}
				})
			],
			processing: true,
            serverSide: true,
            ajax: '{!! route('admin.plan.data') !!}',
			name:'search',
            drawCallback: function(){
                var length_select = $(".dataTables_length");
                var select = $(".dataTables_length").find("select");
                select.addClass("tablet__select");
            },
            autoWidth: false,
            columns: [
				{data: 'name', name: 'name'},
                {data: 'price', name: 'price'},
                {data: 'no_of_users', name: 'no_of_users'},
				{data: 'no_of_email', name: 'no_of_email'},
				{data: 'no_of_sms', name: 'no_of_sms'},
                {data: 'duration_months', name: 'duration_months'},
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
	generateDataTable()

	// jQuery(document).on('click','#plan_delete',function(e){
	// 	var id = jQuery(this).data('id');
	// 	if(id==''){
	// 		Swal.fire(
	// 			'Error!',
	// 			errorMsg,
	// 			'error'
	// 			)
	// 		return false	
	// 	}

	// 	Swal.fire({
	// 		title: 'Are you sure?',
	// 		text: "You won't be able to revert this!",
	// 		icon: 'warning',
	// 		showCancelButton: true,
	// 		confirmButtonColor: '#3085d6',
	// 		cancelButtonColor: '#d33',
	// 		confirmButtonText: 'Yes, delete it!'
	// 	}).then((result) => {
	// 		if (result.isConfirmed) {
	// 			jQuery.ajax({
	// 			url: '{!! URL::to("/rkadmin/plan/plan-delete/") !!}/'+id,
	// 			type: 'GET',
	// 			data: { id:id },
	// 			dataType: 'json',
	// 			success: function(response){
	// 				if(response.status === true){
	// 					Swal.fire(
	// 					'Deleted!',
	// 					'Your plan has been deleted.',
	// 					'success'
	// 					)
	// 					table.destroy()
	// 					generateDataTable()
	// 				}else{
	// 					Swal.fire(
	// 					'Error!',
	// 					errorMsg,
	// 					'error'
	// 					)
	// 				}
	// 			}
	// 		});
	// 		}
	// 	})

	// });
	
	jQuery(document).on('click','.plan-delete',function(e){
		$('#plan-delete').modal('show');
		$('.delete-record').data('id',$(this).data('id'));
	});	
	jQuery(document).on('click','.delete-record',function(e){
		var dId = $(this).data('id');
		$.ajax({
			url:"{{URL::to('rkadmin/plan/delete')}}"+'/'+dId,
			type:'GET',
			dataType: 'json',
		}).done(function(data){
			location.reload();
		}).fail({

		});
	});

}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection