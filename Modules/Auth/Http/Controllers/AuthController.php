<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\User;
use Modules\Loan\Traits\Reuse;

class AuthController extends Controller
{
    use Reuse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('auth::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        // validate user registration
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);
        try {
            // create new user with role
            $createdUser = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
                'role' => $fields['role'],
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
