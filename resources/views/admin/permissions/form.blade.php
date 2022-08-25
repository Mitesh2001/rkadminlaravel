
			<div class="form-group row">
				@if($companyId)
					{{Form::hidden('company_id',$companyId)}}
					{{Form::hidden('is_company','1')}}
				@else
					<div class="col-lg-6">
						{!! Form::label('type', __('Type'), ['class' => '']) !!}
						<span class="text-danger">*</span>
						{!!
							Form::select('guard_name',
							['web'=>'Web','api'=>'API'],!empty($permission['guard_name']) ? $permission['guard_name'] : null,
							['class' => 'form-control ui search selection top right pointing company_id-select',
							'id' => 'company_id-select','required','placeholder'=>'Please select type'])
						!!}
						<span class="form-text text-muted">Please select type</span>
					</div>
				@endif
				<div class="col-lg-6">
					{!! Form::label('name', __('Permission Name'). ':', ['class' => '']) !!}
					<span class="text-danger">*</span>
					{!! 
						Form::text('name',
						isset($data['name']) ? $data['name'] : null, 
						['class' => 'form-control','required']) 
					!!}
					<span class="form-text text-muted">Please enter permission name</span>
				</div>
			</div>

		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitpermission']) !!}
				</div>
			</div>
		</div>
	@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection