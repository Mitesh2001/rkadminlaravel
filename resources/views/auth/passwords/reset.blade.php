@extends('admin.layouts.app')

@section('content')
<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside d-flex flex-column flex-row-auto">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
						<!--begin::Aside header-->
						{{-- <a href="#" class="text-center mb-10">
							<img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-light.png" class="max-h-70px" alt="" />
						</a> --}}
						<!--end::Aside header-->
						<!--begin::Aside title-->
						<h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #986923;"></h3>
						<!--end::Aside title-->
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(/metronic/theme/html/demo1/dist/assets/media/svg/illustrations/login-visual-1.svg)"></div>
					<!--end::Aside Bottom-->
				</div>
				<!--begin::Aside-->

				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-center">
						<!--begin::Signin-->
						<div class="login-form login-signin">
							<!--begin::Form-->
							<form id="reset_password_form"; method="POST" action="{{ route('password.update') }}">

                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

								<!--begin::Title-->
								<div class="pb-13 pt-lg-0 pt-5 text-center">
									{{-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Welcome to RK CRM</h3> --}}
									{{-- <img src="{{ asset('media/logos/logo-light.png') }}" class="max-h-70px" alt="" /> --}}
									<img src="{{ asset('media/logos/logo-light.png') }}"  class="max-h-100px" alt="" />
								</div>
								@include('admin.layouts.alert')
								<!--begin::Title-->
								<!--begin::Form group-->
								<div class="form-group ({ $errors->has('email') ? ' has-error' : '' }}">
									<label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                                    <input id="email" type="email" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
									@if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->
								<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
									</div>
                                    <input id="password" type="password" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$" title = 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.'>

                                 @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
								@if (Session::has('message'))
                                        <span class="help-block">
                                        <strong>{{ Session::get('message') }}</strong>
                                    </span>
                                @endif
								</div>
								<!--begin::Form group-->
								<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
									<div class="d-flex justify-content-between mt-n5">
										<label class="font-size-h6 font-weight-bolder text-dark pt-5">Confirm Password</label>
									</div>
                                    <input id="password_confirm" type="password" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg  form-control" name="password_confirmation" required autocomplete="new-password" equalTo="#password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$" title = 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character. And confirm password should match with password'>

                                 @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
								@if (Session::has('message'))
                                        <span class="help-block">
                                        <strong>{{ Session::get('message') }}</strong>
                                    </span>
                                @endif
								</div>
								<!--end::Form group-->
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="submit" value="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3"> {{ __('Reset Password') }}</button>
								</div>
								<!--end::Action-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Content body-->
					<!--begin::Content footer-->
					<div class="d-flex justify-content-lg-start justify-content-center align-items-end py-7 py-lg-0">
						<div class="text-dark-50 font-size-lg font-weight-bolder mr-10">
							<span class="mr-1">{{ date("Y") }} &copy;</span>
							<a href="#" target="_blank" class="text-dark-75 text-hover-primary">{{ \Helper::getSetting('app_name') ?? "RKADMIN" }}</a>
						</div>

					</div>
					<!--end::Content footer-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>

	{{-- <script src="{{ asset('js/additional-methods.min.js') }}"></script>
	<script src="{{ asset('js/jquery.validate.min.js') }}"></script> --}}
@endsection

@section('scripts')
<script>
$(document).ready(function () {
	$("#reset_password_form").validate(/* {
		rules : {
                password_confirm : {
                    equalTo : "#password"
                }
			},
            messages: {
                password_confirm: "Enter Confirm Password Same as Password"
            }} */);
});
</script>
@endsection


