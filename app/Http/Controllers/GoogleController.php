<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {

            $user = Socialite::driver('google')->user();
            $findUser = User::query()->where('google_id', $user->id)->first();

            if ($findUser) {
                Auth::login($findUser);
            } else {
                $newUser = User::query()->updateOrCreate(['email' => $user->email], [
                    'name'      => $user->name,
                    'google_id' => $user->id,
                    'password'  => Hash::make('test28')
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('home');

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
