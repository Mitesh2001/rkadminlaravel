@extends('admin.layouts.master')
@section('content')
    @section('styles')
        <style>
            .plus-btn{
                padding: 3px 2px 5px 5px;
            }
            .add-field-div{
                padding: 7px 6px 9px 9px;
            }
            .plus-minus-div{
                margin-top: 11px !important;
            }
        </style>
    @endsection
<h1>Manage Custom Form</h1>
@if(!empty($companyData))
    <h5>Client Name : {{$companyData->client_data['name']}} &nbsp; | Company Name : {{$companyData['company_name']}}</h5>
@endif
<hr>
 @include('admin.layouts.alert')
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">
                Manage Form
            </h3>
        </div>
        <div class="card-body">
            {{Form::open(['url' => ['rkadmin/custom-form/'.$companyId],'type'=>'POST','class' => 'ui-form'])}}
                {{Form::hidden('form_type','1')}}
                <div class="form-group row">
                    {{Form::hidden('company_id',$companyId)}}
					{{Form::hidden('oldModuleType',$moduleType)}}
                    <div class="col-lg-3">
                        {!! Form::label('module_name', __('Module Name'), ['class' => '']) !!}
                        {!! 
                            Form::select('module_name',$moduleName,
                            $moduleType, 
                            ['class' => 'form-control','required']) 
                        !!}
                        <span class="form-text text-muted">Please select module name</span>
                    </div>
                </div>
                @php
                    $sectionKey = null;
                @endphp
                @if(!empty($formData))
                    @foreach($formData as $key=>$row)
                        @php
                            $key = $row->id;
                            $sectionKey = $key;
                        @endphp
                        <hr class="{{'input-field-div-'.$key.' remove-section-data-'.$key}}">
                        <div class="{{'form-group row remove-section-data-'.$key}}">
                            <div class="col-lg-3">
                                {{Form::hidden('section['.$key.'][is_edit_section]','1')}}
                                {!! Form::label('section_name', __('Section Name'), ['class' => 'font-weight-bold']) !!}
                                {!! 
                                    Form::text('section['.$key.'][name]',
                                    isset($row['name']) ? $row['name'] : null, 
                                    ['class' => 'form-control','required']) 
                                !!}
                                <span class="form-text text-muted">Please enter section name</span>
                            </div>
                            <div class="col-lg-2">
                                {!! Form::label('section_priority', __('Section Priority'), ['class' => 'font-weight-bold']) !!}
                                {!! 
                                    Form::text('section['.$key.'][priority]',
                                    isset($row['priority']) ? $row['priority'] : null, 
                                    ['class' => 'form-control valid-number','required','maxlength'=>'3']) 
                                !!}
                                <span class="form-text text-muted">Please enter section priority</span>
                            </div>
                            <div class='col-md-1'>
                                <a href='javascript:;' class='btn btn-primary font-weight-bold btn-pill mt-5 remove-section add-field-div' data-id="{{$key}}"><i class='fa fa-minus'></i></a>
                            </div>
                        </div>
                        <div class="{{'form-group row remove-section-data-'.$key}}">
                            <div class="col-md-2 plus-minus-div">
                                <a href="javascript:;" class="btn btn-success font-weight-bold btn-pill mt-5 plus-btn add-field-div add-field-data" data-id="{{$key}}" data-sid="{{$key}}">Add Field &nbsp;<i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        
                        {{-- @if($loop->iteration == 1)
                            <div class="{{'form-group row'}}">
                                <div class="col-lg-3">
                                    {!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
                                    {!! 
                                        Form::select('field_data['.$key.'][1][input_type]',$inputTypes,1,
                                        ['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
                                    !!}
                                    {{Form::hidden('field_data['.$key.'][1][input_type]','1')}}
                                    {{Form::hidden('field_data['.$key.'][1][pre_field]','1')}}
                                    <span class="form-text text-muted">Please select input type</span>
                                </div>
                                <div class="col-lg-2">
                                    {!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][1][label_name]','name',
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter label name</span>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.'][1][is_required]','1',true) !!}
                                            <span></span>
                                            Is Required ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][1][minlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter minlength</span>
                                </div>
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][1][maxlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter maxlength</span>
                                </div>
                            </div>
                            <hr>

                            <div class="{{'form-group row'}}">
                                <div class="col-lg-3">
                                    {!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
                                    {!! 
                                        Form::select('field_data['.$key.'][2][input_type]',$inputTypes,1,
                                        ['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
                                    !!}
                                    {{Form::hidden('field_data['.$key.'][2][input_type]','1')}}
                                    {{Form::hidden('field_data['.$key.'][2][pre_field]','1')}}
                                    <span class="form-text text-muted">Please select input type</span>
                                </div>
                                <div class="col-lg-2">
                                    {!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][2][label_name]','email',
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter label name</span>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.'][2][is_required]','1',true) !!}
                                            <span></span>
                                            Is Required ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][2][minlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter minlength</span>
                                </div>
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][2][maxlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter maxlength</span>
                                </div>
                            </div>
                            <hr>

                            <div class="{{'form-group row'}}">
                                <div class="col-lg-3">
                                    {!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
                                    {!! 
                                        Form::select('field_data['.$key.'][3][input_type]',$inputTypes,1,
                                        ['class' => 'form-control input-type searchpicker','required','disabled','data-id'=>1]) 
                                    !!}
                                    {{Form::hidden('field_data['.$key.'][3][input_type]','1')}}
                                    {{Form::hidden('field_data['.$key.'][3][pre_field]','1')}}
                                    <span class="form-text text-muted">Please select input type</span>
                                </div>
                                <div class="col-lg-2">
                                    {!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][3][label_name]','password',
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter label name</span>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.'][3][is_required]','1',true) !!}
                                            <span></span>
                                            Is Required ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][3][minlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter minlength</span>
                                </div>
                                <div class="{{'col-lg-4 1-field-data-1 5-field-data-1 field-data-1'}}">
                                    {!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.'][3][maxlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter maxlength</span>
                                </div>
                            </div>
                        @endif --}}

                        @foreach($row->getFieldData as $valueKey=>$value)
                            @php
                                $valueKey = $value->id;
                                $selectStatus = $value['input_type'] == 2 ? '' : 'd-none';
                                $valueStatus = in_array($value['input_type'],['2','3','4']) ? '' : 'd-none';
                                $textStatus = in_array($value['input_type'],['1','5']) ? '' : 'd-none';
                                $numberStatus = $value['input_type'] == 6 ? '' : 'd-none';
                                $fieldValues = \Helper::getFieldValue($moduleType,$value->id);
                            @endphp 
                            <hr class="{{'input-field-div-'.$valueKey.' remove-section-data-'.$key}}">
                            <div class="{{'row input-field-div-'.$valueKey.' text-right remove-section-data-'.$key}}">
                                <div class='col-md-11'>
                                    <a href='javascript:;' class='btn btn-success font-weight-bold btn-pill mt-5 remove-field-value add-field-div' data-id="{{$valueKey}}"><i class='fa fa-minus'></i></a>
                                </div>
                            </div>
                            <div class="{{'form-group row input-field-div-'.$valueKey.' remove-section-data-'.$key}}">
                                <div class="col-lg-3">
                                    {{Form::hidden('field_data['.$key.']['.$valueKey.'][is_edit_field]','1')}}
                                    {{Form::hidden('field_data['.$key.']['.$valueKey.'][pre_field]',$value['is_pre_field'])}}
                                    {!! Form::label('input_type', __('Input Type'), ['class' => '']) !!}
                                    {!! 
                                        Form::select('field_data['.$key.']['.$valueKey.'][input_type]',$inputTypes,
                                        isset($value['input_type']) ? $value['input_type'] : null, 
                                        ['class' => 'form-control input-type searchpicker','required','data-id'=>$valueKey]) 
                                    !!}


                                    <span class="form-text text-muted">Please select input type</span>
                                </div>
                                <div class="col-lg-2">
                                    {!! Form::label('label_name', __('Label Name'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][label_name]',
                                        isset($value['label_name']) ? $value['label_name'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter label name</span>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.']['.$valueKey.'][is_required]','1',isset($value['is_required']) ? $value['is_required'] : null) !!}
                                            <span></span>
                                            Is Required ?
                                        </label>
                                    </div>
                                </div>
                                <div class="{{'col-md-2 2-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$selectStatus}}">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.']['.$valueKey.'][is_search]','1',isset($value['is_searchable']) ? $value['is_searchable'] : null) !!}
                                            <span></span>
                                            Is Searchable ?
                                        </label>
                                    </div>
                                </div>
                                <div class="{{'col-md-3 2-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$selectStatus}}">
                                    <br>
                                    <div class="checkbox-inline mt-3">
                                        <label class="checkbox checkbox-square">
                                            {!! Form::checkbox('field_data['.$key.']['.$valueKey.'][is_multiple_select]','1',isset($value['is_select_multiple']) ? $value['is_select_multiple'] : null) !!}
                                            <span></span>
                                            Can select multiple values ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="{{'row form-group input-field-div-'.$valueKey.' remove-section-data-'.$key}}">
                                <div class="{{'col-lg-4 1-field-data-'.$valueKey.' 5-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$textStatus}}">
                                    {!! Form::label('minlength', __('Minlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][minlength]',
                                        isset($value['minlength']) ? $value['minlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter minlength</span>
                                </div>
                                <div class="{{'col-lg-4 1-field-data-'.$valueKey.' 5-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$textStatus}}">
                                    {!! Form::label('maxlength', __('Maxlength'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][maxlength]',
                                        isset($value['maxlength']) ? $value['maxlength'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter maxlength</span>
                                </div>
                                <div class="{{'col-lg-4 1-field-data-'.$valueKey.' 5-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$textStatus}}">
                                    {!! Form::label('pattern', __('Pattern'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][pattern]',
                                        isset($value['pattern']) ? $value['pattern'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter pattern</span>
                                </div>
                                <div class="{{'col-lg-6 6-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$numberStatus}}">
                                    {!! Form::label('min_value', __('Min Value'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][min_value]',
                                        isset($value['minvalue']) ? $value['minvalue'] : null, 
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter min value</span>
                                </div>
                                <div class="{{'col-lg-6 6-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$numberStatus}}">
                                    {!! Form::label('max_value', __('Max Value'), ['class' => '']) !!}
                                    {!! 
                                        Form::text('field_data['.$key.']['.$valueKey.'][max_value]',
                                        isset($value['maxvalue']) ? $value['maxvalue'] : null,
                                        ['class' => 'form-control']) 
                                    !!}
                                    <span class="form-text text-muted">Please enter max value</span>
                                </div>
                            </div>
                            <div class="{{'form-group row field-value-data-'.$valueKey.' input-field-div-'.$valueKey.' remove-section-data-'.$key}}">
                                <div class="{{'col-lg-4 2-field-data-'.$valueKey.' 4-field-data-'.$valueKey.' 3-field-data-'.$valueKey.' field-data-'.$valueKey.' '.$valueStatus}}">
                                    <label>Value</label>
                                    <textarea name="{{'field_data['.$key.']['.$valueKey.'][values]'}}" class="form-control" cols="50" rows="4">@foreach ($fieldValues as $item){{$item}}{{"\n"}}@endforeach</textarea>
                                    <span class='form-text text-muted'>Please enter value with enter key</span>
                                </div>
                            </div>
                        @endforeach
                        <div class="{{'input-field-data-'.$sectionKey}}"></div>
                    @endforeach
                @endif
                {{-- append new field --}}
                <div class="input-field-data"></div>
                <hr>
                <div class="form-group row">
                    <div class="col-md-3 plus-minus-div">
                        <a href="javascript:;" class="btn btn-primary font-weight-bold btn-pill mt-5 plus-btn add-section add-field-div" data-id="1">Add Section &nbsp;<i class="fa fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        {!! Form::submit('Submit', ['class' => 'btn btn-md btn-primary']) !!}
                        <a href="{{url('rkadmin/custom-form/'.encrypt($companyId))}}" class="btn btn-md btn-primary ml-2">Cancel</a>
                    </div>
                </div>
            {{Form::close()}}
        </div>
    </div>
@stop
@section('scripts')
    <script>
        var inputTypes = @json($inputTypes);
    </script>
    <script src="{{ asset('js/dynamic_form.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection