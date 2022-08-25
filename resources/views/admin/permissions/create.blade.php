@extends('admin.layouts.master')
@section('content')
@push('scripts')
    <script>
	{{-- $(document).ready(function () {
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
            if(!getCookie("step_permission_create"))
            {
                // Instance the tour
                $("#permissions").addClass( "in" );
                var tour = new Tour({
                    storage: false,
                    backdrop:true,
                    steps: [

                        {
                            element: "#permissionCreateForm",
                            title: "{{trans("Fill out the form")}}",
                            content: "{{trans("Fill out the form to get started, the only required fields are name, company name, and email")}}",
                            placement:'top'
                        },
                        {
                            element: "#submitpermission",
                            title: "{{trans("Click the submit button")}}",
                            content: "{{trans("Click the create new permission button, and you're done")}}",
                            placement:'top'
                        }
                    ]});

                // Initialize the tour
                tour.init();

                tour.start();
                setCookie("step_permission_create", true, 1000)
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
        }); --}}
    </script>
@endpush

    <?php
    $data = Session::get('data');
    ?>

<div class="card card-custom">
<div class="card-header">
<div class="card-title">
	<span class="card-icon">
		<i class="flaticon2-supermarket text-primary"></i>
	</span>
	<h3 class="card-label">{{ __('Creae Permission') }}</h3>
</div>
</div>
<div class="card-body">
  @include('admin.layouts.alert')
    {!! Form::open([
            'route' => 'admin.permissions.store',
            'class' => 'ui-form',
            'id' => 'permissionCreateForm'
            ]) !!}
    @include('admin.permissions.form', ['submitButtonText' => __('Create New permission')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

@stop
