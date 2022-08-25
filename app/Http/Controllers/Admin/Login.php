<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Login extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function index()
    {
        return view('admin.auth.login', [
            'title' => 'Login',
            'error' => ''
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (auth()->guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

            $user_type = auth()->guard('admin')->user()->type;
            $organization_id = auth()->guard('admin')->user()->organization_id;
            $company_id = auth()->guard('admin')->user()->company_id;

            if($user_type == 1  || $user_type == 3 || $user_type == 4){
                return redirect()->intended('/rkadmin');
            }else if($user_type == 2){
                    if($organization_id == 0  || $company_id == 0){
                        return redirect()->intended('/rkadmin');
                    }else{
                        auth()->guard('admin')->logout();
                        return redirect(route('admin.login'))->with('error', 'Invalid credentials. Please try again.')->withInput($request->only('email', 'remember'));
                    }
            }else{
                auth()->guard('admin')->logout();
                return redirect(route('admin.login'))->with('error', 'Invalid credentials. Please try again.')->withInput($request->only('email', 'remember'));
            }
        }

        return redirect(route('admin.login'))->with('error', 'Invalid credentials. Please try again.')->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        auth()->guard('admin')->logout();

        return redirect(route('admin.login'))->with('success', 'You have successfully logged out.');
    }
}
