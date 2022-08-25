
    <div class="form-group row">
        <div class="col-lg-6">
            {!! Form::label('name', __('Name'), ['class' => '']) !!}
            <span class="text-danger">*</span>
            {!! 
                Form::text('name',
                isset($plan['name']) ? $plan['name'] : null, 
                ['class' => 'form-control','required']) 
            !!}
            <span class="form-text text-muted">Please enter name</span>
        </div>
        <div class="col-lg-6">
            {!! Form::label('price', __('Price'). ':', ['class' => '']) !!}
            <span class="text-danger">*</span>
            {!! 
                Form::text('price',
                isset($plan['price']) ? $plan['price'] : null, 
                ['class' => 'form-control valid-price-number','maxlength'=>'8','required']) 
            !!}
            <span class="form-text text-muted">Please enter price</span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {!! Form::label('number_of_users', __('Number Of Users'), ['class' => '']) !!}
            <span class="text-danger">*</span>
            {!! 
                Form::text('no_of_users',
                isset($plan['no_of_users']) ? $plan['no_of_users'] : null, 
                ['class' => 'form-control valid-number','maxlength'=>'4','required']) 
            !!}
            <span class="form-text text-muted">Please enter number of users</span>
        </div>
        <div class="col-lg-6">
            {!! Form::label('duration_month', __('Duration In Months'). ':', ['class' => '']) !!}
            <span class="text-danger">*</span>
            {!! 
                Form::text('duration_months',
                isset($plan['duration_months']) ? $plan['duration_months'] : null, 
                ['class' => 'form-control valid-number','maxlength'=>'2','required']) 
            !!}
            @if(!empty($plan))
                {{Form::hidden('plan_id',encrypt($plan->id))}}
            @endif
        <span class="form-text text-muted">Please enter duration in months</span>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {!! Form::label('description', __('Description'), ['class' => '']) !!}
            {!! 
                Form::textarea('description',
                isset($plan['description']) ? $plan['description'] : null, 
                ['class' => 'form-control','rows'=>'2']) 
            !!}
            <span class="form-text text-muted">Please enter description</span>
        </div>
        <div class="col-lg-3">
            {!! Form::label('email', __('Email'), ['class' => '']) !!}
            {!! 
                Form::text('no_of_email',
                isset($plan['no_of_email']) ? $plan['no_of_email'] : null, 
                ['class' => 'form-control valid-number','maxlength'=>'10']) 
            !!}
            <span class="form-text text-muted">Please enter no of email</span>
        </div>
        <div class="col-lg-3">
            {!! Form::label('sms', __('SMS'), ['class' => '']) !!}
            {!! 
                Form::text('no_of_sms',
                isset($plan['no_of_sms']) ? $plan['no_of_sms'] : null, 
                ['class' => 'form-control valid-number','maxlength'=>'10']) 
            !!}
            <span class="form-text text-muted">Please enter no of sms</span>
        </div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col-lg-6">
                {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitpermission']) !!}
                <a href="{{url('rkadmin/plan')}}" class="btn btn-md btn-primary ml-2">Cancel</a>
            </div>
        </div>
    </div>
	@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection