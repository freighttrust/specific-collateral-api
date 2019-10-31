<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Models\Root;

class User extends Root implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function register ($request) {
        $user = User::updateorCreate([ 'email' => $request['email'] ], $request->toArray());
        return $user;
    }

    public static function authenticateUser($request) {
        $user = User::where('email', $request->get('email'))->first();
        if ($user && Hash::check($request->get('password'), $user->password)) {
            $user->refreshAccessToken();
            return $user;
        }
        return false;
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public function refreshAccessToken() {
        $token = Hash::make( $this->email . $this->password . date('H:i:s') . mt_rand(10000, 99999) );
        $this->token = $token;
        $this->token_expiry = date('Y-m-d H:i:s', strtotime('+3 months'));
        $this->save();
    }
}

User::creating(function($user){
    $user->email = strtolower($user->email);
    if ($user->whereEmail($user->email)->first()){
        abort(403,'Email address is already in use.');
    }
});

User::created(function($user){
    $user->refreshAccessToken();
});
