<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Home extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */

    public function index(Request $request)
    {
        return view('admin.home')->with([
            'title' => 'Dashboard',
        ]);
    }
}
