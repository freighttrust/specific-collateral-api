<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function register(Request $request) {
        $this->validate($request, [
            'email' => 'required|unique:users',
            'password' => 'required|min:8'
        ]);

        $user = User::register($request);
        return $user;
    }

    public function login(Request $request) {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::authenticateUser($request);
        if (!$user) {
             abort('401','Email or password incorrect');
        }
        return $user;
    }
}