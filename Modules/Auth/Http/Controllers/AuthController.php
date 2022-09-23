<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\User;
use Modules\Loan\Traits\Reuse;
use Modules\Auth\Http\Requests\UserRegister;

class AuthController extends Controller
{
    use Reuse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(UserRegister $request) // created custom form request
    {
        try {
            // create new user with role
            $createdUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            return response($createdUser, 200);
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        try {
            $checkLogin = $this->doLogin($fields);
            return $checkLogin;
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
}
