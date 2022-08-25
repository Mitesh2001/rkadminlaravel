<!DOCTYPE html>
<html>
<head></head>
<body class="hold-transition login-page">
    <!-- form start -->
<form action="{{ route('admin.change_password') }}" class="form-horizontal" method="post" id="frmDetail" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="box-body">
        <div class="form-group">
            <label for="password" class="control-label col-sm-2">Password</label>
            <div class="col-sm-6">
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter password">
            </div>
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label col-sm-2">Confirm Password</label>
            <div class="col-sm-6">
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm password">
                <input type="hidden" name="token" value="{{ $token }}">
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

</body>
</html>
