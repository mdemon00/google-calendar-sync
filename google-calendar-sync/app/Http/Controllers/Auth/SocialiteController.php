<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
    
            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
    
            if (!$user) {
                // If the user doesn't exist, create a new user record
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Optionally, you can generate a random password for the user
                    'password' => Hash::make(Str::random(16)), // Example: Generate a random 16-character password
                ]);
            }
    
            // Log in the user
            Auth::login($user, true);
    
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login with Google');
        }
    }
}
