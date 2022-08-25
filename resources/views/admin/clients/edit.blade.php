{{-- Extends layout --}}
@extends('admin.layouts.default')

@section('content')
	<h3 class="form_title">{{ __('Edit Client :') }} <span>({{$client->name}})</span></h3>
<hr>   
 @include('admin.layouts.alert')
    {!! Form::model($client, [
            'method' => 'PATCH',
            'route' => ['admin.clients.update', $client->id],
			'class' => 'ui-form',
            'id' => 'clientForm',
            'files' => true
            ]) !!}
    @include('admin.clients.form', ['submitButtonText' => __('Update client')])

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