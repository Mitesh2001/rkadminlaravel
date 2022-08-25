@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-email text-primary"></i>
            </span>
            <h3 class="form_title">Email Template : <span>Update</span></h3>
        </div>
    </div>
    <div class="card-body">
        @include('admin.layouts.alert')
        {!! Form::open(['route' => 'admin.emails.store','class' => 'ui-form']) !!}
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('name', __('Name'), ['class' => '']) !!}
                    {!! 
                        Form::text('name',
                        isset($emailTemplate['name']) ? $emailTemplate['name'] : null, 
                        ['class' => 'form-control','required','disabled']) 
                    !!}
                    {{Form::hidden('id',!empty($emailTemplate) ? $emailTemplate->email_template_id : null)}}
                </div>
                <div class="col-lg-6">
                    {!! Form::label('subject', __('Subject'), ['class' => '']) !!}
                    <span class="text-danger">*</span>
                    {!! 
                        Form::text('subject',
                        isset($emailTemplate['subject']) ? $emailTemplate['subject'] : null, 
                        ['class' => 'form-control','required']) 
                    !!}    
                    <span class="form-text text-muted">Please enter subject</span>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <span class="text-danger">*</span>
                    {!! Form::label('content', __('Email Template'), ['class' => '']) !!}
                    {!! 
                        Form::textarea('content',  
                        isset($emailTemplate['content']) ? $emailTemplate['content'] : null, 
                        ['class' => 'form-control','id'=>'kt_tinymce_2','required']) 
                    !!}
                    <span class="form-text text-muted">Edit email template</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::submit('Update', ['class' => 'btn btn-md btn-primary']) !!}
                        <a href="{{url('rkadmin/emails')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
                    </div>
                </div>
            </div>
        {{Form::close()}}
    </div>
</div>
@stop
@section('scripts')
<script>
var KTTinymce = function () {
    var emailTemplateEditor = function () {
        tinymce.init({
            selector: '#kt_tinymce_2',
            plugins: ['table'],
            forced_root_block : "", 
            force_br_newlines : true,
            force_p_newlines : false,
            statusbar: false,
        });
    }
    return {
        init: function() {
            emailTemplateEditor();
        }
    };
}();

jQuery(document).ready(function() {
    KTTinymce.init();
});
</script>
<!-- <script src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/plugins/custom/tinymce/tinymce.bundle.js?v=7.2.8"></script> -->

<script src="{{ asset('plugins/custom/tinymce/tinymce.min.js') }}"></script>

@endsection      
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection