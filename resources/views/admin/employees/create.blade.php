@extends('admin.layouts.master')
@section('content')
@section('styles')
<style>
    #toggle-password{
        float: right;
        margin-top: -26px !important;
        margin-right: 10px;
    }
    .family-btn{
        padding: 6px 7px 8px 9px;
    }
</style>
@endsection

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
       
    </script>
@endpush

    <?php
    $data = Session::get('data');
 
    if(!empty($companyId)){ 
        $title_diff = 'Employee'; 
    }else{
        $title_diff = 'User'; 
    }
    ?>
<h3 class="form_title">Create {{$title_diff}} @if(!empty($companyData))
    : <span>Client Name : {{$companyData->client_data['name']}} &nbsp; | Company Name : {{$companyData->company_name}}</span>
@endif</h3>
<hr>
 @include('admin.layouts.alert')
    {!! Form::open([
            'route' => 'admin.employees.store',
            'class' => 'ui-form',
            'id' => 'employeeCreateForm',
			'enctype' => 'multipart/form-data'
            ]) !!}
            <?php
           
            if(!empty($companyId)){ 
                $btn_label = 'Create New Employee'; 
            }else{
                $btn_label = 'Create New User'; 
            }
            ?>

    @include('admin.employees.form', ['submitButtonText' => $btn_label])

    {!! Form::close() !!}
@stop
@section('scripts')
    <script>
        var relations = @json($relations);
        $(document).ready(function(){
            var familyId = $('.add-family-btn').data('id');
            if(familyId == 1){
                $('.add-family-btn').click();
            }
            var employeeId = $('.add-previous-employer-btn').data('id');
            if(employeeId == 1){
                $('.add-previous-employer-btn').click();
            }
        });
    </script>
    <!--<script src="{{ asset('js/employee.js') }}?v=0.0.1" type="text/javascript"></script>-->
@endsection

