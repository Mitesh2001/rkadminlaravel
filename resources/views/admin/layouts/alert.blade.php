@if(isset($errors) && $errors->any())
<div class="col-sm-12">
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! implode('', $errors->all('<div>:message</div>')) !!}</strong>
    </div>
</div>
@endif

@if ($message = Session::get('success'))
<div class="col-sm-12 p-0">
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
</div>
@endif

@if ($message = Session::get('error'))
<div class="col-sm-12 p-0">
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
</div>
@endif