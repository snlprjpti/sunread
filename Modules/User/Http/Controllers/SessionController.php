<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

/**
 * Admin user session controller
 */
class SessionController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['login']);

    }

    /**
     * Show the form for creating a new resource
     *
     */

    public function login()
    {
        request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $jwtToken = null;


        if (! $jwtToken = Auth::guard('admin')->attempt(request()->only('email', 'password'))) {
            return response()->json([
                'error' => 'Invalid Email or Password',
            ], 401);
        }

        $admin = auth('admin')->user();
        return response()->json([
            'token' => $jwtToken,
            'message' => 'Logged in successfully.',
            'data' => $admin
        ]);
    }


}