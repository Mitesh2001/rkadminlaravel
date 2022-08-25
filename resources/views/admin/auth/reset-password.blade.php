<!DOCTYPE html>
<html>
<head></head>
<body class="hold-transition login-page">
    <form action="{{ route('admin.password.request') }}" method="post">
        @method('POST')
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group has-feedback">
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' has-error' : '' }}" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" name="password" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?^)-+_^(&*]).{8,20}$" placeholder="Password">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <input type="password" name="password_confirmation" class="form-control"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%?^)-+_^(&*]).{8,20}$" placeholder="Password Confirmation">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
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
