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
<h1>Create Custom Form</h1>
@if(!empty($companyData))
    <h5>Client Name : {{$companyData->client_data['name']}} &nbsp; | Company Name : {{$companyData['company_name']}}</h5>
@endif
<hr>
    @include('admin.layouts.alert')
    @include('admin.dynamic.form', ['submitButtonText' => __('Create Custom Form')])
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
