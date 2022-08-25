@extends('admin.layouts.master')
@section('content')
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon-list-1 text-primary"></i>
            </span>
            <h3 class="form_title">Invoice : <span>Update</span></h3>
        </div>
</div>
    <div class="card-body">
        <div class="row" style="display:none">
            <div class="col-md-12">
                <p>You can use this variable.</p>
                <?php  $btn_class = 'copy_btn btn btn-sm mb-1 btn-success'; ?>
                <span class="{{$btn_class}}"><?php echo '{{#invoice_no}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#created_date}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#company_address}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#gst_no}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#client_name}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#client_email}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#total_amount}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#sgst_amount}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#cgst_amount}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#igst_amount}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#payment_status}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#payment_mode}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#payment_date}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#bank_name}}';?></span>
                <span class="{{$btn_class}}"><?php echo '{{#transaction_no}}';?></span>
            </div>    
        </div>    
    @include('admin.layouts.alert')
    
        {!! Form::open([
                'url' => 'rkadmin/invoice/update',
                'class' => 'ui-form',
                'id' => 'updateInvoice',
                'enctype' => 'multipart/form-data'
                ]) !!}

                <div class="form-group row">
                    <div class="col-lg-12" style="display: none">
                        {!! Form::label('invoice_template', __('Invoice Template'), ['class' => '']) !!}
                        {!! 
                            Form::textarea('invoice_template',  
                            isset($invoice_template) ? $invoice_template : null, 
                            ['class' => 'form-control','id'=>'kt_tinymce_2']) 
                        !!}
                        <span class="form-text text-muted">Edit invoice template</span>
                    </div>


                    <div class="col-lg-12">
                        {!! Form::label('invoice_template', __('Invoice Template'), ['class' => '']) !!}
                        {!! 
                            Form::textarea('invoice_template',  
                            isset($invoice_template) ? $invoice_template : null, 
                            ['class' => 'form-control','id'=>'kt_ckeditor4_2']) 
                        !!}
                        <span class="form-text text-muted">Edit invoice template</span>
                    </div>


                </div>
                <div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
                    {!! Form::submit('Update Invoice', ['class' => 'btn btn-md btn-primary', 'id' => 'submitInvoice']) !!}
				</div>
			</div>
		</div>

        {!! Form::close() !!}
    </div>
</div>
<!--end::Card-->
@stop
@section('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js"></script>

<script>
  CKEDITOR.plugins.add('strinsert', {
   requires: ['richcombo'],
   init: function(editor) {
      //  array of strings to choose from that'll be inserted into the editor
      var strings = [];
      var o_b = '&lcub;&lcub;';
      var c_b = '&rcub;&rcub;';  

      strings.push([o_b+'#invoice_no'+c_b, 'Invoice No']);
      
      strings.push([o_b+'#created_date'+c_b, 'Created Date']);

      strings.push([o_b+'#company_name_and_logo'+c_b, 'Company Name And Logo']);

      strings.push([o_b+'#company_address'+c_b, 'Company Address']);
      
      strings.push([o_b+'#client_name'+c_b, 'Client Name']);

      strings.push([o_b+'#client_email'+c_b, 'Client Email']);

      strings.push([o_b+'#gst_no'+c_b, 'GST No']);

      strings.push([o_b+'#total_amount'+c_b, 'Total Amount']);
      
      strings.push([o_b+'#sgst_amount'+c_b, 'SGST Amount']);
      
      strings.push([o_b+'#sgst'+c_b, 'SGST %']);
      
      strings.push([o_b+'#cgst'+c_b, 'CGST %']);

      strings.push([o_b+'#cgst_amount'+c_b, 'CGST Amount']);

      strings.push([o_b+'#igst_amount'+c_b, 'IGST Amount']);

      strings.push([o_b+'#payment_status'+c_b, 'Payment Status']);

      strings.push([o_b+'#payment_mode'+c_b, 'Payment Mode']);

      strings.push([o_b+'#payment_date'+c_b, 'Payment Date']);

      strings.push([o_b+'#bank_name'+c_b, 'Bank Name']);

      strings.push([o_b+'#transaction_no'+c_b, 'Transaction Number']);

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

CKEDITOR.replace( 'kt_ckeditor4_2',{
      height: 400,
      allowedContent : true,
      removeButtons : 'Image,Source,About,Scayt',
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

// jQuery(document).ready(function() {
//     KTTinymce.init();
// });
</script>


<!-- <script src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/plugins/custom/tinymce/tinymce.bundle.js?v=7.2.8"></script> -->
@endsection