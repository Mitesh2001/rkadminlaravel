<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<div class="card-header">
		<h3 class="card-title">
			Custom Fields
		</h3>
	</div>
		<div class="card-body">
			{{Form::open(['url' => ['rkadmin/custom-form/'.$companyId],'type'=>'POST','class' => 'ui-form'])}}
				<div class="form-group row">
					{{Form::hidden('company_id',$companyId)}}
					<div class="col-lg-3">
						{!! Form::label('module_name', __('Module Name'), ['class' => '']) !!}
						{!! 
							Form::select('module_name',$moduleName,
							isset($data['module_name']) ? $data['module_name'] : null, 
							['class' => 'form-control','required']) 
						!!}
						<span class="form-text text-muted">Please select module name</span>
					</div>
				</div>
				{{--
				<div class="form-group row">
					<div class="col-lg-3">
						{!! Form::label('section_name', __('Section Name'), ['class' => 'font-weight-bold']) !!}
						{!! 
							Form::text('section[1][name]',
							'Primary Information', 
							['class' => 'form-control','required']) 
						!!}
						<span class="form-text text-muted">Please enter section name</span>
					</div>
					<div class="col-lg-2">
						{!! Form::label('section_priority', __('Section Priority'), ['class' => 'font-weight-bold']) !!}
						{!! 
							Form::text('section[1][priority]',1, 
							['class' => 'form-control valid-number','required','maxlength'=>'3','readonly']) 
						!!}
						<span class="form-text text-muted">Please enter section priority</span>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-2 plus-minus-div">
						<a href="javascript:;" class="btn btn-success font-weight-bold btn-pill mt-5 plus-btn add-field-div add-field-data" data-id="3" data-sid="1">Add Field &nbsp;<i class="fa fa-plus"></i></a>
					</div>
				</div>
				<hr>
				
				<div class="{{'form-group row'}}">
					<div class="col-lg-3">
						{!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
						{!! 
							Form::select('field_data[1][1][input_type]',$inputTypes,1,
							['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
						!!}
						{{Form::hidden('field_data[1][1][input_type]','1')}}
						{{Form::hidden('field_data[1][1][pre_field]','1')}}
						<span class="form-text text-muted">Please select input type</span>
					</div>
					<div class="col-lg-2">
						{!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][1][label_name]','name',
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter label name</span>
					</div>
					<div class="col-md-2">
						<br>
						<div class="checkbox-inline mt-3">
							<label class="checkbox checkbox-square">
								{!! Form::checkbox('field_data[1][1][is_required]','1',true) !!}
								<span></span>
								Is Required ?
							</label>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][1][minlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter minimum length</span>
					</div>
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][1][maxlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter maximum length</span>
					</div>
				</div>
				<hr>
				
				<div class="{{'form-group row'}}">
					<div class="col-lg-3">
						{!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
						{!! 
							Form::select('field_data[1][2][input_type]',$inputTypes,1,
							['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
						!!}
						{{Form::hidden('field_data[1][2][input_type]','1')}}
						{{Form::hidden('field_data[1][2][pre_field]','1')}}
						<span class="form-text text-muted">Please select input type</span>
					</div>
					<div class="col-lg-2">
						{!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][2][label_name]','email',
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter label name</span>
					</div>
					<div class="col-md-2">
						<br>
						<div class="checkbox-inline mt-3">
							<label class="checkbox checkbox-square">
								{!! Form::checkbox('field_data[1][2][is_required]','1',true) !!}
								<span></span>
								Is Required ?
							</label>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][2][minlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter minimum length</span>
					</div>
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][2][maxlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter maximum length</span>
					</div>
				</div>
				<hr>
				<div class="{{'form-group row'}}">
					<div class="col-lg-3">
						{!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
						{!! 
							Form::select('field_data[1][3][input_type]',$inputTypes,1,
							['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
						!!}
						{{Form::hidden('field_data[1][3][input_type]','1')}}
						{{Form::hidden('field_data[1][3][pre_field]','1')}}
						<span class="form-text text-muted">Please select input type</span>
					</div>
					<div class="col-lg-2">
						{!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][3][label_name]','password',
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter label name</span>
					</div>
					<div class="col-md-2">
						<br>
						<div class="checkbox-inline mt-3">
							<label class="checkbox checkbox-square">
								{!! Form::checkbox('field_data[1][3][is_required]','1',true) !!}
								<span></span>
								Is Required ?
							</label>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][3][minlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter minimum length</span>
					</div>
					<div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
						{!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
						{!! 
							Form::text('field_data[1][3][maxlength]',
							isset($value['maxlength']) ? $value['maxlength'] : null, 
							['class' => 'form-control']) 
						!!}
						<span class="form-text text-muted">Please enter maximum length</span>
					</div>
				</div>

				<div class="input-field-data-1"></div>
				<div class="input-field-data"></div>
				<hr>
				--}}
				<div class="input-field-data"></div>
				<div class="form-group row">
					<div class="col-md-3 plus-minus-div">
						<a href="javascript:;" class="btn btn-primary font-weight-bold btn-pill mt-5 plus-btn add-section add-field-div" data-id="0">Add Section &nbsp;<i class="fa fa-plus"></i></a>
					</div>
				</div>
				<div class="card-footer">
					<div class="row">
						{!! Form::submit('Submit', ['class' => 'btn btn-md btn-primary']) !!}
						<a href="{{URL::to('rkadmin/custom-form/'.encrypt($companyId))}}" class="btn btn-md btn-primary ml-2">Cancel</a>
					</div>
				</div>
			{{Form::close()}}
		</div>
		
</div>
<!--end::Card-->