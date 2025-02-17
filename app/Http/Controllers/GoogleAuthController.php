<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
       try {

        $google_user = Socialite::driver('google')->user();

        $user = User::where('googleid', $google_user->getId())->first();

        if(!$user){
            $new_user = User::create([
                'fullname' => $google_user->getName(),
                'email' => $google_user->getEmail(),
                'googleid' => $google_user->getId(),
            ]);

            Auth::login($new_user);

            // dd($new_user);

            return redirect()->intended('/');
        } else {
            Auth::login($user);

            $usertype = Auth::user()->usertype;

            if($usertype == 'admin'){
                return redirect()->intended('/admin');
            } else if ($usertype == 'user'){
                return redirect()->intended('/');
            }
        }

       } catch (\Throwable $th) {
            dd('Something went wrong'. $th->getMessage());
       }
    }
}
