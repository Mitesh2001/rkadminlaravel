@extends('admin.layouts.master')
@section('content')
    
@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true
                });
            });
        });
        $(document).ready(function () {
            if(!getCookie("step_client_create"))
            {
                // Instance the tour
                $("#clients").addClass( "in" );
                var tour = new Tour({
                    storage: false,
                    backdrop:true,
                    steps: [

                        {
                            element: "#clientCreateForm",
                            title: "{{trans("Fill out the form")}}",
                            content: "{{trans("Fill out the form to get started, the only required fields are name, company name, and email")}}",
                            placement:'top'
                        },
                        {
                            element: "#submitClient",
                            title: "{{trans("Click the submit button")}}",
                            content: "{{trans("Click the create new client button, and you're done")}}",
                            placement:'top'
                        }
                    ]});

                // Initialize the tour
                tour.init();

                tour.start();
                setCookie("step_client_create", true, 1000)
            }
            function setCookie(key, value, expiry) {
                var expires = new Date();
                expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 2000));
                document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
            }

            function getCookie(key) {
                var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
                return keyValue ? keyValue[2] : null;
            }
        });
    </script>
@endpush

    <?php
    $data = Session::get('data');
    ?>
<h3 class="form_title">Create Client</h3>
<hr>
  
 @include('admin.layouts.alert')
    {!! Form::open([
            'route' => 'admin.clients.store',
            'class' => 'ui-form',
            'id' => 'clientCreateForm',
            'files' => true
            ]) !!}
    @include('admin.clients.form', ['submitButtonText' => __('Create New Client')])

    {!! Form::close() !!}
@stop
@section('scripts')
    <script>
        var companytypes = @json($companytypes);
        var industries = @json($industries);
        var licensetypes = @json($licensetypes);
        var establishYears = @json($establishYears);
        var plans = @json($plans);
        var states = @json($states);
        var countries = @json($countries);
        $(document).ready(function(){
			jQuery('.client_info .country-val').val('101').trigger('change');
            var companyId = $('.add-company').data('id');
            if(companyId == 1){
                $('.add-company').click();
            }
        });
		
        jQuery(document).on('change','.country_id-select',function(){
            var country_id_select = $(this).val();
            var parent_div = jQuery(this).data('div');
            jQuery(parent_div+' .state_id_select').select2({allowClear: true,placeholder: "Please select state"}).empty();
                jQuery.ajax({
                    url: '{!! route("admin.getstate") !!}',
                    data: {country_id:country_id_select},
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
            
                        if(response.success){
                            jQuery(parent_div+' .state_id_select').select2({
                                placeholder: 'Please select state',
                                data: response.states
                            }).trigger('change')						
                        }
                    }
                });
        });

        jQuery(document).on('change','.state_id_select',function(){
            var state_id_select = $(this).val();
            var parent_div = jQuery(this).data('div');

            jQuery(parent_div+' .city-select').select2({allowClear: true,placeholder: "Please select city"}).empty();
            jQuery.ajax({
                url: '{!! route("admin.getcity") !!}',
                data: {state_name:state_id_select},
                type: 'get',
                dataType: 'json',
                success: function(response){
        
                    if(response.success){
                        jQuery(parent_div+' .city-select').select2({
                            placeholder: 'Please select city',
                            data: response.cities
                        }).trigger('change')
                    }
                }
            });
        });

        jQuery(document).on('change','.city-select',function(){
            var city_id_select = $(this).val();
            var parent_div = jQuery(this).data('div');
            
            jQuery(parent_div+' .postcode-select').select2({allowClear: true,placeholder: "Please select post code"}).empty();
            jQuery.ajax({
                url: '{!! route("admin.getpostcode") !!}',
                data: {city_name:city_id_select},
                type: 'get',
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        jQuery(parent_div+' .postcode-select').select2({
                            placeholder: 'Please select post code',
                            data: response.postcodes
                        });
                    }
                }
            });
        });
       
    </script>
    <script src="{{ asset('js/clientform.js') }}" type="text/javascript"></script>
@endsection