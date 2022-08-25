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
				<i class="fas fa-users text-primary"></i>
			</span>
			<h3 class="form_title">Clients</h3>
		</div>
	</div>
	<div class="card-body">
	{{Form::open(['class'=>'client-filter-form'])}}
		<div class="row mb-8">
			<div class="col-lg-3 col-md-9 col-sm-12">
				<div class="input-group" id="kt_daterangepicker_2">
					{{Form::text('plan_exp_date','',['class'=>'form-control exp-date','readonly','placeholder'=>'Please select plan expiry date'])}}
					<div class="input-group-append">
						<span class="input-group-text">
							<i class="la la-calendar-check-o"></i>
						</span>
					</div>
				</div>
				<span class="form-text text-muted ml-2">Please select plan expiry date</span>
			</div>
			<div class="col-md-3">
				{{Form::select('client_id',$clientName,'',['class'=>'form-control client-id searchpicker','placeholder'=>'Please select client name'])}}
				<span class="form-text text-muted ml-2">Please select client name</span>
			</div>
			<div class="col-md-3">
				{{Form::select('company_name',$companyName,'',['class'=>'form-control company-id searchpicker','placeholder'=>'Please select company name'])}}
				<span class="form-text text-muted ml-2">Please select company name</span>
			</div>
			<div class="col-md-3">
				{{Form::select('plan',$plan,'',['class'=>'form-control plan searchpicker','placeholder'=>'Please select plan'])}}
				<span class="form-text text-muted ml-2">Please select plan</span>
			</div>
			</div>
			<div class="row mb-8">
			<div class="col-lg-3">
				{!!
					Form::select('cli_country_id',
					$countries,
					((old('cli_country_id'))?old('cli_country_id'):0),
					['class' => 'form-control ui search selection top right pointing country_id-select country-val searchpicker',
					'id' => 'country_id-select','placeholder'=>'Please select country','data-div'=>'.client-filter-form','data-statepicker'=>'state-drop-down-client','data-statetext'=>'state-textbox-client','data-postcode'=>'postcode-client'])
				!!}
				<span class="form-text text-muted">Please select country</span>
			</div>
			@php
					$checkStatePicker =  empty($client) || (!empty($client) && $client->country_id == 101) ? '' : '';
					
					$checkStatePickerAttr =  empty($client) || (!empty($client) && $client->country_id == 101) ? 'required' : 'required';

					$checkStateText = !empty($checkStatePicker) ? 'd-none' : 'd-none';
					$checkStateTextAttr =  empty($checkStatePicker) ? '' : '';
				@endphp
				<div class="{{'col-lg-3 state-drop-down-client '.$checkStatePicker}}">
					{!!
						Form::select('cli_state_id',
						$states,
						old('cli_state_id'),
						['class' => 'form-control ui search selection top right pointing state_id_select searchpicker',
						'id' => 'state_id_select','data-div'=>'.client-filter-form','placeholder'=>'Please select state',$checkStatePickerAttr])
					!!}
					<span class="form-text text-muted">Please select state</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-client '.$checkStateText}}">
						{!! 
							Form::text('cli_state_name',  
							old('cli_state_name'), 
							['class' => 'form-control state-textbox-client-text','id'=>'cli_state_name',$checkStateTextAttr]) 
						!!}
					
					<span class="form-text text-muted">Please enter state name</span>
				</div>
				<div class="{{'col-lg-3 state-drop-down-client '.$checkStatePicker}}">
					{!!
						Form::select('cli_city',
						array(),
						old('cli_city'),
						['class' => 'form-control ui search selection top right pointing city-select searchpicker',
						'id' => 'city-select','placeholder'=>'Please select city','data-div'=>'.client-filter-form',$checkStatePickerAttr])
					!!}
					<span class="form-text text-muted">Please select city</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-client '.$checkStateText}}">
					{!! 
						Form::text('cli_city_txt',  
						old('cli_city_txt'), 
						['class' => 'form-control','id'=>'cli_city_txt']) 
					!!}
					<span class="form-text text-muted">Please enter city</span>
				</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-primary client-filter-submit">Submit</button>
				<button type="button" class="btn btn-primary client-filter-reset">Reset</button>
			</div>
		</div>
	{{Form::close()}}	
	 @include('admin.layouts.alert')
		<!--begin: Datatable-->
		<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
					<th>Client ID</th>
					<th>Name</th>
					<th>Company Name</th>
					<th>Plan Name</th>
					<th>Plan Price (Inc.)</th>
					<th>Plan Expiry Date</th>
					<th>City</th>					
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
    <script src="{{ asset('js//pages/crud/forms/widgets/bootstrap-daterangepicker.js?v=7.2.6') }}" type="text/javascript"></script>
    {{-- page scripts --}}
    <script>
//var tbl = $('#kt_datatable');
		"use strict";
		var formData = '';
		var clientId = '';
		var companyId = '';
		var plan = '';
		var date = '';
		var table = '';
		var country_id = '';
		var state_id = '';
		var state_name = '';
		var city_id = '';
		var city_txt = '';

		function generateDataTable()
		{
			companyId = $('.company-id').val();
			clientId = $('.client-id').val();
			plan = $('.plan').val();
			date = $('.exp-date').val();
			country_id = $('#country_id-select').val();
			state_id = $('#state_id_select').val();
			state_name = $('#cli_state_name').val();
			city_id = $('#city-select').val();
			city_txt = $('#cli_city_txt').val();
			
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
						title: 'Clients',
						//action: newexportaction,
						exportOptions: {
							modifier : {
								 order : 'applied', // 'current', 'applied','index', 'original'
								 page : 'current', // 'all', 'current'
								 search : 'applied', // 'none', 'applied', 'removed'
							},
							columns: [0,1,2,3,4,5,6]
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
						title: 'Clients',
						//action: newexportaction,
						exportOptions: {
							columns: [0,1,2,3,4,5,6]
						}
					}),
					$.extend( true, {}, fixNewLine, {
						extend: 'print',
						title: 'Clients',
						//action: newexportaction,
						exportOptions: {
							columns: [0,1,2,3,4,5,6]
						}
					})
				],
				processing: true,
				serverSide: true,
				ajax: {
						url: '{!! route('admin.clients.data') !!}',
						data: function(data) {data.client_id=clientId,data.company_id=companyId,data.plan=plan,data.date=date,data.country_id=country_id,data.state_id=state_id,data.state_name=state_name,data.city_id=city_id,data.city_txt=city_txt;} 
					},
				name:'search',
				drawCallback: function(){
					var length_select = $(".dataTables_length");
					var select = $(".dataTables_length").find("select");
					select.addClass("tablet__select");
				},
				autoWidth: false,
				columns: [
					{data: 'client_uid', name: 'client_uid'},
					{data: 'namelink', name: 'name'},
					{data: 'company_name', name: 'company_name'},					
					{data: 'plan_name', name: 'plan_name'},
					{data: 'final_amount', name: 'final_amount'},
					{data: 'expiry_date', name: 'expiry_date'},
					{data: 'city', name: 'city'},
					{data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
				]
			});
		}
		
		jQuery(document).ready(function () {
			generateDataTable();
			jQuery(document).on('click','.client-filter-submit',function(){
				table.destroy();
				generateDataTable();
			});
			jQuery(document).on('click','.client-filter-reset',function(){
				table.destroy();
				$(".company-id, .plan, .client-id, .exp-date, .state_id_select, .city-select").val(null).trigger('change');
				$('.country_id-select').val(101).trigger('change');
				$('.state-drop-down-client').removeClass('d-none');
				$('.state-drop-down-client').removeClass('d-none');
				if(!$('.state-textbox-client').hasClass('d-none'))
					$('.state-drop-down-client').addClass('d-none');
				$('#cli_state_name, #cli_city_txt').val(null).trigger('change');
				generateDataTable();
			});
			$(document).on('click','.export-btn',function(){
				var dId = $(this).data('id');
				exportData(clientId,companyId,plan,date,dId);
			});
			$('[data-toggle="tooltip"]').tooltip();
			
			jQuery(document).on('change','.country_id-select',function(){
            var country_id_select = $(this).val();
            var parent_div = jQuery(this).data('div');
            jQuery(parent_div+' .state_id_select').select2({allowClear: true,placeholder: "Please select state"}).empty();
                jQuery.ajax({
                    url: '{!! route("admin.getstate") !!}',
                    data: {country_id:country_id_select},
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
            
                        if(response.success){
                            jQuery(parent_div+' .state_id_select').select2({
                                placeholder: 'Please select state',
                                data: response.states
                            }).trigger('change')						
                        }
                    }
                });
        });
			
			jQuery(document).on('change','.state_id_select',function(){
				var state_id_select = $(this).val();
				var parent_div = jQuery(this).data('div');
				jQuery('.city-select').select2({allowClear: true,placeholder: "Please select city"}).empty();
					jQuery.ajax({
						url: '{!! route("admin.getcity") !!}',
						data: {state_name:state_id_select},
						type: 'get',
						dataType: 'json',
						success: function(response){
							if(response.success){
								jQuery('.city-select').select2({
									placeholder: 'Please select city',
									data: response.cities
								}).trigger('change')
							}
						}
					});
			}); 
		});

		function exportData(clientId,companyId,plan,date,dId){
			window.location.href = "{{URL::to('rkadmin/clients/export/data?')}}"+'client_id='+clientId+'&company_id='+companyId+'&plan='+plan+'&date='+date+'&type='+dId;
		}
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection