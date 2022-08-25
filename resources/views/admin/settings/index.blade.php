@extends('admin.layouts.master')
@section('content')
@include('admin.layouts.alert')

<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon-settings text-primary"></i>
            </span>
            <h3 class="form_title">Global Settings</h3>
        </div>
    </div>
    <div class="card-body">

        {!! Form::open(['route' => 'admin.settings.store', 'id' => 'global-settings-form']) !!}
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('app_name', __('Application Name'), ['class' => '']) !!}
                    {!!
                        Form::text('app_name',
                        \Helper::getSetting('app_name') ?? "Samvidha",
                        ['class' => 'form-control',
                        'placeholder' => 'Application Name',
                        'required' => true])
                    !!}
                </div>
				<div class="col-lg-6">
                    {!! Form::label('app_link', __('Application Link'), ['class' => '']) !!}
                    {!!
                        Form::text('app_link',
                        \Helper::getSetting('app_link') ?? "",
                        ['class' => 'form-control',
                        'placeholder' => 'Application Link',
                        'required' => true])
                    !!}
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('mail_from', __('Mail From (Default)'), ['class' => '']) !!}
                    {!!
                        Form::email('mail_from',
                        \Helper::getSetting('mail_from'),
                        ['class' => 'form-control',
                        'placeholder' => 'Mail From (Default)',
                        'required' => true])
                    !!}
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-6">
                    {!! Form::label('mail_from_name', __('Mail From Name (Default)'), ['class' => '']) !!}
                    {!!
                        Form::text('mail_from_name',
                        \Helper::getSetting('mail_from_name'),
                        ['class' => 'form-control',
                        'placeholder' => 'Mail From Name (Default)',
                        'required' => true])
                    !!}
                </div>
            </div>
            <br>
            <h3 class="text-muted">Subscription Mail Setting</h3>
            <hr>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_1', __('Subscription Mail 1'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_1',
						$alltemplates,
						\Helper::getSetting('sb_mail_1'),
						['class' => 'form-control',
						'id' => 'sb_mail_1','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_1', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_1',
                        \Helper::getSetting('sb_mail_day_1'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_1_status" name="sb_mail_1_status" <?php if(\Helper::getSetting('sb_mail_1_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_1_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_2', __('Subscription Mail 2'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_2',
						$alltemplates,
						\Helper::getSetting('sb_mail_2'),
						['class' => 'form-control',
						'id' => 'sb_mail_2','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_2', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_2',
                        \Helper::getSetting('sb_mail_day_2'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_2_status" name="sb_mail_2_status" <?php if(\Helper::getSetting('sb_mail_2_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_2_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_3', __('Subscription Mail 3'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_3',
						$alltemplates,
						\Helper::getSetting('sb_mail_3'),
						['class' => 'form-control',
						'id' => 'sb_mail_3','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_3', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_3',
                        \Helper::getSetting('sb_mail_day_3'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_3_status" name="sb_mail_3_status" <?php if(\Helper::getSetting('sb_mail_3_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_3_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_4', __('Subscription Mail 4'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_4',
						$alltemplates,
						\Helper::getSetting('sb_mail_4'),
						['class' => 'form-control',
						'id' => 'sb_mail_4','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_4', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_4',
                        \Helper::getSetting('sb_mail_day_4'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_4_status" name="sb_mail_4_status" <?php if(\Helper::getSetting('sb_mail_4_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_4_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_5', __('Subscription Mail 5'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_5',
						$alltemplates,
						\Helper::getSetting('sb_mail_5'),
						['class' => 'form-control',
						'id' => 'sb_mail_5','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_5', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_5',
                        \Helper::getSetting('sb_mail_day_5'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_5_status" name="sb_mail_5_status" <?php if(\Helper::getSetting('sb_mail_5_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_5_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
				<div class="col-lg-4">
                    {!! Form::label('sb_mail_6', __('Subscription Mail 6'), ['class' => '']) !!}
                    {!!
						Form::select('sb_mail_6',
						$alltemplates,
						\Helper::getSetting('sb_mail_6'),
						['class' => 'form-control',
						'id' => 'sb_mail_6','placeholder'=>'Please Select Template'])
					!!}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('sb_mail_day_6', __('Number of days'), ['class' => '']) !!}
                    {!!
                        Form::number('sb_mail_day_6',
                        \Helper::getSetting('sb_mail_day_6'),
                        ['class' => 'form-control',
                        'placeholder' => 'Number of days',
                        'number' => true])
                    !!}
                </div>
                <div class="col-lg-4">
                    <div class="form-check mt-6">
                        <input class="form-check-input" type="checkbox" id="sb_mail_6_status" name="sb_mail_6_status" <?php if(\Helper::getSetting('sb_mail_6_status') == 1){ echo "checked"; }?>>
                        <label class="form-check-label" for="sb_mail_6_status">
                            Active / Inactive Mail
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::submit('Update', ['class' => 'btn btn-md btn-primary']) !!}
                    </div>
                </div>
            </div>
        {{Form::close()}}
    </div>
</div>

@stop

@section('scripts')
<script>

</script>
@endsection
