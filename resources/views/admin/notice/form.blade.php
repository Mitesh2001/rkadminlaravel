@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-bell-2 text-primary"></i>
            </span>
            <h3 class="form_title">{{isset($notice['notice']) ? 'Update' : 'Add'}} Notice</h3>
        </div>
    </div>
    <div class="card-body">
        @include('admin.layouts.alert')
        {!! Form::open(['route' => 'admin.notice-board.store','class' => 'ui-form']) !!}
            <div class="form-group row">
                <div class="col-lg-4">
                    {!! Form::label('company_id', __('Company'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
					{!!
						Form::select('company_id',
						$company,
						isset($notice['company_id']) ? $notice['company_id'] : null,
						['class' => 'form-control searchpicker company_id',
						'placeholder'=>'Please select company','required'])
					!!}
					<span class="form-text text-muted">Please select company</span>
                </div>
                <div class="col-lg-4">
                    {!! Form::label('user_id', __('User'), ['class' => '']) !!}
					{!!
						Form::select('user_id',
						array(),
						isset($notice['user_id']) ? $notice['user_id'] : null,
						['class' => 'form-control searchpicker',
						'placeholder'=>'Please select user'])
					!!}
					<span class="form-text text-muted">Please select user</span>
                </div>
                <div class="col-lg-4">
                    {!! Form::label('notice_date', __('Notice start and end date'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::text('notice_date_time',
                        isset($notice['notice_date_time']) ? $notice['notice_date_time'] : null, 
                        ['class' => 'form-control', 'id' => 'notice_date_time','required' ]) 
                    !!}
                    <span class="form-text text-muted">Please enter start and end date</span>
                </div>
                <div class="col-lg-4">
                    {!! Form::label('notice', __('Notice'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::text('notice',
                        isset($notice['notice']) ? $notice['notice'] : null, 
                        ['class' => 'form-control','required','rows'=>'3']) 
                    !!}
                    {{Form::hidden('id',!empty($notice) ? $notice->id : null)}}
                    <span class="form-text text-muted">Please enter notice</span>
                </div>
                <div class="col-lg-8">
                    {!! Form::label('description', __('Description'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::textarea('description',
                        isset($notice['description']) ? $notice['description'] : null, 
                        ['class' => 'form-control','required','rows'=>'3']) 
                    !!}
                    {{Form::hidden('id',!empty($notice) ? $notice->id : null)}}
                    <span class="form-text text-muted">Please enter description</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::submit(isset($notice['notice']) ? 'Update' : 'Submit', ['class' => 'btn btn-md btn-primary']) !!}
                        <a href="{{url('rkadmin/notice-board')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
                    </div>
                </div>
            </div>
        {{Form::close()}}
    </div>
</div>
@stop
@section('scripts')
<script>
getCompanyDetails("{{isset($notice['notice']) ? $notice->company_id : null}}");
jQuery(document).on('change','.company_id',function(){
    var company_id = $(this).val();
    getCompanyDetails(company_id);
});
function getCompanyDetails(company_id){
	if(!company_id){
        jQuery('#user_id').select2({placeholder: 'Please select user',data:[]}).empty();
		return false;
	}
    jQuery('#user_id').select2().empty();
    jQuery.ajax({
        url: '{!! URL::to("rkadmin/notice-board/get-company-wise-users") !!}/'+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(response){
            if(response.success){
                jQuery('#user_id').select2({
                    placeholder: 'Please select user',
                    data: response.users
                });
            }
        }
    });
}

var KTBootstrapDaterangepicker = function () {

var notice_date_time = function () {

    $('#notice_date_time').daterangepicker({
        buttonClasses: ' btn',
        opens: 'left',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        @if(!isset($notice))
            minDate: new Date(),
        @endif
        timePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY/MM/DD HH:mm:ss'
        }
    });

}

return {
 init: function() {
    notice_date_time();
 }
};

}();

jQuery(document).ready(function() {
    KTBootstrapDaterangepicker.init();
});

setTimeout(function(){ 
    jQuery("#user_id").val("{{isset($notice['notice']) ? $notice->user_id : null}}").trigger('change')
 }, 500);
</script>
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection