<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Password;

class ResetPassword extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/rkadmin';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('admin.auth.change_password')->with(['token' => $token, 'email' => $request->email]);
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function broker()
    {
        return Password::broker('admins');
    }
}
