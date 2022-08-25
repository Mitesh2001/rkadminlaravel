


<!DOCTYPE html>
<html>
<head></head>
<body class="hold-transition login-page">
    <form action="{{ route('admin.password.email') }}" method="post">
        @method('POST')
        @csrf
        <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="email" name="email" class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Reset Link</button>
            </div>
            <!-- /.col -->
        </div>
    </form>
    <div class="clearfix">&nbsp;</div>
    <a href="{{ route('admin.login') }}">Login ? Click Here</a><br/>
</body>
</html>
