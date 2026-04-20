<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Traitement de la connexion
     */
    public function login(Request $request)
    {
        // Validation des données
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Générer un code 2FA
            $code = rand(100000, 999999);

            session([
                '2fa_user_id' => $user->id,
                '2fa_code'    => $code,
                '2fa_expire'  => now()->addMinutes(5),
            ]);

            // Envoi par email
            Mail::raw("Votre code de vérification est : $code", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject("Code de vérification - Plateforme Hospitalière");
            });

            // ✅ Si AJAX → réponse JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => url('/'),
                    'message'  => 'Code envoyé à votre email.'
                ]);
            }

            return redirect('/')->with([
                'info' => 'Un code de vérification a été envoyé à votre email.',
                'show_verify_modal' => true
            ]);
        }

        // ❌ Mauvais identifiants - CORRECTION ICI
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects'
            ], ); // Ajout du code HTTP 401
        }

        return back()
            ->withErrors(['email' => 'Email ou mot de passe incorrect'])
            ->withInput($request->only('email'))
            ->with('keep_login_modal_open', true);
    }

    /**
     * Vérification du code 2FA
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        if (
            session('2fa_code') == $request->code &&
            now()->lessThan(session('2fa_expire'))
        ) {
            $user = User::find(session('2fa_user_id'));

            // Stocker infos utilisateur en session
            session([
                'utilisateur_id'   => $user->id,
                'utilisateur_nom'  => $user->first_name . ' ' . $user->last_name,
                'role'             => $user->role->name,
                'hospital_id'      => $user->role->name === 'superadmin' ? null : $user->hospital_id,
            ]);

            // Nettoyer la session temporaire
            session()->forget(['2fa_code', '2fa_user_id', '2fa_expire']);

            // Déterminer la redirection selon le rôle
            $redirectUrl = match($user->role->name) {
                'superadmin' => route('superadmin.dashboard'),
                'admin' => route('admin.dashboard'),
                'chef_service' => route('chef.dashboard'),
                'infirmier' => route('infirmier.dashboard'),
                'patient' => route('patient.dashboard'),
                default => '/'
            };

            // ✅ Réponse JSON pour AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $redirectUrl,
                    'message' => 'Connexion réussie ✅'
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Connexion réussie ✅');
        }

        // ❌ Code expiré
        if (now()->greaterThanOrEqualTo(session('2fa_expire'))) {
            session()->forget(['2fa_code', '2fa_user_id', '2fa_expire']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré, reconnectez-vous.'
                ]);
            }

            return redirect('/')->withErrors(['code' => 'Code expiré, reconnectez-vous.']);
        }

        // ❌ Code invalide
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Code invalide'
            ]);
        }

        return redirect('/')->withErrors(['code' => 'Code invalide'])->with('show_verify_modal', true);
    }

    /**
     * Mot de passe oublié
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Renvoyer un code 2FA
     */
    public function resendCode()
{
    if (session('2fa_user_id')) {
        $user = User::find(session('2fa_user_id'));

        if ($user) {
            $code = rand(100000, 999999);

            session([
                '2fa_code'   => $code,
                '2fa_expire' => now()->addMinutes(5),
            ]);

            Mail::raw("Votre nouveau code de vérification est : $code", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject("Nouveau code de vérification - Plateforme Hospitalière");
            });

            return response()->json(['success' => true]);
        }
    }

    return response()->json([
        'success' => false,
        'message' => 'Identifiant invalide'
    ]);
}
}
