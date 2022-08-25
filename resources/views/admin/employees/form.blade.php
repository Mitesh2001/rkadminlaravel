<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Personal Information
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			{{Form::hidden('company_id',$companyId)}}
			{{Form::hidden('isProfile',$isProfile)}}
			@php
				if(!$isProfile){
				if($companyId==0){ 
				@endphp
					<div class="col-lg-4">
						{!! Form::label('user_type', __('User Type'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('type',
							$usersType,
							isset($data['type']) ? $data['type'] : old('type'),
							['required'=>"required",'class' => 'form-control','id'=>'user_type','placeholder'=>'Please select user type'])
						!!}
						<span class="form-text text-muted">Please select user type</span>
					</div>
				@php
					}
				@endphp

				@php
					if($companyId){ 
				@endphp
					<div class="col-lg-4">
					{!! Form::label('company_contact_type', __('Company Contact Type'), ['class' => '']) !!}
					{!!
						Form::select('company_contact_type',
						['1'=>'Primary','2'=>'Secondary','3'=>'Alternate'],
						isset($data['company_contact_type']) ? $data['company_contact_type'] : old('company_contact_type'),
						['class' => 'form-control','placeholder'=>'Please select company contact type'])
					!!}
					<span class="form-text text-muted">Please select company contact type</span>
				</div>	
				@php
				}}
				@endphp
			
			<div class="col-lg-4">
				{!! Form::label('name', __('Name'), ['class' => '']) !!}
				<span class="text-danger">*</span>
				{!!
					Form::text('name',
					isset($data['name']) ? $data['name'] : old('name'),
					['class' => 'form-control special-characters','required'=>"required",'pattern'=>"^[a-zA-Z 0-9]{0,50}$", 'title' => 'Name can not allowed special character.','maxlength'=>'50'])
				!!}
				<span class="form-text text-muted">Please enter full name</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('email', __('Email'), ['class' => '']) !!}
				<span class="text-danger">*</span>
				{!!
					Form::email('email',
					isset($data['email']) ? $data['email'] : old('email'),
					['class' => 'form-control','pattern'=>"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$",'required'=>"required",'maxlength'=>'50'])
				!!}
				<span class="form-text text-muted">Please enter email</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('mobileno', __('Mobile'), ['class' => 'control-label thin-weight']) !!}
				<span class="text-danger">*</span>
				{!!
					Form::text('mobileno',
					isset($data['mobileno']) ? $data['mobileno'] : old('mobileno'),
					['class' => 'form-control valid-number','minlength'=>'10','maxlength'=>"10",'required'=>"required",'pattern'=>"[0-9]{10}",'title'=>"Mobile number must be 10 digits"])
				!!}
				<span class="form-text text-muted">Please enter mobile</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('alt_mobileno', __('Additional Contact No'), ['class' => 'control-label thin-weight']) !!}
				{!!
					Form::text('alt_mobileno',
					isset($data['alt_mobileno']) ? $data['alt_mobileno'] : old('alt_mobileno'),
					['class' => 'form-control valid-number','pattern'=>"[0-9]{10}",'minlength'=>'10','maxlength'=>"10",'title'=>"Additional Contact No number must be 10 digits"])
				!!}
				<span class="form-text text-muted">Please enter additional contact no</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('designation', __('Designation'), ['class' => 'control-label thin-weight']) !!}
				{!!
					Form::text('designation',
					isset($data['designation']) ? $data['designation'] : old('designation'),
					['class' => 'form-control','maxlength'=>"15"])
				!!}
				<span class="form-text text-muted">Please enter designation</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4"><?php $gender = array(''=>'Select Gender','F'=>'Female','M'=>'Male','O'=>'Other');?>
				{!! Form::label('gender-select', __('Gender'), ['class' => 'control-label thin-weight']) !!}
				{!!
					Form::select('gender',
					$gender,
					isset($data['gender']) ? $data['gender'] : old('gender'),
					['class' => 'form-control ui search selection top right pointing gender-select',
					'id' => 'gender-select'])
				!!}
				<span class="form-text text-muted">Please enter gender</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('date_of_birth', __('Date Of Birth'), ['class' => '']) !!}
				{!!
					Form::date('date_of_birth',
					isset($employee['dob']) ? $employee['dob'] : old('date_of_birth'),
					['class' => 'form-control','max'=>date('Y-m-d')])
				!!}
				<span class="form-text text-muted">Please select date of birth</span>
			</div>
			<?php
			$password_req = 'required="required"';
			$password_req_s = '*';  
			if(isset($employee)){
				$password_req = '';
				$password_req_s = ''; 
			}
			?>
			<div class="col-lg-4">
				{!! Form::label('password', __('Password'), ['class' => 'control-label thin-weight']) !!}
				<span class="text-danger">{{$password_req_s}}</span>
				{!!
					Form::password('password',
					['class' => 'form-control txt-password','pattern'=>"(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?^)-+_^(&*]).{8,20}$",'title' => 'The password format is invalid','maxlength'=>'20','minlength'=>'8', 'title' => 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.',$password_req])
				!!}
				<i class="far fa-eye" id="toggle-password"></i>
				<span class="form-text text-muted">Please enter password</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('cast_name', __('Cast'), ['class' => '']) !!}
				{!!
					Form::select('cast_name',
					$cast,
					isset($data['cast_name']) ? $data['cast_name'] : old('cast_name'),
					['class' => 'form-control'])
				!!}
				<!-- ui search selection top right pointing searchpicker-->
				<span class="form-text text-muted">Please select cast</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('marital_status', __('Marital Status'), ['class' => '']) !!}
				{!!
					Form::select('marital_status',['1'=>'Married','2'=>'Unmarried'],
					isset($data['marital_status']) ? $data['marital_status'] : old('marital_status'),
					['class' => 'form-control','placeholder'=>'Select Marital Status'])
				!!}
				<span class="form-text text-muted">Please select marital status</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('blood_group', __('Blood Group'), ['class' => '']) !!}
				{!!
					Form::select('blood_group',$bloodGroup,
					isset($data['blood_group']) ? $data['blood_group'] : old('blood_group'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please select blood group</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('critical_illness', __('Critical Illness'), ['class' => '']) !!}
				{!!
					Form::text('critical_illness',
					isset($data['critical_illness']) ? $data['critical_illness'] : old('critical_illness'),
					['class' => 'form-control ui search selection top right pointing'])
				!!}
				<span class="form-text text-muted">Please enter critical illness</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('legal_issue', __('Legal Issue'), ['class' => '']) !!}
				{!!
					Form::text('legal_issue',
					isset($data['legal_issue']) ? $data['legal_issue'] : old('legal_issue'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter legal issue</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('other_activity', __('Other Activity'), ['class' => '']) !!}
				{!!
					Form::text('other_activity',
					isset($data['other_activity']) ? $data['other_activity'] : old('other_activity'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter other activity</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('emergency_no', __('Emergency Contact Number'), ['class' => '']) !!}
				{!!
					Form::text('emergency_no',
					isset($data['emergency_no']) ? $data['emergency_no'] : old('emergency_no'),
					['class' => 'form-control valid-number', 'pattern'=>"^[0-9]{0,10}$",'minlength'=>'10','maxlength'=>'10','title'=>"Emergency contact number must be 10 digits"])
				!!}
				<span class="form-text text-muted">Please enter emergency contact number</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('marriage_anniversary_date', __('Marriage Anniversary Date'), ['class' => '']) !!}
				<span class="text-danger marriage_anniversary_required" style="display:none">*</span>
				{!!
					Form::date('marriage_anniversary_date',
					isset($data['marriage_anniversary_date']) ? $data['marriage_anniversary_date'] : old('marriage_anniversary_date'),
					['class' => 'form-control','max'=>date('Y-m-d')])
				!!}
				<span class="form-text text-muted">Please select marriage anniversary date</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('driving_licence_no', __('Driving Licence Number'), ['class' => '']) !!}
				{!!
					Form::text('driving_licence_no',
					isset($data['driving_licence_no']) ? $data['driving_licence_no'] : old('driving_licence_no'),
					['class' => 'form-control','pattern'=>"^(([A-Z]{2}[0-9]{2})( )|([A-Z]{2}-[0-9]{2}))((19|20)[0-9][0-9])[0-9]{7}$",'minlength'=>'16' ,'maxlength'=>'16','title'=>'The driving licence no format is invalid'])
				!!}
				<span class="form-text text-muted">Please enter driving licence number</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('aadhar_no', __('Aadhar Number'), ['class' => '']) !!}
				{!!
					Form::text('aadhar_no',
					isset($data['aadhar_no']) ? $data['aadhar_no'] : old('aadhar_no'),
					['class' => 'form-control valid-number', 'pattern'=>"^[0-9]{0,12}$",'minlength'=>'12','maxlength'=>'12', 'title' => 'The aadhar number format is invalid'])
				!!}
				<span class="form-text text-muted">Please enter aadhar number</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('pan_no', __('Pan Number'), ['class' => '']) !!}
				{!!
					Form::text('pan_no',
					isset($data['pan_no']) ? $data['pan_no'] : old('pan_no'),
					['class' => 'form-control','minlength'=>'10','maxlength'=>'10','pattern'=>"^[A-Z]{5}[0-9]{4}[A-Z]{1}$",'title' => 'The pan number format is invalid'])
				!!}
				<span class="form-text text-muted">Please enter pan number</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('picture', __('Profile Picture'), ['class' => 'control-label thin-weight']) !!}
				<div class="custom-file">
				{!!
					Form::file('picture',
					['class' => 'custom-file-input'])
				!!}
				<label class="custom-file-label" for="picture">Choose Profile Picture</label>
				</div>
					<div id="preview_profile_image" class="symbol symbol-100 mt-2"@if(empty($employee) || $employee['picture']=='' || !file_exists( public_path().'/storage/images/'.$employee['picture'] )) style="display:none" @endif>
						<div class="symbol-label" id="show_profile_image" @if(!empty($employee) && $employee['picture']!='' && file_exists( public_path().'/storage/images/'.$employee['picture'] ))style="background-image:url('{{ asset('storage/images/'.$employee['picture']) }} ')" @endif></div>
					</div>
			</div>
		</div>
	</div>
</div>
<!--end::Card-->

<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact commission_info" style="<?php if(!empty($employee) and ($employee->type == 3 || $employee->type == 4)){ echo 'display: block'; }else{ echo 'display: none'; }?>">
	<div class="card-header">
		<h3 class="card-title">
			Commission	
		</h3>
	</div>
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-4">
					{!! Form::label('commission', __('Commission'), ['class' => '']) !!}
					<span class="text-danger commission_required">*</span>
					<div class="input-group">
						{!!
							Form::number('commission',
							isset($data['commission']) ? $data['commission'] : old('commission'),
							['class' => 'form-control','max'=>'100', 'min'=>'0', 'step'=>'.01'])
						!!}
						<div class="input-group-append">
							<span class="input-group-text"><i class="la la-percent"></i></span>
						</div>
					</div>	
					<span class="form-text text-muted">Please enter commission</span>
				</div>
			</div>
		</div>
</div>
<!--end::Card-->

<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Contact Information
		</h3>
	</div>
		@php
			$checkStatePicker = empty($employee) || (!empty($employee) && $employee->country_id == 101)  ? '' : '';
			$checkStateText = !empty($checkStatePicker) ? '' : 'd-none';
			$checkPostPicker =  empty($employee) || (!empty($employee) && $employee->country_id == 101) ? '' : 'd-none';
			$checkPostText = !empty($checkPostPicker) ? '' : 'd-none';
		@endphp
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-4">
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
				<div class="col-lg-4">
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
				<div class="col-lg-4">
					{!! Form::label('landmark', __('Landmark'), ['class' => '']) !!}
					<div class="input-group">
					{!! 
						Form::text('landmark',
						isset($data['landmark']) ? $data['landmark'] : old('landmark'), 
						['class' => 'form-control']) 
					!!}
					<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					<span class="form-text text-muted">Please enter landmark</span>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3">
					{!! Form::label('country_id', __('Country'), ['class' => '']) !!}
					{!!
						Form::select('country_id',
						$countries,
						isset($employee['country_id']) ? $employee['country_id'] : 0,
						['class' => 'form-control ui search selection top right pointing country_id-select country-val searchpicker',
						'id' => 'country_id-select','data-statepicker'=>'state-drop-down-employee','data-statetext'=>'state-textbox-employee','data-postpicker'=>'post-drop-down-employee','data-posttext'=>'post-textbox-employee','data-postcode'=>'postcode-employee'])
					!!}
					<span class="form-text text-muted">Please select country</span>
				</div>
				<div class="{{'col-lg-3 state-drop-down-employee '.$checkStatePicker}}">
					{!! Form::label('state_id_select', __('State'), ['class' => '']) !!}
					{!!
						Form::select('state_id',
						$states,
						isset($data['state_id']) ? $data['state_id'] : old('state_id'),
						['class' => 'form-control ui search selection top right pointing state_id_select searchpicker',
						'id' => 'state_id_select','placeholder'=>'Please select state'])
					!!}
					<span class="form-text text-muted">Please select state</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-employee '.$checkStateText}}">
					{!! Form::label('state_name', __('State'), ['class' => '']) !!}
					<div class="input-group">
						{!! 
							Form::text('state_name',  
							isset($data['state_name']) ? $data['state_name'] : old('state_name'), 
							['class' => 'form-control']) 
						!!}
					</div>
					<span class="form-text text-muted">Please enter state name</span>
				</div>
				
				<div class="{{'col-lg-3 state-drop-down-employee '.$checkStatePicker}}">
					{!! Form::label('city-select', __('City'), ['class' => '']) !!}
					{!!
						Form::select('city',
						[isset($employee['city']) ? $employee['city'] : 'Please select city'],
						isset($data['city']) ? $data['city'] : old('city'),
						['class' => 'form-control ui search selection top right pointing city-select searchpicker',
						'id' => 'city-select'])
					!!}
					<span class="form-text text-muted">Please select city</span>
				</div>
				<div class="{{'col-lg-3 state-textbox-employee '.$checkStateText}}">
					{!! Form::label('city_txt', __('City'), ['class' => '']) !!}
					{!!
						Form::text('city_txt',
						isset($employee['city']) ? $employee['city'] : old('city_txt'),
						['class' => 'form-control'])
					!!}
					<span class="form-text text-muted">Please enter city</span>
				</div>
				<div class="{{'col-lg-3 post-drop-down-employee '.$checkPostPicker}}">
					{!! Form::label('pincode-select', __('Post Code'), ['class' => '']) !!}
					{!!
						Form::select('pincode',
						[isset($employee['pincode']) ? $employee['pincode'] : 'Please select post code'],
						isset($data['pincode']) ? $data['pincode'] : old('pincode'),
						['class' => 'form-control ui search selection top right pointing pincode-select searchpicker',
						'id' => 'pincode-select'])
					!!}
					<span class="form-text text-muted">Please select post code</span>
				</div>
				<div class="{{'col-lg-3 post-textbox-employee '.$checkPostText}}">
					{!! Form::label('pincode_txt', __('Post Code'), ['class' => '']) !!}
					<div class="input-group">
					{!!
						Form::text('pincode_txt',
						isset($employee['pincode']) ? $employee['pincode'] : old('pincode_txt'),
						['class' => 'form-control valid-number postcode-employee','minlength'=>'5','maxlength'=>"6"])
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
@if(!$isProfile)
@if($companyId)
<div class="card card-custom gutter-b example example-compact role_info">
	<div class="card-header">
		<h3 class="card-title">
			Role Information
		</h3>
	</div>
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-4">
					{!! Form::label('role_id', __('Role'), ['class' => '']) !!}
					{!!
						Form::select('role_id',
						$roleslist,
						(isset($role[0]))?$role[0]:old('role_id'),
						['class' => 'form-control ui search selection top right pointing role_id-select searchpicker',
						'id' => 'role_id-select'])
					!!}
					<span class="form-text text-muted">Please select role</span>
				</div>
			</div>
		</div>
</div>
<!--end::Card-->
@endif
@endif
{{-- Education Detail --}}
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Education Detail
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('high_school', __('High School'), ['class' => '']) !!}
				{!!
					Form::text('education_data[0][high_school]',
					!empty($educationData[0]->high_school) ? $educationData[0]->high_school : old('education_data[0][high_school]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter high school</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('graduate', __('Graduate'), ['class' => '']) !!}
				{!!
					Form::text('education_data[0][graduate]',
					!empty($educationData[0]->graduate) ? $educationData[0]->graduate : old('education_data[0][graduate]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter graduate</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('post_graduate', __('Post Graduate'), ['class' => '']) !!}
				{!!
					Form::text('education_data[0][post_graduate]',
					!empty($educationData[0]->post_graduate) ? $educationData[0]->post_graduate : old('education_data[0][post_graduate]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter post graduate</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				{!! Form::label('other', __('Other'), ['class' => '']) !!}
				{!!
					Form::text('education_data[0][other]',
					!empty($educationData[0]->other) ? $educationData[0]->other : old('education_data[0][other]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter other details</span>
			</div>
			<div class="col-lg-6">
				{!! Form::label('special_skill_and_training', __('Special Skill & Training'), ['class' => '']) !!}
				{!!
					Form::text('education_data[0][special_skill_and_training]',
					!empty($educationData[0]->special_skill_and_training) ? $educationData[0]->special_skill_and_training : old('education_data[0][special_skill_and_training]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter special skill & training</span>
			</div>
		</div>
	</div>
</div>

{{-- Family Details --}}
<?php /* if(empty($familyData))
		if(isset($_POST['family_detail'])){
		$familyData = $_POST['family_detail'];
		} */
	?>
	 
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Family Details
		</h3>
		<div class="col-md-1 flot-right">
			<a href="javascript:;" class="btn btn-sm btn-dark mt-5 add-family-btn" data-id="{{!empty($familyData) ? count((array)$familyData) + 1 : ((old('family_detail'))?count((old('family_detail'))):1)}}"><i class="fa fa-plus p-0"></i></a>
		</div>
	</div>
	<div class="card-body family-data">
	
		@if(!empty($familyData))
			@foreach ($familyData as $key=>$row)
				@php
					$key = $loop->iteration;
					$familyClass = $key == 1 ? '' : 'family-data-'.$key;
				@endphp
				@if($key != 1)
					<br class="{{'family-data-'.$key}}">
					<hr class="{{'family-data-'.$key}}">
					
					<br class="{{'family-data-'.$key}}">
					<br class="{{'family-data-'.$key}}">
				@endif
				<div class="{{'form-group row '.$familyClass}}">
					<div class="col-lg-4">
						{!! Form::label('family_member_name', __('Family Member Name'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][family_member_name]',
							!empty($row->family_member_name) ? $row->family_member_name : old('family_detail['.$key.'][family_member_name]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter family member name</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('relation', __('Relation'), ['class' => '']) !!}
						{!!
							Form::select('family_detail['.$key.'][relation]',$relations,
							!empty($row->relation) ? $row->relation : old('family_detail['.$key.'][relation]'),
							['class' => 'form-control searchpicker'])
						!!}
						<span class="form-text text-muted">Please select relation</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('dob', __('Date Of Birth'), ['class' => '']) !!}
						{!!
							Form::date('family_detail['.$key.'][date_of_birth]',
							!empty($row->date_of_birth) ? $row->date_of_birth : old('family_detail['.$key.'][date_of_birth]'),
							['class' => 'form-control','max'=>date('Y-m-d')])
						!!}
						<span class="form-text text-muted">Please enter date of birth</span>
					</div>
					<div class="col-lg-1">
					@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark btn-sm mt-5 remove-family float-right family-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus p-0'></i></a>
					@endif
					</div>
				</div>	
				<div class="{{'form-group row '.$familyClass}}">
					<div class="col-lg-6">
						{!! Form::label('education', __('Education'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][education]',
							!empty($row->education) ? $row->education : old('family_detail['.$key.'][education]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter education</span>
					</div>
					<div class="col-lg-5">
						{!! Form::label('occupation', __('Occupation'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][occupation]',
							!empty($row->occupation) ? $row->occupation : old('family_detail['.$key.'][occupation]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter occupation</span>
					</div>
					<div class="col-lg-1">
					</div>
				</div>
			@endforeach
		@endif
		
		@if(old('family_detail'))
		@foreach(old('family_detail') as $key=>$row)
			@php
					$key = $loop->iteration;
					$familyClass = $key == 1 ? '' : 'family-data-'.$key;
				@endphp
				@if($key != 1)
					<br class="{{'family-data-'.$key}}">
					<hr class="{{'family-data-'.$key}}">
					<a href='javascript:;' class="{{'btn btn-dark btn-sm mt-5 remove-family float-right family-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus p-0'></i></a>

					<br class="{{'family-data-'.$key}}">
					<br class="{{'family-data-'.$key}}">
				@endif
				<div class="{{'form-group row '.$familyClass}}">
					<div class="col-lg-4">
						{!! Form::label('family_member_name', __('Family Member Name'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][family_member_name]',
							!empty($row->family_member_name) ? $row->family_member_name : old('family_detail['.$key.'][family_member_name]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter family member name</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('relation', __('Relation'), ['class' => '']) !!}
						{!!
							Form::select('family_detail['.$key.'][relation]',$relations,
							!empty($row->relation) ? $row->relation : old('family_detail['.$key.'][relation]'),
							['class' => 'form-control searchpicker'])
						!!}
						<span class="form-text text-muted">Please select relation</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('dob', __('Date Of Birth'), ['class' => '']) !!}
						{!!
							Form::date('family_detail['.$key.'][date_of_birth]',
							!empty($row->date_of_birth) ? $row->date_of_birth : old('family_detail['.$key.'][date_of_birth]'),
							['class' => 'form-control','max'=>date('Y-m-d')])
						!!}
						<span class="form-text text-muted">Please enter date of birth</span>
					</div>
					<div class="col-lg-1">
					@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark btn-sm mt-5 remove-family float-right family-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus p-0'></i></a>
					@endif
					</div>
				</div>	
				<div class="{{'form-group row '.$familyClass}}">
					<div class="col-lg-6">
						{!! Form::label('education', __('Education'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][education]',
							!empty($row->education) ? $row->education : old('family_detail['.$key.'][education]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter education</span>
					</div>
					<div class="col-lg-5">
						{!! Form::label('occupation', __('Occupation'), ['class' => '']) !!}
						{!!
							Form::text('family_detail['.$key.'][occupation]',
							!empty($row->occupation) ? $row->occupation : old('family_detail['.$key.'][occupation]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter occupation</span>
					</div>
					<div class="col-lg-1">
					@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark btn-sm mt-5 remove-family float-right family-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus p-0'></i></a>
					@endif
					</div>
				</div>
	   @endforeach
	  @endif
	</div>
</div>

{{-- Previous Employer Details --}}
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Previous Employer Details
		</h3>
		<div class="col-md-1 flot-right">
			<a href="javascript:;" class="btn btn-dark btn-sm mt-5 add-previous-employer-btn" data-id="{{!empty($previousEmployerData) ? count((array)$previousEmployerData) + 1 : 1}}"><i class="fa fa-plus p-0"></i></a>
		</div>
	</div>
	<div class="card-body previous-employer-data">
		@if(!empty($previousEmployerData))
			@foreach ($previousEmployerData as $key=>$row)
				@php
					$key = $loop->iteration;
					$previousEmployerClass = $key == 1 ? '' : 'previous-employer-data-'.$key;
				@endphp
				@if($key != 1)
					<br class="{{'previous-employer-data-'.$key}}">
					<hr class="{{'previous-employer-data-'.$key}}">
					
					<br class="{{'previous-employer-data-'.$key}}">
					<br class="{{'previous-employer-data-'.$key}}">
				@endif
				<div class="{{'form-group row '.$previousEmployerClass}}">
					<div class="col-lg-4">
						{!! Form::label('employer', __('Employer'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][employer]',
							!empty($row->employer) ? $row->employer : old('previous_employer['.$key.'][employer]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter employer</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('city', __('City'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][city]',
							!empty($row->city) ? $row->city : old('previous_employer['.$key.'][city]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter city</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('contact_name', __('Contact Name'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][contact_name]',
							!empty($row->contact_name) ? $row->contact_name : old('previous_employer['.$key.'][contact_name]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter contact name</span>
					</div>
					<div class="col-lg-1">
					@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark font-weight-bold btn-pill mt-1 remove-employer-data family-btn float-right previous-employer-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus'></i></a>
					@endif
					</div>
				</div>	
				<div class="{{'form-group row '.$previousEmployerClass}}">
					<div class="col-lg-4">
						{!! Form::label('contact_no', __('Contact Number'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][contact_no]',
							!empty($row->contact_no) ? $row->contact_no : old('previous_employer['.$key.'][contact_no]'),
							['class' => 'form-control valid-number', 'pattern'=>"^[0-9]{0,10}$",'maxlength'=>'10','minlength'=>'10','title'=>"Contact number must be 10 digits"])
						!!}
						<span class="form-text text-muted">Please enter contact number</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('employment_period', __('Employment Period'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][employment_period]',
							!empty($row->employment_period) ? $row->employment_period : old('previous_employer['.$key.'][employment_period]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter employment period</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('reason_for_leaving', __('Reason for leaving'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][reason_for_leaving]',
							!empty($row->reason_for_leaving) ? $row->reason_for_leaving : old('previous_employer['.$key.'][reason_for_leaving]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter reason for leaving</span>
					</div>
					<div class="col-lg-1">
					</div>
				</div>
			@endforeach
		@endif
		@if(old('previous_employer'))
		@foreach(old('previous_employer') as $key=>$row)
	@php
					$key = $loop->iteration;
					$previousEmployerClass = $key == 1 ? '' : 'previous-employer-data-'.$key;
				@endphp
				@if($key != 1)
					<br class="{{'previous-employer-data-'.$key}}">
					<hr class="{{'previous-employer-data-'.$key}}">
					<br class="{{'previous-employer-data-'.$key}}">
					<br class="{{'previous-employer-data-'.$key}}">
				@endif
				<div class="{{'form-group row '.$previousEmployerClass}}">
					<div class="col-lg-4">
						{!! Form::label('employer', __('Employer'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][employer]',
							!empty($row->employer) ? $row->employer : old('previous_employer['.$key.'][employer]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter employer</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('city', __('City'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][city]',
							!empty($row->city) ? $row->city : old('previous_employer['.$key.'][city]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter city</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('contact_name', __('Contact Name'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][contact_name]',
							!empty($row->contact_name) ? $row->contact_name : old('previous_employer['.$key.'][contact_name]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter contact_name</span>
					</div>
					<div class="col-lg-1">
					@if($key != 1)
					<a href='javascript:;' class="{{'btn btn-dark font-weight-bold btn-pill mt-1 remove-employer-data family-btn float-right previous-employer-data-'.$key}}" data-id="{{$key}}"><i class='fa fa-minus'></i></a>
					@endif
					</div>
				</div>	
				<div class="{{'form-group row '.$previousEmployerClass}}">
					<div class="col-lg-4">
						{!! Form::label('contact_no', __('Contact Number'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][contact_no]',
							!empty($row->contact_no) ? $row->contact_no : old('previous_employer['.$key.'][contact_no]'),
							['class' => 'form-control valid-number', 'pattern'=>"^[0-9]{0,12}$",'maxlength'=>'10','minlength'=>'10','title'=>"Contact number must be 10 digits"])
						!!}
						<span class="form-text text-muted">Please enter contact number</span>
					</div>
					<div class="col-lg-3">
						{!! Form::label('employment_period', __('Employment Period'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][employment_period]',
							!empty($row->employment_period) ? $row->employment_period : old('previous_employer['.$key.'][employment_period]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter employment period</span>
					</div>
					<div class="col-lg-4">
						{!! Form::label('reason_for_leaving', __('Reason for leaving'), ['class' => '']) !!}
						{!!
							Form::text('previous_employer['.$key.'][reason_for_leaving]',
							!empty($row->reason_for_leaving) ? $row->reason_for_leaving : old('previous_employer['.$key.'][reason_for_leaving]'),
							['class' => 'form-control'])
						!!}
						<span class="form-text text-muted">Please enter reason for leaving</span>
					</div>
					<div class="col-lg-1">
					</div>
				</div>
				@endforeach
		@endif
	</div>
</div>

{{-- Job Detail --}}
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Job Detail
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			<div class="col-lg-3">
				{!! Form::label('joining_date', __('Joining Date'), ['class' => '']) !!}
				{!!
					Form::date('job_data[0][joining_date]',
					!empty($jobData[0]->joining_date) ? $jobData[0]->joining_date : old('job_data[0][joining_date]'),
					['class' => 'form-control','max'=>date('Y-m-d')])
				!!}
				<span class="form-text text-muted">Please select joining date</span>
			</div>
			<div class="col-lg-3">
				{!! Form::label('position', __('Position'), ['class' => '']) !!}
				{!!
					Form::text('job_data[0][position]',
					!empty($jobData[0]->position) ? $jobData[0]->position : old('job_data[0][position]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter position</span>
			</div>
			<div class="col-lg-3">
				{!! Form::label('level', __('Level'), ['class' => '']) !!}
				{!!
					Form::text('job_data[0][level]',
					!empty($jobData[0]->level) ? $jobData[0]->level : old('job_data[0][level]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter level</span>
			</div>
			<div class="col-lg-3">
				{!! Form::label('reporting_to', __('Reporting To'), ['class' => '']) !!}
				{!!
					Form::text('job_data[0][reporting_to]',
					!empty($jobData[0]->reporting_to) ? $jobData[0]->reporting_to : old('job_data[0][reporting_to]'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter reporting to</span>
			</div>
		</div>	
	</div>
</div>

{{--bank details  --}}
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Bank Detail
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			<div class="col-lg-4">
				{!! Form::label('bank_name', __('Bank Name'), ['class' => '']) !!}
				{!!
					Form::text('bank_data[0][bank_name]',
					!empty($bankData[0]->bank_name) ? $bankData[0]->bank_name : old('bank_data[0][bank_name]'),
					['class' => 'form-control', 'pattern'=>"^[a-zA-Z ]{0,50}$",'title' => 'Bank name not allowed special character or number.'])
				!!}
				<span class="form-text text-muted">Please enter bank name</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('account_number', __('Account Number'), ['class' => '']) !!}
				{!!
					Form::text('bank_data[0][account_number]',
					!empty($bankData[0]->account_number) ? $bankData[0]->account_number : old('bank_data[0][account_number]'),
					['class' => 'form-control valid-number','pattern'=>"^[0-9]{0,20}$", 'title' => 'Account number can allowed only number.','maxlength'=>"10"])
				!!}
				<span class="form-text text-muted">Please enter account number</span>
			</div>
			<div class="col-lg-4">
				{!! Form::label('ifsc_code', __('IFSC Code'), ['class' => '']) !!}
				{!!
					Form::text('bank_data[0][ifsc_code]',
					!empty($bankData[0]->ifsc_code) ? $bankData[0]->ifsc_code : old('bank_data[0][ifsc_code]'),
					['class' => 'form-control', 'pattern'=>"^[a-zA-Z0-9]{0,20}$", 'title' => 'IFSC code can not allowed special character.','maxlength'=>"20"])
				!!}
				<span class="form-text text-muted">Please enter ifsc code</span>
			</div>
		</div>	
	</div>
	{{-- <div class="card-footer">
		<div class="row">
			<div class="col-lg-6">
				{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitEmployee']) !!}
			</div>
		</div>
	</div> --}}
</div>

<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Social Links Information
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			<div class="col-lg-6">
				{!! Form::label('facebook', __('Facebook'), ['class' => '']) !!}
				{!!
					Form::url('facebook',
					isset($data['facebook']) ? $data['facebook'] : old('facebook'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter facebook url</span>
			</div>
			<div class="col-lg-6">
				{!! Form::label('twitter', __('Twitter'), ['class' => '']) !!}
				{!!
					Form::url('twitter',
					isset($data['twitter']) ? $data['twitter'] : old('twitter'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter twitter url</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				{!! Form::label('instagram', __('Instagram'), ['class' => '']) !!}
				{!!
					Form::url('instagram',
					isset($data['instagram']) ? $data['instagram'] : old('instagram'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter instagram url</span>
			</div>
			<div class="col-lg-6">
				{!! Form::label('youtube', __('Youtube'), ['class' => '']) !!}
				{!!
					Form::url('youtube',
					isset($data['youtube']) ? $data['youtube'] : old('youtube'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter youtube url</span>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6">
				{!! Form::label('website', __('Website'), ['class' => '']) !!}
				{!!
					Form::url('website',
					isset($data['website']) ? $data['website'] : old('website'),
					['class' => 'form-control'])
				!!}
				<span class="form-text text-muted">Please enter website url</span>
			</div>
		</div>
	</div>
	@if(!$isProfile)
</div>
<!--end::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Acccount Security
		</h3>
	</div>
	<div class="card-body">
		<div class="form-group row">
			<div class="col-lg-6">
				{!! Form::label('isLocked', __('Lock Account ?'), ['class' => '']) !!}
				<span class="switch switch-outline switch-icon switch-danger">
					<label>
						<input type="checkbox" <?php echo (isset($employee['isLocked']))?(($employee['isLocked'] == 1)?'checked="checked"':''):'';?> value="1" name="isLocked">
						<span></span>
					</label>
				</span>
				<span class="form-text text-muted">Please uncheck to unlock account.</span>
			</div>
		</div>
	</div>
	@endif
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-6">
				{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitEmployee']) !!}
				@php
				if($companyId){
					$url = url('rkadmin/employees/'.encrypt($companyId));
				}else{
					$url = url('rkadmin/employees/');
				}
				@endphp
				<a href="{{$url}}" class="btn btn-md btn-primary ml-2">Cancel</a>
			</div>
		</div>
	</div>
</div>
<!-- Modal-->
<div class="modal fade" id="cast_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCast" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCast">Add Cast</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="col-lg-12">
					{!! Form::label('NewCast', __('Cast'), ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('NewCast',
						null, 
						['class' => 'form-control','required']) 
					!!}
					<span class="form-text text-muted">Please enter new cast</span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary font-weight-bold" onClick="submitNewCast()">Submit</button>
			</div>
		</div>
	</div>
</div>
<!--End Modal-->
@section('scripts')
<script>
$('#cast_name').select2({
  ajax: {
    url: '{!! route("admin.getcast") !!}',
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
				  text: item,
				  id: item
              }
          })
      };
    },
    cache: true
  },
  //tags: true,
  minimumInputLength: 3,
});
function submitNewCast(){
	var NewCast = $('#NewCast').val();
	if(NewCast){
		jQuery.ajax({
			url: '{!! route("admin.addCast") !!}',
			data: {"NewCast":NewCast,"_token": "{{ csrf_token() }}"},
			type: 'post',
			dataType: 'json',
			success: function(response){
				if(response.success){
					var newState = new Option(response.name, response.name, true, true);
					$("#cast_name").append(newState).trigger('change');
					$('#cast_modal').modal('hide');
				}
			}
		});
	}
}
	function changeMaritalStatus(status){
		if(status == '2'){
		$('.marriage_anniversary_required').hide();
			$("#marriage_anniversary_date").val('').prop('disabled', true).prop('required',false);
		}else if(status == '1'){
		$('.marriage_anniversary_required').show();
			$("#marriage_anniversary_date").prop('disabled', false).prop('required',true);
		}
	}
	jQuery(document).ready(function(){
		jQuery("#picture").change(function(e) {
			var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
			if (regex.test($(this).val().toLowerCase())) {
                if (typeof (FileReader) != "undefined") {
                    $("#preview_profile_image").show();
                   
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("#show_profile_image").css("background-image", "url('"+e.target.result+"')");
                    }
                    reader.readAsDataURL($(this)[0].files[0]);
                } else {
                    alert("This browser does not support FileReader.");
                }
			} else {
				alert("Please upload a valid image file.");
			}
		});
		jQuery(document).on('change','#cast_name',function(e){
		var cast = $(this).val();
		if(cast == 'New Cast'){
		jQuery('#cast_modal').modal('show');
		}			
		});
			
		jQuery(document).on('change','#marital_status',function(e){
			changeMaritalStatus($("#marital_status").val())
		});
		jQuery(document).on('change','#user_type',function(e){
			var user_type = $(this).val();
			if(user_type == 3 || user_type == 4){
				// $('.role_info').hide();
				$('.commission_info').show();
				$('.commission_required').show();
				$('.commission_info #commission').prop('required', true);
				
			}else{
				// $('.role_info').show();
				$('.commission_info').hide();
				$('.commission_required').hide();
				$('.commission_info #commission').prop('required', false);
			}
		});
		// $('#user_type').trigger('change')
		changeMaritalStatus('{{@$employee["marital_status"]}}')
	});

        var relations = @json($relations);
        $(document).ready(function(){
            var familyId = $('.add-family-btn').data('id');
            if(familyId == 1){
                $('.add-family-btn').click();
            }
            var employeeId = $( 
                '.add-previous-employer-btn').data('id');
            if(employeeId == 1){
                $('.add-previous-employer-btn').click();
            }
        });
		jQuery(document).on('change','.country_id-select',function(){
            var country_id_select = $(this).val();
            jQuery('.state_id_select').select2().empty();
                jQuery.ajax({
                    url: '{!! route("admin.getstate") !!}',
                    data: {country_id:country_id_select},
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
            
                        if(response.success){
                            jQuery('.state_id_select').select2({
                                placeholder: 'Please select state',
                                data: response.states
                            }).trigger('change')
                        }
                    }
                });
        });
		jQuery(document).on('change','.state_id_select',function(){
            var state_id_select = $(this).val();
            
            jQuery('.city-select').select2().empty();
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

		jQuery(document).on('change','.city-select',function(){
            var city_id_select = $(this).val();
            
            jQuery('.pincode-select').select2().empty();
                jQuery.ajax({
                    url: '{!! route("admin.getpostcode") !!}',
                    data: {city_name:city_id_select},
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
                        if(response.success){
                            jQuery('.pincode-select').select2({
                                placeholder: 'Please select post code',
                                data: response.postcodes
                            });
                        }
                    }
                });
            });

			<?php if(isset($employee)){?>
                jQuery('.state_id_select').trigger('change');

				setTimeout(function(){ 
					jQuery(".city-select").val("{{isset($employee['city']) ? $employee['city'] : 'Please select city'}}").trigger('change')
 				}, 500);

				 setTimeout(function(){ 
					jQuery(".pincode-select").val("{{isset($employee['pincode']) ? $employee['pincode'] : 'Please select post code'}}").trigger('change')
 				}, 800); 

				
            <?php }?>

    </script>
    <script src="{{ asset('js/employee.js') }}?v=0.0.1" type="text/javascript"></script>
<script type="text/javascript">
/* $('document').ready(function() {
	$('input').on('input', function() {
      var re = new RegExp(this.pattern);
      var str = $(this).val();
	  $(this).tooltip({
        placement: 'bottom',
        trigger: 'focus',
        html: true,
        title: $(this).attr('title')
      });
	  if(!re.test(str)){
		$(this).tooltip('show');
	  }else{
		  $(this).tooltip('hide');
	  }
    });
}); */
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection