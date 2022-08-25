@extends('admin.layouts.master')
@section('content')
@include('admin.layouts.alert')

<div class="row">
@if(!empty($settings))
    @if($settings['is_tested'] == 0)
    <div class="col-sm-12">
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Test is required</strong>
        </div>
    </div>
    @endif

    @if($settings['is_working'] == 0)
    <div class="col-sm-12">
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Currenty SMS will not send</strong>
        </div>
    </div>
    @endif
@endif 
</div>


<div class="card card-custom mb-3">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-email text-primary"></i>
            </span>
            <h3 class="form_title">SMS API Setting : <span>Update</span></h3>
        </div>
    </div>
    <div class="card-body">  
        
        {!! Form::open(['route' => 'admin.sms.store', 'id' => 'sms-setting']) !!}
            <div class="form-group row">
                <div class="col-lg-8">
                    {!! Form::label('api_url', __('API'), ['class' => '']) !!}
                    {!! 
                        Form::text('api_url',
                        isset($settings['api_url']) ? $settings['api_url'] : null, 
                        ['class' => 'form-control change_preview', 'required' => true, 'maxlength' => 256]) 
                    !!}
                    {{Form::hidden('id',!empty($settings) ? $settings->email_template_id : null)}}
                    <small>Case Sensitive</small>
                </div> 
            </div>
            <div class="form-group row">
                <div class="col-lg-8">
                    {!! Form::label('mobile_param', __('Number Parameter'), ['class' => '']) !!}
                    {!! 
                        Form::text('mobile_param',
                        isset($settings['mobile_param']) ? $settings['mobile_param'] : null, 
                        ['class' => 'form-control change_preview', 'required' => true, 'maxlength' => 256]) 
                    !!}
                    {{Form::hidden('id',!empty($settings) ? $settings->email_template_id : null)}}
                    <small>Case Sensitive</small>
                </div> 
            </div>
            <div class="form-group row">
                <div class="col-lg-8">
                    {!! Form::label('msg_param', __('Message Parameter'), ['class' => '']) !!}
                    {!! 
                        Form::text('msg_param',
                        isset($settings['msg_param']) ? $settings['msg_param'] : null, 
                        ['class' => 'form-control change_preview', 'required' => true, 'maxlength' => 256]) 
                    !!}
                    {{Form::hidden('id',!empty($settings) ? $settings->email_template_id : null)}}
                    <small>Case Sensitive</small>
                </div> 
            </div>
            <div class="form-group row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <span class="d-flex flex-column"> 
                                        Parameter
                                        <small>Case Sensitive</small>
                                    </span>
                                </th>
                                <th>
                                    <span class="d-flex flex-column">
                                        Value
                                        <small>Case Sensitive</small>
                                    </span> 
                                </th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="parameters-table"> 
  
                            @if(isset($settings['parameters']) && count(json_decode($settings['parameters'])))
                            <?php $x = 1; ?>
                                @foreach(json_decode($settings['parameters']) as $parameter) 
                                    <tr id="row_{{ $x }}">
                                        <td>
                                            <input type="text" name="parameters[{{ $x }}]" id="parameter_{{ $x }}" class="form-control parameter parameter_{{ $x }} change_preview" value="{{ $parameter->key }}" required="true" maxlength="256">
                                        </td>
                                        <td>
                                            <input type="text" name="values[{{ $x }}]" id="value_{{ $x }}" class="form-control value value_{{ $x }} change_preview" value="{{ $parameter->value }}" required="true" maxlength="256">
                                        </td>
                                        <td>
                                            <a class="btn btn-link p-1" onClick="if(confirm('Are you sure?')) removeParameter({{$x}}, true);"><i class="flaticon2-trash text-danger"></i></a>
                                        </td>
                                    </tr>
                                    <?php $x++; ?>
                                @endforeach
                            @else 
                                <tr id="row_1">
                                    <td>
                                        <input type="text" name="parameters[1]" id="parameter_1" class="form-control parameter parameter_1 change_preview" required="true" maxlength="256">
                                    </td>
                                    <td>
                                        <input type="text" name="values[1]" id="value_1" class="form-control value value_1 change_preview" required="true" maxlength="256">
                                    </td>
                                    <td>
                                        <a class="btn btn-link p-1"><i class="flaticon2-trash text-danger"></i></a>
                                    </td>
                                </tr>
                            @endif
 
                        </tbody>
                    </table> 
                </div>  
            </div>
            <div class="form-group row">
                <div class="col-lg-6"> 
                    <a class="btn btn-md btn-primary ml-2" onclick="addParameter()">Add Parameter</a>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    {!! Form::label('url_preview', __('URL Preview'), ['class' => '']) !!}
                    {!! 
                        Form::text('url_preview',
                        isset($settings['final_url']) ? $settings['final_url'] : null, 
                        ['class' => 'form-control', 'required' => true, 'readonly' => true]) 
                    !!} 
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

<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-email text-primary"></i>
            </span>
            <h3 class="form_title">Test API</h3>
        </div>
    </div>
    <div class="card-body">  
        
        {!! Form::open(['route' => 'admin.sms.test_api', 'id' => 'sms-test-form']) !!}
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('number', __('Number'), ['class' => '']) !!}
                    {!! 
                        Form::text('number',
                        null, 
                        ['class' => 'form-control', 'required' => true, 'minlength' => 10, 'maxlength' => 10]) 
                    !!} 
                </div> 
                <div class="col-lg-6">
                    {!! Form::label('message', __('Message'), ['class' => '']) !!}
                    {!! 
                        Form::text('message',
                        isset($settings['message']) ? $settings['message'] : null, 
                        ['class' => 'form-control', 'required' => true, 'maxlength' => 256]) 
                    !!} 
                </div> 
            </div> 
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::submit('Send SMS', ['class' => 'btn btn-md btn-primary']) !!} 
                    </div>
                </div>
            </div>
        {{Form::close()}}
    </div>
</div>

@stop
@section('scripts')
<script>
    
    // Add Parameter
    function addParameter()
    {
        let dynamic_number = $("#parameters-table tr").length; 
        dynamic_number++;

        let row = `<tr id="row_${dynamic_number}"> <td> <input type="text" name="parameters[${dynamic_number}]" id="parameter_${dynamic_number}" class="form-control parameter parameter_${dynamic_number} change_preview"> </td> <td> <input type="text" name="values[${dynamic_number}]" id="value_${dynamic_number}" class="form-control value value_${dynamic_number} change_preview"> </td> <td> <a onClick="removeParameter(${dynamic_number})" class="btn btn-link p-1"><i class="flaticon2-trash text-danger"></i></a> </td> </tr>`;
        $("#parameters-table").append(row);
    } 

    // Remove row
    function removeParameter(id, ajax = false)
    {
        $(`#row_${id}`).remove();
        var token = "{{csrf_token()}}";
        getUrl();
        var form_data = $("#sms-setting").serialize();

        if(ajax == true) {
            $.ajax({
                url:"{{ route('admin.sms.updateParameters') }}",
                type:'POST',
                dataType: 'json',
                data:{
                    _token : token,
                    form_data : form_data,
                },
                success: function (e) {  
                    if(e.status == true) {
                        swal.fire({
                            text: e.message,
                            icon: 'success',
                            buttonsStyling: false, 
                            confirmButtonClass: 'btn btn-primary font-weight-bold'
                        });
                        setTimeout(() => { 
                            location.reload(); 
                        }, 1000);
                    } else {
                        swal.fire({
                            text: "Something went to wrong!",
                            icon: 'danger',
                            buttonsStyling: false, 
                            confirmButtonClass: 'btn btn-primary font-weight-bold'
                        });  
                    }   
                }
            });
        }  
    }

    function getUrl()
    {
        let url = $("#api_url").val(); 

        url += "?";
 
        $(".parameter").each(function (i, element) {
             
            let param = $(element).val();
            let param_value = $(element).closest('tr').find('.value').val();
              
            url += param + "=" + param_value + "&"; 
        }); 

        let number_param = $("#mobile_param").val();
        let message_param = $("#msg_param").val();

        url += number_param + "=99XXXXXXXX&";
        url += message_param + "=Message...";
 
        $("#url_preview").val(url);
    }

    $(document).on('change', '.change_preview', function() {
        getUrl();
    });

</script> 

@endsection      
	