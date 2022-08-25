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
				<i class="flaticon2-supermarket text-primary"></i>
			</span>
			<h3 class="card-label">Permissions
				@if(!empty($companyId))
					&nbsp; | Client Name : {{$companyData->client_data['name']}} &nbsp; | Company Name : {{$companyData->company_name}}
				@endif
			</h3>
		</div>
		{{-- <div class="card-toolbar">
			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">
				<button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="svg-icon svg-icon-md">
					<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Design/PenAndRuller.svg-->
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<rect x="0" y="0" width="24" height="24" />
							<path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
							<path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
						</g>
					</svg>
					<!--end::Svg Icon-->
				</span>Export</button>
				<!--begin::Dropdown Menu-->
				<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
					<!--begin::Navigation-->
					<ul class="navi flex-column navi-hover py-2">
						<li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Choose an option:</li>
						<li class="navi-item">
							<a href="#" class="navi-link">
								<span class="navi-icon">
									<i class="la la-print"></i>
								</span>
								<span class="navi-text">Print</span>
							</a>
						</li>
						<li class="navi-item">
							<a href="#" class="navi-link">
								<span class="navi-icon">
									<i class="la la-copy"></i>
								</span>
								<span class="navi-text">Copy</span>
							</a>
						</li>
						<li class="navi-item">
							<a href="#" class="navi-link">
								<span class="navi-icon">
									<i class="la la-file-excel-o"></i>
								</span>
								<span class="navi-text">Excel</span>
							</a>
						</li>
						<li class="navi-item">
							<a href="#" class="navi-link">
								<span class="navi-icon">
									<i class="la la-file-text-o"></i>
								</span>
								<span class="navi-text">CSV</span>
							</a>
						</li>
						<li class="navi-item">
							<a href="#" class="navi-link">
								<span class="navi-icon">
									<i class="la la-file-pdf-o"></i>
								</span>
								<span class="navi-text">PDF</span>
							</a>
						</li>
					</ul>
					<!--end::Navigation-->
				</div>
				<!--end::Dropdown Menu-->
			</div>
			<!--end::Dropdown-->
		</div> --}}
	</div>
	<div class="card-body">
		@include('admin.layouts.alert')
		<div class="col-sm-12 d-none error-msg">
			<div class="alert alert-danger alert-block">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Please select minimum one permission</strong>
			</div>
		</div>
		{{-- @if($companyId)
			<div class="row">
				<div class="col-md-12 text-right mb-5">
					<a href="{{url('rkadmin/permissions/create?company_id='.encrypt($companyId))}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-plus"></i>Add Permission</a>
				</div>
			</div>
		@endif --}}
		@if(!empty($roleData))
			{{Form::open(['route'=>'admin.permission.assign','method'=>'POST','class'=>'permission-assign-form'])}}
				<div class="form-group row">
					<div class="col-lg-12">
						<div style="padding: 5px 0;">
							<input type="checkbox" name="tmppermission" class="minimal all-check" id="selectAll">
							<label  for="selectAll" style="font-weight: 600;">Check All</label>
							{{Form::hidden('company_id',$companyId)}}
						</div>
						<hr style="margin: 5px 0;border-top: 1px solid #d7d5d5;"/>
						<div class="row">
							<div class="col-sm-4">
								@php
									$i = 1;
								@endphp
								@foreach($roleData as $k => $value)
									@php
										$valueId = str_replace(" ", "_", $k);
										$permissionCount = count($value);
										$addedPermissionCount = isset($assignedPermssion) ? array_intersect(array_keys($value), $assignedPermssion) : [];
										$addedPermissionCount = count($addedPermissionCount);
										$isChecked = $addedPermissionCount == $permissionCount ? true : false;
									@endphp
									<div style="padding: 5px 0;">
										<input type="checkbox" class="{{'check-item select-all '.$valueId.'-main'}}" data-class="{{$valueId}}" @if($isChecked) checked="checked" @endif>
										<label style="font-weight: 600;">{{$k}}</label>
									</div>
									@foreach ($value as $id=>$item)
										<div class="ml-5" style="padding: 5px 0;">
											<input type="checkbox" name="permission[]" class="{{'check-item sub-permission '.$valueId.' p-'.$i}}" data-id="{{$i}}" data-mainclass="{{$valueId.'-main'}}" value="{{$id}}" @if(isset($assignedPermssion) && in_array($id, $assignedPermssion)) checked="checked" @endif>
											<label style="font-weight: normal;">{{$item}}</label>
										</div>
										@endforeach
										@if(($i + 1)%4 === 0)
											</div><div class="col-sm-4">
										@endif
										@php
											$i++;
										@endphp
								@endforeach
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="row text-center mr-5">
						<div class="col-lg-12">
							<a href="#" class="btn btn-md btn-primary submit">Submit</a>
							<a href="{{url('rkadmin/clients')}}" class="btn btn-md btn-success">Cancel</a>
						</div>
					</div>
				</div>
			{{Form::close()}}
		@else
			<!--begin: Datatable-->
			<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
				<thead>
					<tr>
						<th>Client</th>
						<th>Name</th>
						<th class="action-header">Actions</th>
					</tr>
				</thead>
			</table>
			<!--end: Datatable-->
		@endif
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
		var companyId = "{{$companyId}}";
		"use strict";
		var KTDatatablesDataSourceAjaxServer = {
			init: function () {
				$("#kt_datatable").DataTable({
					dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
"<'row'<'col-sm-12'tr>>" +
"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'i><'col-sm-12 col-md-6'p>><'clear'>",
					sPaginationType: "full_numbers",
					lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, 100, "All"] ],
					processing: true,
					serverSide: true,
					ajax: '{!! route('admin.permissions.data') !!}?company_id='+companyId,
					name:'search',
					drawCallback: function(){
						var length_select = $(".dataTables_length");
						var select = $(".dataTables_length").find("select");
						select.addClass("tablet__select");
					},
					autoWidth: false,
					columns: [
						{data: 'clientname', name: 'clientname'},
						{data: 'namelink', name: 'name'},
						{ data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
					]
				})
			}
		};
		jQuery(document).ready((function () {
			KTDatatablesDataSourceAjaxServer.init();
			jQuery(document).on('click','.submit',function(q){
				$('.error-msg').addClass('d-none');
				var checkedBoxCount = $('.sub-permission:checked').length;
				if(typeof checkedBoxCount != 'undefind' && checkedBoxCount > 0){
					$('.permission-assign-form').submit();
				}else{
					$('.error-msg').removeClass('d-none');
				}
			});
		}));
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection