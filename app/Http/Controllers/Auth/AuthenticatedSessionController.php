<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Tentative de connexion
        if (! Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Identifiants invalides.']);
        }

        // =========================
        // CODE DE VERIFICATION (2FA) - COMMENTÉ
        // =========================

        /*
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Génération du code à 6 chiffres
        $code = rand(100000, 999999);

        // Stocker uniquement l'ID et le code en session
        session([
            'verification_code' => $code,
            'verification_user_id' => $user->id,
        ]);

        // Envoi du code par mail
        Mail::raw("Votre code de vérification est : $code", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Code de vérification - gestion de bulletins');
        });

        // Déconnecter temporairement (2FA pas encore validé)
        Auth::logout();

        // Rediriger vers le formulaire de vérification
        return redirect()->route('verify.form');
        */

        // =========================
        // CONNEXION DIRECTE
        // =========================

        $request->session()->regenerate();

        return redirect()->intended('/dashboard'); // ou route('dashboard')
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}