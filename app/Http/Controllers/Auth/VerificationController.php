<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerificationController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $code = session('verification_code');
        $userId = session('verification_user_id');

        if (! $code || ! $userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expirée. Veuillez vous reconnecter.']);
        }

        if ($request->code != $code) {
            return back()->withErrors(['code' => 'Code incorrect']);
        }

        // Récupérer l’utilisateur par son ID
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        // Authentifier l’utilisateur
        Auth::login($user);

        // Nettoyage de la session 2FA
        session()->forget('verification_code');
        session()->forget('verification_user_id');

       return redirect()->route('dashboard')->with('success', 'Connexion réussie 🎉');
    }
}
