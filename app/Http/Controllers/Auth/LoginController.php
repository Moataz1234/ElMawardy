<?php

namespace App\Http\Controllers\Auth;


use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use App\Providers\AsgardeoProvider;

class LoginController extends Controller
{
    // Redirect to Choreo for authentication
    public function redirectToProvider()
    {
        return Socialite::driver('asgardeo')
        // ->scopes(['openid', 'profile', 'email'])  // Use setScopes instead of scopes
        ->redirect();
    }

    // Handle the callback from Choreo after login
    public function handleProviderCallback()
    {
        $user = Socialite::driver('asgardeo')->user();

        // Check if user exists in the database
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            // Log in the existing user
            Auth::login($existingUser);
        } else {
            // Register the user if not already in the database
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => bcrypt(Str::random(16)),
            ]);

            Auth::login($newUser);
        }

        // Redirect to the intended route or home page
        return redirect()->intended('/home');
    }
}