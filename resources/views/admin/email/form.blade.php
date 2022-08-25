<div class="form-group row">
	<div class="col-lg-4">
		{!! Form::label('name', __('Name'), ['class' => '']) !!}
		<span class="text-danger">*</span>
		{!!
			Form::text('name',
			(old('name'))?old('name'):(isset($emailTemplate['name']) ? $emailTemplate['name']:null),
			['class' => 'form-control','required','id'=>'name','placeholder'=>'Please enter template name'])
		!!}
		{{Form::hidden('id',!empty($emailTemplate) ? $emailTemplate->email_template_id : null)}}
	</div>
	<div class="col-lg-4">
		{!! Form::label('subject', __('Subject'), ['class' => '']) !!}
		<span class="text-danger">*</span>
		{!!
			Form::text('subject',
			(old('subject'))?old('subject'):(isset($emailTemplate['subject']) ? $emailTemplate['subject'] : null),
			['class' => 'form-control','required','placeholder'=>'Please enter subject'])
		!!}
	</div>
	<div class="col-lg-4 template_type_div">
		{!! Form::label('template_type', __('Template Type'), ['class' => '']) !!}
		<?php $types = array('Please Select Template Type','Default Events','Marketing');?>
		<span class="text-danger">*</span>
		{!!
			Form::select('template_type',
			$types,
			(old('template_type'))?old('template_type'):(isset($emailTemplate['template_type']) ? $emailTemplate['template_type'] : ''),
			['required','class' => 'form-control','id'=>'template_type'])
		!!}
	</div>
</div>
<div class="form-group row">
	<div class="col-lg-12">
		{!! Form::label('content', __('Email Content'), ['class' => '']) !!}
		<span class="text-danger">*</span>
		{!!
			Form::textarea('content',
			(old('content'))?old('content'):(isset($emailTemplate['content']) ? $emailTemplate['content'] : null),
			['class' => 'form-control','id'=>'kt_tinymce_2','required'])
		!!}
		<span class="form-text text-muted">Edit email template</span>
	</div>
</div>

{{-- <div class="form-group row">
	<div class="col-lg-12">
		{!! Form::label('content', __('Variables'), ['class' => '']) !!}
		{!!
			Form::select('template_variables',
			$types,
			(old('template_variables'))?old('template_variables'):(isset($data['template_variables']) ? $data['template_variables'] : null),
			['class' => 'form-control ui search selection template_variables searchpicker','id'=>'template_variables','multiple'],[ 0 => [ "disabled" => true ]] )
		!!}
	</div>
</div> --}}
<div class="card-footer">
	<div class="row">
		<div class="col-lg-6">
			{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!}
			<a href="{{url('rkadmin/emails')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
		</div>
	</div>
</div>
@section('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js"></script>
<script>
<?php if(isset($emailTemplate)){?>
//jQuery("#name").attr("readonly",true);
$('.template_type_div').hide();
<?php /* if($emailTemplate['template_type'] == 1){?>
jQuery("#template_type").attr("disabled",true);
<?php } */}?>
/* var KTTinymce = function () {
    var emailTemplateEditor = function () {
        tinymce.init({
            selector: '#kt_tinymce_2',
            forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false,
            statusbar: true,
            //plugins: ['table','wordcount','code','visualchars','visualblocks','toc','lists','advlist','charmap','directionality','textpattern','searchreplace','save','autolink','autoresize','link','media','powerpaste','image','quickbars'],
			//toolbar: 'wordcount',
			plugins: [
    'advlist autolink lists link image charmap print preview anchor emoticons',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table paste imagetools wordcount hr'
  ],
  toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | emoticons',
  fullpage_default_encoding: 'UTF-8',
  extraPlugins: 'strinsert'
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
}); */
CKEDITOR.plugins.add('strinsert', {
   requires: ['richcombo'],
   init: function(editor) {
      //  array of strings to choose from that'll be inserted into the editor
      var strings = [];
      var o_b = '&lcub;&lcub;';
      var c_b = '&rcub;&rcub;';

      /* strings.push([o_b+'#subscription_invoice_no'+c_b, 'Subscription Invoice No']);

      strings.push([o_b+'#subscription_created_date'+c_b, 'Subscription Created Date']); */

      strings.push([o_b+'#company_name'+c_b, 'Company Name']);

      //strings.push([o_b+'#company_logo'+c_b, 'Company Logo']);

      strings.push([o_b+'#company_address'+c_b, 'Company Address']);
	  
	 /*  strings.push([o_b+'#company_contact_person_name'+c_b, 'Contact Person Name']);
	  
	  strings.push([o_b+'#company_contact_person_mobile'+c_b, 'Contact Person Mobile']);
	  
	  strings.push([o_b+'#company_contact_person_email'+c_b, 'Contact Person Email']); */

      strings.push([o_b+'#client_name'+c_b, 'Client Name']);

      strings.push([o_b+'#client_email'+c_b, 'Client Email']);

      strings.push([o_b+'#client_mobile'+c_b, 'Client Mobile']);

      strings.push([o_b+'#client_address'+c_b, 'Client Address']);

      //strings.push([o_b+'#company_gst_no'+c_b, 'Company GST No']);

      /* strings.push([o_b+'#subscription_total_amount'+c_b, 'Subscription Total Amount']);

      strings.push([o_b+'#subscription_sgst_amount'+c_b, 'Subscription SGST Amount']);

      strings.push([o_b+'#subscription_sgst'+c_b, 'Subscription SGST %']);

      strings.push([o_b+'#subscription_cgst'+c_b, 'Subscription CGST %']);

      strings.push([o_b+'#subscription_cgst_amount'+c_b, 'Subscription CGST Amount']);

      strings.push([o_b+'#subscription_igst'+c_b, 'Subscription IGST %']);
	  
      strings.push([o_b+'#subscription_igst_amount'+c_b, 'Subscription IGST Amount']);

      strings.push([o_b+'#subscription_payment_status'+c_b, 'Subscription Payment Status']);

      strings.push([o_b+'#subscription_payment_mode'+c_b, 'Subscription Payment Mode']);

      strings.push([o_b+'#subscription_payment_date'+c_b, 'Subscription Payment Date']);

      strings.push([o_b+'#subscription_bank_name'+c_b, 'Subscription Bank Name']);

      strings.push([o_b+'#subscription_transaction_no'+c_b, 'Subscription Transaction Number']);

      strings.push([o_b+'#reset_password_button'+c_b, 'Reset Password Button']);

      strings.push([o_b+'#reset_password_link'+c_b, 'Reset Password Link']); */
	  
      //strings.push([o_b+'#subscription_date'+c_b, 'Subscription Date']);
	  
      //strings.push([o_b+'#subscription_number'+c_b, 'Subscription Number']);
	  
      /* strings.push([o_b+'#subscription_expiry_date'+c_b, 'Subscription Expiry Date']);
	  
	  strings.push([o_b+'#user_name'+c_b, 'User Name']);
	  
	  strings.push([o_b+'#user_email'+c_b, 'User Email']);
	  
	  strings.push([o_b+'#user_mobile'+c_b, 'User Mobile']);
	  
	  strings.push([o_b+'#user_address'+c_b, 'User Address']);
	  
	  strings.push([o_b+'#dealer_name'+c_b, 'Dealer Name']);
	  
	  strings.push([o_b+'#dealer_email'+c_b, 'Dealer Email']);
	  
	  strings.push([o_b+'#dealer_mobile'+c_b, 'Dealer Mobile']);
	  
	  strings.push([o_b+'#dealer_address'+c_b, 'Dealer Address']);
	  
	  strings.push([o_b+'#dealer_commission'+c_b, 'Dealer Commission']);
	  
	  strings.push([o_b+'#distributor_name'+c_b, 'Distributor Name']);
	  
	  strings.push([o_b+'#distributor_email'+c_b, 'Distributor Email']);
	  
	  strings.push([o_b+'#distributor_mobile'+c_b, 'Distributor Mobile']);
	  
	  strings.push([o_b+'#distributor_address'+c_b, 'Distributor Address']);
	  
	  strings.push([o_b+'#distributor_commission'+c_b, 'Distributor Commission']); */
	  
      strings = strings.sort(function(a, b) {
            return a[0].localeCompare(b[0], undefined, {
              sensitivity: 'accent'
            });
        });

      // add the menu to the editor
      editor.ui.addRichCombo('strinsert', {
         label: 'Variable',
         title: 'Insert Variable',
         voiceLabel: 'Insert Variable',
         className: 'cke_format',
         multiSelect: false,
         panel: {
            css: [editor.config.contentsCss, CKEDITOR.skin.getPath('editor')],
            voiceLabel: editor.lang.panelVoiceLabel
         },

         init: function() {
            this.startGroup("Insert Variable");
            for (var i in strings) {
               this.add(strings[i][0], strings[i][1], strings[i][1]);
            }
         },

         onClick: function(value) {
            editor.focus();
            editor.fire('saveSnapshot');
            editor.insertHtml(value);
            editor.fire('saveSnapshot');
         }
      });
   }
});

CKEDITOR.replace( 'kt_tinymce_2',{
      height: 400,
      allowedContent : true,
      //removeButtons : 'Image,Source,About,Scayt',
      extraPlugins: 'strinsert',
} );


toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-top-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "200",
  "hideDuration": "500",
  "timeOut": "2000",
  "extendedTimeOut": "500",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};
</script>
{{-- <script src="{{ asset('plugins/custom/tinymce/tinymce.min.js') }}"></script> --}}
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection