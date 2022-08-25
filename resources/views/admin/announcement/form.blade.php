@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="fa fa-bullhorn text-primary"></i>
            </span>
            <h3 class="form_title"></h3>
            <h3 class="form_title">{{isset($announcement['announcement']) ? 'Update' : 'Add'}} Announcement</h3>
        </div>
    </div>
    <div class="card-body">
        @include('admin.layouts.alert')
        {!! Form::open(['route' => 'admin.announcement.store','class' => 'ui-form']) !!}
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('announcement', __('Announcement'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::textarea('announcement',
                        isset($announcement['announcement']) ? $announcement['announcement'] : null, 
                        ['class' => 'form-control','required','rows'=>'3']) 
                    !!}
                    {{Form::hidden('id',!empty($announcement) ? $announcement->id : null)}}
                    <span class="form-text text-muted">Please enter announcement</span>
                </div>
                <div class="col-lg-6">
                    {!! Form::label('announcement_date', __('Announcement start and end date'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::text('announcement_date_time',
                        isset($announcement['announcement_date_time']) ? $announcement['announcement_date_time'] : null, 
                        ['class' => 'form-control', 'id' => 'announcement_date_time','required' ]) 
                    !!}
                    <span class="form-text text-muted">Please enter announcement start and end date</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::submit('Submit', ['class' => 'btn btn-md btn-primary']) !!}
                        <a href="{{url('rkadmin/announcement')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
                    </div>
                </div>
            </div>
        {{Form::close()}}
    </div>
</div>
@section('scripts')
<script>
$(document).ready(function(){
    jQuery(document).on('change','#start_date',function(e){
        $('#end_date').attr('min',$(this).val())
    });
});
var KTBootstrapDaterangepicker = function () {

var announcement_date_time = function () {

    $('#announcement_date_time').daterangepicker({
        buttonClasses: ' btn',
        opens: 'left',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary',
        @if(!isset($announcement))
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
    announcement_date_time();
 }
};

}();

jQuery(document).ready(function() {
    KTBootstrapDaterangepicker.init();
});    
</script>    
@endsection    
@stop
        
	