
	<div class="form-group row">
		@if($companyId)
			{{Form::hidden('company_id',$companyId)}}
			{{Form::hidden('is_company','1')}}
		@elseif(!empty($isAssign))
			<div class="col-lg-6">
				{!! Form::label('company_id', __('Company Name'), ['class' => '']) !!}
				<span class="text-danger">*</span>
				{!!
					Form::select('company_id[]',
					$clients,
					isset($companyIds) ? $companyIds : null, 
					['class' => 'form-control ui search selection top right pointing company_id-select searchpicker',
					'id' => 'company_id-select','required','multiple'])
				!!}
				{{Form::hidden('role_id',!empty($role->id) ? $role->id : null)}}
				{{Form::hidden('role_ids',!empty($roleId) ? $roleId : null)}}
				<span class="form-text text-muted">Please select client</span>
			</div>
		@endif
		<div class="col-lg-6">
			{!! Form::label('name', __('Role Name'). ':', ['class' => '']) !!}
			<span class="text-danger">*</span>
			{!! 
				Form::text('name',  
				isset($data['name']) ? $data['name'] : null, 
				['class' => 'form-control special-characters','required','pattern'=>"^[a-zA-Z ]{0,50}$", 'title' => 'Role name can not allowed special character and number.']) 
			!!}
			{{Form::hidden('guard_name',$type)}}
			<span class="form-text text-muted">Please enter role name</span>
		</div>
	</div>
	<div class="row">
		@php
			$i = 0;
			$j = 0;
		@endphp
		@foreach($roleData as $k => $value)
			@php
				$valueId = str_replace(" ", "_", $k);
				$permissionCount = count($value);
				$addedPermissionCount = isset($rolePermissions) ? array_intersect(array_keys($value), $rolePermissions) : [];
				$addedPermissionCount = count($addedPermissionCount);
				$isChecked = $addedPermissionCount == $permissionCount ? true : false;
			@endphp
			<div class="col-md-4">
				<div class="card card-custom card-stretch">
					<div class="card-header">
						<div class="card-title">
							<h3 class="card-label"><input type="checkbox" class="{{'check-item select-all '.$valueId.'-main'}}" id="{{'checbox-'.$valueId}}" data-class="{{$valueId}}" @if($isChecked) checked="checked" @endif>
								<label for="{{'checbox-'.$valueId}}">{{$k}}</label>
							</h3>
						</div>
					</div>
					<div class="row p-5">						
						@foreach ($value as $id=>$item)
							<div class="col-md-4 pt-3 pr-0">
								<input type="checkbox" name="permission[]" class="{{'check-item sub-permission '.$valueId.' p-'.$j}}" id="{{'checkbox-'.$j}}" data-id="{{$i}}" data-mainclass="{{$valueId.'-main'}}" value="{{$id}}" @if(isset($rolePermissions) && in_array($id, $rolePermissions)) checked="checked" @endif>
								<label style="font-weight: normal;" for="{{'checkbox-'.$j}}">{{$item}}</label>
							</div>
							@php
							$j++;
							@endphp
						@endforeach
					</div>
				</div>
			</div>
			@if(($i + 1)%3 === 0)
				</div><div class="row mt-3">
			@endif
			@php
				$i++;
			@endphp
		@endforeach
	</div>

	@php
		$companyId = $companyId ? '?company_id='.encrypt($companyId) : '';
	@endphp
	<br>
	<div class="footer">
		<div class="row">
			<div class="col-lg-12">
				{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitrole']) !!}
				@php
					if($companyId){
						$url = url('rkadmin/roles'.$companyId);
					}else{
						$url = url('rkadmin/roles?type='.$type);
					}
				@endphp
				<a href="{{$url}}" class="btn btn-md btn-primary ml-2">Cancel</a>
			</div>
		</div>
	</div>
@push('scripts')
@isset($jsValidator)
{!! $jsValidator !!}
<script>
    $(document).ready(function(){
        $("#selectAll").on("ifChanged", function(){
            var isChecked = $(this).is(":checked");
            $(".check-item").attr("checked", isChecked);
            //$(".check-item").iCheck('update');
        });
    });
</script>
@endisset
@endpush
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection