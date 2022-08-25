@php
use App\Models\State;
use App\Models\City;
@endphp
<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Primary Information
		</h3>
		
	</div>
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-4">
					{!! Form::label('name', __('Name'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('name',  
						isset($data['name']) ? $data['name'] : old('name'), 
						['class' => 'form-control','required','pattern'=>"^[a-zA-Z 0-9]{0,50}$"]) 
					!!}
					<span class="form-text text-muted">Please enter full name</span>
				</div>
				<div class="col-lg-4">
					{!! Form::label('email', __('Primary Email'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::email('email',
						isset($data['email']) ? $data['email'] : old('email'), 
						['class' => 'form-control','pattern'=>"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$",'required']) 
					!!}
					<span class="form-text text-muted">Please enter primary email</span>
				</div>
				<div class="col-lg-4">
					{!! Form::label('secondary_email', __('Secondary Email'), ['class' => '']) !!}
					{!! 
						Form::email('secondary_email',
						isset($data['secondary_email']) ? $data['secondary_email'] : old('secondary_email'), 
						['class' => 'form-control','pattern'=>"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"]) 
					!!}
					<span class="form-text text-muted">Please enter secondary email</span>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-4">
					{!! Form::label('mobile_no', __('Primary Mobile'), ['class' => 'control-label thin-weight']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('mobile_no',  
						isset($data['mobile_no']) ? $data['mobile_no'] : old('mobile_no'), 
						['class' => 'form-control valid-number','minlength'=>"10",'maxlength'=>"10",'required', 'pattern'=>"[0-9]{10}",'title'=>"Mobile number must be 10 digits"]) 
					!!}
					<span class="form-text text-muted">Please enter primary mobile</span>
				</div>
				<div class="col-lg-4">
					{!! Form::label('secondary_mobile_no', __('Secondary Mobile'), ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('secondary_mobile_no',  
						isset($data['secondary_mobile_no']) ? $data['secondary_mobile_no'] : old('secondary_mobile_no'), 
						['class' => 'form-control valid-number','minlength'=>"10",'maxlength'=>"10", 'pattern'=>"[0-9]{10}",'title'=>"Mobile number must be 10 digits"]) 
					!!}
					<span class="form-text text-muted">Please enter secondary mobile</span>
				</div>
				<div class="col-lg-4">
					{!! Form::label('customFile', __('Picture'), ['class' => 'control-label thin-weight']) !!}
					<div></div>
					<div class="custom-file">
						{!! 
							Form::file('picture', 
							['class' => 'custom-file-input','id'=>'customFile']) 
						!!}
						<label class="custom-file-label" for="customFile">Choose Picture</label>
					</div>

					@if(!empty($client['picture']))
						@if(file_exists(asset('storage/images/'.$client['picture'])))	
							<img src={{asset('storage/images/'.$client['picture'])}} alt="" height="50px" width="100px">
						@endif
					@endif
						
				</div>
			</div>
		</div>
		
</div>
<!--end::Card-->


<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact client_info">
	<div class="card-header">
		<h3 class="card-title">
			Client Contact Information
		</h3>
	</div>
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-6">
					{!! Form::label('address_line_1', __('Address Line 1'), ['class' => '']) !!}
					<div class="input-group">
						{!! 
							Form::text('address_line_1',  
							isset($data['address_line_1']) ? $data['address_line_1'] : old('address_line_1'), 
							['class' => 'form-control']) 
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					<span class="form-text text-muted">Please enter address line 1</span>
				</div>
				<div class="col-lg-6">
					{!! Form::label('address_line_2', __('Address Line 2'), ['class' => '']) !!}
					<div class="input-group">
					{!! 
						Form::text('address_line_2',
						isset($data['address_line_2']) ? $data['address_line_2'] : old('address_line_2'), 
						['class' => 'form-control']) 
					!!}
					<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					<span class="form-text text-muted">Please enter address line 2</span>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3">
					{!! Form::label('country_id-select', __('Country'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!!
						Form::select('cli_country_id',
						$countries,
						isset($client['country_id']) ? $client['country_id'] : ((old('cli_country_id'))?old('cli_country_id'):0),
						['class' => 'form-control ui search selection top right pointing country_id-select country-val searchpicker',
						'id' => 'country_id-select','placeholder'=>'Please select country','data-div'=>'.client_info','data-statepicker'=>'state-drop-down-client','data-statetext'=>'state-textbox-client','data-postcode'=>'postcode-client','data-postpicker'=>'post-drop-down-client','data-posttext'=>'post-textbox-client','required'])
					!!}
					<span class="form-text text-muted">Please select country</span>
				</div>
				@php
					$checkStatePicker =  empty($client) || (!empty($client) && $client->country_id == 101) ? '' : '';
					
					$checkStatePickerAttr =  empty($client) || (!empty($client) && $client->country_id == 101) ? 'required' : '';

					$checkStateText = !empty($checkStatePicker) ? '' : 'd-none';
					$checkStateTextAttr =  empty($checkStatePicker) ? '' : '';
					
					$checkPostPicker =  empty($client) || (!empty($client) && $client->country_id == 101) ? '' : 'd-none';
					$checkPostText = !empty($checkPostPicker) ? '' : 'd-none';
					$checkPostPickerAttr =  empty($client) || (!empty($client) && $client->country_id == 101) ? 'required' : '';
					$checkPostTextAttr =  empty($checkStatePicker) ? '' : '';
				@endphp
				<div class="{{'col-lg-3 state-drop-down-client '.$checkStatePicker}}">
					{!! Form::label('state_id_select', __('State'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!!
						Form::select('cli_state_id',
						$states,
						isset($client['state_id']) ? $client['state_id'] : old('cli_state_id'),
						['class' => 'form-control ui search selection top right pointing state_id_select searchpicker state-drop-down-client-picker',
						'id' => 'state_id_select','data-div'=>'.client_info','placeholder'=>'Please select state',$checkStatePickerAttr])
					!!}
					<span class="form-text text-muted">Please select state</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-client '.$checkStateText}}">
					{!! Form::label('cli_state_name', __('State'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					<div class="input-group">
						{!! 
							Form::text('cli_state_name',  
							isset($client['state_name']) ? $client['state_name'] : old('cli_state_name'), 
							['class' => 'form-control state-textbox-client-text','id'=>'cli_state_name',$checkStateTextAttr]) 
						!!}
					</div>
					<span class="form-text text-muted">Please enter state name</span>
				</div>
				<div class="{{'col-lg-3 state-drop-down-client '.$checkStatePicker}}">
					{!! Form::label('city-select', __('City'), ['class' => '']) !!}
					
					<span class="text-danger">*</span>
					{!!
						Form::select('cli_city',
						isset($cities) ? $cities : array(''=>'Please select city'),
						isset($client['city']) ? $client['city'] : old('cli_city'),
						['class' => 'form-control ui search selection top right pointing city-select searchpicker state-drop-down-client-picker',
						'id' => 'city-select','data-div'=>'.client_info',$checkStatePickerAttr])
					!!}
					<span class="form-text text-muted">Please select city</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-client '.$checkStateText}}">
					{!! Form::label('cli_city_txt', __('City'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('cli_city_txt',  
						isset($client['city']) ? $client['city'] : old('cli_city_txt'), 
						['class' => 'form-control state-textbox-client-text','id'=>'cli_city_txt',$checkStateTextAttr]) 
					!!}
					<span class="form-text text-muted">Please enter city</span>
				</div>
				<div class="{{'col-lg-3 post-drop-down-client '.$checkPostPicker}}">
					{!! Form::label('postcode-select', __('Post Code'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!!
						Form::select('cli_postcode',
						isset($postcode) ? $postcode : array(''=>'Please select post code'),
						isset($client['postcode']) ? $client['postcode'] : old('cli_postcode'),
						['class' => 'form-control ui search selection top right pointing postcode-select searchpicker post-drop-down-client-picker',
						'id' => 'postcode-select',$checkPostPickerAttr])
					!!}
					<span class="form-text text-muted">Please select post code</span>
				</div>
				<div class="{{'col-lg-3 post-textbox-client '.$checkPostText}}">
					{!! Form::label('cli_postcode_txt', __('Post Code'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					<div class="input-group">
					{!! 
						Form::text('cli_postcode_txt',  
						isset($client['postcode']) ? $client['postcode'] : old('cli_postcode_txt'), 
						['class' => 'form-control valid-number postcode-client post-textbox-client-text','id'=>'cli_postcode_txt','maxlength'=>"6",$checkPostTextAttr]) 
					!!}
					<div class="input-group-append"><span class="input-group-text"><i class="la la-bookmark-o"></i></span></div>
					</div>
					<span class="form-text text-muted">Please enter post code</span>
				</div>
				
			</div>
		</div>
</div>
<!--end::Card-->

<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Company Information
		</h3>
	</div>
	<div class="card-body company-form accordion" id="company_form">
		@if(!empty($client))
			@foreach($client->company as $row)
				@php

					$key = $loop->iteration;
					$comapnyClass = $key == 1 ? '' : 'company-data-edit'.$key;
					$companyPrimaryContact = \Helper::getCompanyContact($row->id);
					$user_edit_id = '';
					if($companyPrimaryContact){
						$user_edit_id = $companyPrimaryContact->id;	
					}
					
					$checkCompanyStatePicker = empty($row) || (!empty($row['country_id']) && $row['country_id'] == 101) ? '' : '';

					$checkCompanyStateText = !empty($checkCompanyStatePicker) ? '' : 'd-none';
					
					$checkCompanyStatePickerAttr =  empty($row) || (!empty($row) && $row['country_id'] == 101) ? 'required' : 'required';
					
					$checkStateText = !empty($checkStatePicker) ? '' : 'd-none';

					$checkCompanyStateTextAttr =  empty($checkStatePicker) ? '' : '';
					
					$checkPostPicker =  empty($row) || (!empty($row['country_id']) && $row['country_id'] == 101) ? '' : 'd-none';
					$checkPostText = !empty($checkPostPicker) ? '' : 'd-none';
					
					$checkCompanyPostPickerAttr =  empty($row) || (!empty($row) && $row['country_id'] == 101) ? 'required' : '';
					$checkCompanyPostTextAttr =  empty($checkPostPicker) ? '' : '';
										
				@endphp
				<div class="card {{$comapnyClass}}">		
					<div class="card-header">
						<div class="card-title">
							<h3 class="form_title" data-toggle="collapse" data-target="#collapse_edit{{$row->id}}"><i class="fa fa-angle-right"></i> 
								<span>
									{{$companyPrimaryContact ? 'Primary Contact :': null}}

									@php									
									if($companyPrimaryContact){
									@endphp
										<a href="{{URL::to('rkadmin/employees/'.$user_edit_id.'/edit/')}}" target="_blank" class="link"> 
											{{$companyPrimaryContact ? $companyPrimaryContact->name .'('.$companyPrimaryContact->mobileno.')': null}}
										</a>
									@php
									}else{
										@endphp
											{{$companyPrimaryContact ? $companyPrimaryContact->name .'('.$companyPrimaryContact->mobileno.')': null}}
										@php	
									}
									@endphp

									{{$companyPrimaryContact ? '|': null}} 

									{{isset($row->plan_data) && $row->plan_data['name'] ? 'Plan : '. $row->plan_data['name'] : null}} 
									{{ ($row['company_name']) ? ' Company Name : '.$row['company_name'] : ''}}
								</span>
							</h3>
						</div>
					</div>
						
				<div id="collapse_edit{{$row->id}}" class="collapse {{($key == 1) ? 'show' : ''}}" data-parent="#company_form">
				<div class="card-body">	
				{{--
				@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark btn-sm mt-1 remove-company-edit float-right'}}" data-id="{{$key}}" data-toggle="modal" data-target="#company-row-delete"><i class='fa fa-minus p-0'></i></a>
				@endif
				--}}
                
				<div class="form-group row">
				
					{{Form::hidden('company_data['.$key.'][company_id]',$row->id)}}
					{{Form::hidden('company_data['.$key.'][total_sms]',$row->total_sms)}}
					{{Form::hidden('company_data['.$key.'][total_email]',$row->total_email)}}
					{{Form::hidden('company_data['.$key.'][used_sms]',$row->used_sms)}}
					{{Form::hidden('company_data['.$key.'][used_email]',$row->used_email)}}
					{{Form::hidden('company_data['.$key.'][expiry_date]',$row->expiry_date)}}
					
					<div class="col-lg-4">
					
						{!! Form::label('company_name', __('Company Name'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!! 
							Form::text('company_data['.$key.'][company_name]',  
							isset($row['company_name']) ? $row['company_name'] : null, 
							['class' => 'form-control special-characters','required','pattern'=>"^[a-zA-Z 0-9]{0,100}$"]) 
						!!}
						
						<span class="form-text text-muted">Please enter company name</span>
					</div>
					
					<div class="col-lg-4">
						
						{!! Form::label('company_type_id', __('Company Type'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('company_data['.$key.'][company_type_id]',
							$companytypes,
							$row['company_type_id'],
							['class' => 'form-control ui search selection top right pointing company_type_id-select',
							'id' => 'company_type_id-select','required'])
						!!}
						<span class="form-text text-muted">Please select company type</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('industry_id', __('Industry'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('company_data['.$key.'][industry_id]',
							$industries,
							$row['industry_id'],
							['class' => 'form-control ui search selection top right pointing company_type_id-select searchpicker','required'])
						!!}
						<span class="form-text text-muted">Please select industry type</span>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-lg-4">
						{!! Form::label('gst_no', __('GST Number'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][gst_no]',  
							isset($row['gst_no']) ? $row['gst_no'] : null, 
							['class' => 'form-control','maxlength'=>'15']) 
						!!}
						<span class="form-text text-muted">Please enter GST Number</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('pan_no', __('PAN'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][pan_no]',  
							isset($row['pan_no']) ? $row['pan_no'] : null, 
							['class' => 'form-control','maxlength'=>'10','pattern'=>"[A-Z]{5}[0-9]{4}[A-Z]{1}"]) 
						!!}
						<span class="form-text text-muted">Please enter PAN Number</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('no_of_employees', __('Number of Employees'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][no_of_employees]',  
							isset($row['no_of_employees']) ? $row['no_of_employees'] : null, 
							['class' => 'form-control valid-number','maxlength'=>"4"]) 
						!!}
						<span class="form-text text-muted">Please enter number of employees</span>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-lg-3">
						{!! Form::label('excise_no', __('Excise No'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][excise_no]',  
							isset($row['excise_no']) ? $row['excise_no'] : null, 
							['class' => 'form-control','maxlength'=>'15']) 
						!!}
						<span class="form-text text-muted">Please enter excise number</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('vat_no', __('Vat No'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][vat_no]',  
							isset($row['vat_no']) ? $row['vat_no'] : null, 
							['class' => 'form-control','maxlength'=>'9']) 
						!!}
						<span class="form-text text-muted">Please enter vat number</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('company_license_type', __('Company License Type'), ['class' => '']) !!}
						{!! 
							Form::select('company_data['.$key.'][company_license_type]',$licensetypes,  
							isset($row['company_license_type']) ? $row['company_license_type'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please select company license type</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('license_no', __('Compnay License No'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][company_license_no]',  
							isset($row['company_license_no']) ? $row['company_license_no'] : null, 
							['class' => 'form-control','maxlength'=>'20']) 
						!!}
						<span class="form-text text-muted">Please enter company license number</span>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-lg-3">
						{!! Form::label('website', __('Website'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][website]',  
							isset($row['website']) ? $row['website'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter Website</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('established_in', __('Establish Year'), ['class' => '']) !!}
						{!! 
							Form::select('company_data['.$key.'][established_in]',$establishYears,  
							isset($row['established_in']) ? $row['established_in'] : null, 
							['class' => 'form-control valid-number searchpicker','maxlength'=>"4",'placeholder'=>'Please select establish year']) 
						!!}
						<span class="form-text text-muted">Please select establish year</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('turnover', __('Annual Turnover'), ['class' => '']) !!}
						{!! 
							Form::text('company_data['.$key.'][turnover]',  
							isset($row['turnover']) ? $row['turnover'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter annual turnover</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('company_logo', __('Company Logo'), ['class' => '']) !!}
						<div></div>
						<div class="custom-file">
							{!! 
								Form::file('company_data['.$key.'][company_logo]',  
								['class' => 'custom-file-input']) 
							!!}
							<label class="custom-file-label" for="customFile">Choose Company Logo</label>
						</div>
						@if($row['company_logo'])
							{{Form::hidden('company_data['.$key.'][company_old_logo]',$row['company_logo'])}}
							
							@if(file_exists(asset('storage/images/'.$row['company_logo'])))	
								<img src={{asset('storage/images/'.$row['company_logo'])}} alt="" height="50px" width="100px">
							@endif
						@endif
						
					</div>
				</div>

				<div class="form-group row">
					<div class="col-lg-6">
						{!! Form::label('address_line_1', __('Address Line 1'), ['class' => '']) !!}
					<div class="input-group">
						{!! 
							Form::text('company_data['.$key.'][address_line_1]',  
							isset($row['address_line_1']) ? $row['address_line_1'] : null, 
							['class' => 'form-control']) 
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
						</div>
						<span class="form-text text-muted">Please enter address line 1</span>
					</div>
					<div class="col-lg-6">
						{!! Form::label('address_line_2', __('Address Line 2'), ['class' => '']) !!}
						<div class="input-group">
						{!! 
							Form::text('company_data['.$key.'][address_line_2]',
							isset($row['address_line_2']) ? $row['address_line_2'] : null, 
							['class' => 'form-control']) 
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
						</div>
						<span class="form-text text-muted">Please enter address line 2</span>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-lg-3">
						{!! Form::label('country_id', __('Country'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('company_data['.$key.'][country_id]',
							$countries,
							isset($row['country_id']) ? $row['country_id'] : 101,
							['class' => 'form-control ui search selection top right pointing country_id-select searchpicker country-val','data-statepicker'=>'state-drop-down-company-'.$key,'data-statetext'=>'state-textbox-company-'.$key,'data-postcode'=>'postcode-company-'.$key,'data-postpicker'=>'post-drop-down-company-'.$key,'data-posttext'=>'post-textbox-company-'.$key,'data-div'=>'#collapse_edit'.$row->id, $checkCompanyStatePickerAttr])
						!!}
						<span class="form-text text-muted">Please select country</span>
					</div>
					<div class="{{'col-lg-3 state-drop-down-company-'.$key.' '.$checkCompanyStatePicker}}">
					@php
					
					$states = State::where('country_id',(isset($row['country_id']) ? $row['country_id'] : 101))->orderBy('name', 'ASC')->pluck('name','name');
					$states->prepend('Please select state', '');
					@endphp
						{!! Form::label('state_id', __('State'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('company_data['.$key.'][state_id]',
							$states,
							isset($row['state_id']) ? $row['state_id'] : null,
							['class' => 'form-control ui search selection top right pointing state_id_select searchpicker state-drop-down-company-'.$key.'-picker',$checkCompanyStatePickerAttr,'data-div'=>'#collapse_edit'.$row->id])
						!!}
						<span class="form-text text-muted">Please select state</span>
					</div>
					<div class="{{'col-lg-3 state-textbox-company-'.$key.' '.$checkCompanyStateText}}">
						{!! Form::label('state_name', __('State'), ['class' => '']) !!}
							<span class="text-danger">*</span>
							{!! 
								Form::text('company_data['.$key.'][state_name]',  
								isset($row['state_name']) ? $row['state_name'] : null,
								['class' => 'form-control state-textbox-company-'.$key.'-text',$checkCompanyStateTextAttr]) 
							!!}
						<span class="form-text text-muted">Please enter state name</span>
					</div>
					<div class="{{'col-lg-3 state-drop-down-company-'.$key.' '.$checkCompanyStatePicker}}">
						{!! Form::label('city', __('City'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						@php
						if(isset($row['state_id'])){
								$cities = City::WhereHas('state', function ($q2) use ($row) {
											return $q2->where('name', '=', $row['state_id']);
									})->orderBy('name', 'ASC')->pluck('name','name');
							}elseif(isset($row['state_name'])){
								$cities = City::WhereHas('state', function ($q2) use ($row) {
											return $q2->where('name', '=', $row['state_name']);
									})->orderBy('name', 'ASC')->pluck('name','name');
							}
							$cities->prepend('Please select city', '');
						@endphp
						<?php 
							$company_city = array($row['city']=>ucwords(strtolower($row['city'])));
						?>
						{!!
							Form::select('company_data['.$key.'][city]',
							$cities,
							$company_city,
							['class' => 'form-control ui search selection top right pointing city-select searchpicker state-drop-down-company-'.$key.'-picker',$checkCompanyStatePickerAttr,'data-div'=>'#collapse_edit'.$row->id])
						!!}
						<span class="form-text text-muted">Please select city</span>
					</div>
					<div class="{{'col-lg-3 state-textbox-company-'.$key.' '.$checkCompanyStateText}}">
						{!! Form::label('city', __('City'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!! 
							Form::text('company_data['.$key.'][city_txt]',  
							isset($row['city']) ? $row['city'] : null,
							['class' => 'form-control state-textbox-company-'.$key.'-text',$checkCompanyStateTextAttr]) 
						!!}
						<span class="form-text text-muted">Please enter city</span>
					</div>
					<div class="{{'col-lg-3 post-drop-down-company-'.$key.' '.$checkPostPicker}}">
						{!! Form::label('postcode', __('Post Code'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						<?php 
							$postcode_city = array($row['postcode']=>$row['postcode']);
						?>
						{!!
							Form::select('company_data['.$key.'][postcode]',
							$postcode_city,
							$postcode_city,
							['class' => 'form-control ui search selection top right pointing postcode-select searchpicker post-drop-down-company-'.$key.'-picker',$checkCompanyPostPickerAttr,'data-div'=>'#collapse_edit'.$row->id])
						!!}
						<span class="form-text text-muted">Please select post code</span>
					</div>
					<div class="{{'col-lg-3 post-textbox-company-'.$key.' '.$checkPostText}}">
						{!! Form::label('postcode', __('Post Code'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						<div class="input-group">
						{!! 
							Form::text('company_data['.$key.'][postcode_txt]',  
							isset($row['postcode']) ? $row['postcode'] : null, 
							['class' => 'form-control valid-number postcode-company-'.$key. ' post-textbox-company-'.$key.'-text', 'maxlength'=>"6",$checkCompanyPostTextAttr]) 
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-bookmark-o"></i></span></div>
						</div>
						<span class="form-text text-muted">Please enter post code</span>
					</div>
					</div>
					<div class="form-group row">
					<div class="col-lg-4">
					{!! Form::label('from_email', __('From Email'), ['class' => 'control-label thin-weight']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::email('company_data['.$key.'][from_email]',
						isset($row['from_email']) ? $row['from_email'] : null, 
						['class' => 'form-control','pattern'=>"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$", 'required']) 
					!!}
					<span class="form-text text-muted">Please enter from email</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('from_name', __('From Email Name'), ['class' => 'control-label thin-weight']) !!}
						<span class="text-danger">*</span>
						{!! 
							Form::text('company_data['.$key.'][from_name]',
							isset($row['from_name']) ? $row['from_name'] : null, 
							['class' => 'form-control','maxlength'=>"20",'required']) 
						!!}
						<span class="form-text text-muted">Please enter from email</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('sms_sender_id', __('SMS Sender ID'), ['class' => 'control-label thin-weight']) !!}
						<span class="text-danger">*</span>

						<div class="input-group">
							{!! 
								Form::text('company_data['.$key.'][sms_sender_id]',
								isset($row['sms_sender_id']) ? $row['sms_sender_id'] : null, 
								['class' => 'form-control', 'maxlength'=>"6", 'required']) 
							!!}
							<div class="input-group-append">
								<span class="input-group-text">
									<div class="form-check">
										<input type="checkbox" <?php if($row['send_sms'] == 1){ echo "checked"; }?> class="form-check-input mt-1" name="company_data[<?php echo $key;?>][send_sms]" id="send_sms">
										<label class="form-check-label"  for="send_sms">Checked to send sms</label>
									</div>
								</span>
							</div>
						</div>
						<span class="form-text text-muted">Please enter SMS sender id</span>
					</div>
				</div>
				<div class="form-group row">				
					<div class="col-lg-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="email_service" name="company_data[<?php echo $key;?>][email_service]"  <?php if($row['email_service'] == 1){ echo "checked"; }?>>
							<label class="form-check-label" for="email_service">
								Stop email service
							</label>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="sms_service" name="company_data[<?php echo $key;?>][sms_service]" <?php if($row['sms_service'] == 1){ echo "checked"; }?>>
							<label class="form-check-label" for="sms_service">
								Stop sms service
							</label>
						</div>
					</div>

				</div>
			</div>
			</div>
			</div>
			@endforeach
		@endif
	</div>
	<div class="row">
		<div class="col-md-10"></div>
		<div class="col-md-2">
			<a href="javascript:;" class="btn btn-dark add-company btn-sm" data-id="{{!empty($client) ? count($client->company) + 1 : 1}}"><i class="fa fa-plus"></i>Add Company</a>
			<input name="company_data_id" id="company_data_id" type="hidden">
		</div>		
	</div>
	<br>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-4">
				{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!}
				<a href="{{url('rkadmin/clients')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
			</div>
		</div>
	</div>
</div>
<!--end::Card-->
@include('admin.layouts.modal',['modalId'=>'company-row-delete','content'=>'Are you sure you want to delete company ?','title'=>'Delete'])
@push('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endpush