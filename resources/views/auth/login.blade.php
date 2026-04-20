<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | INPTIC</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bg-login {
            background-image: url('/images/inptic.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="h-full overflow-hidden antialiased font-sans bg-login">
    
    <div class="fixed inset-0 bg-gradient-to-br from-blue-900/50 to-black/80 z-0"></div>

    <div class="relative z-10 h-full flex flex-col">
        
        <header class="flex justify-between items-center px-6 py-4 md:px-10">
            <div class="transform hover:scale-105 transition-all">
                <img src="/logo/logoinptic.png" alt="Logo INPTIC" class="h-12 md:h-16 w-auto drop-shadow-lg">
            </div>
            <div class="transform hover:scale-105 transition-all">
                <img src="/logo/Drapeau_du_Gabon.png" alt="Drapeau du Gabon" class="h-10 md:h-14 w-auto shadow-md rounded">
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center p-4">
            <div 
                x-data="{ show: false }" 
                x-init="setTimeout(() => show = true, 50)"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="w-full max-w-md"
            >
                <div class="glass-card rounded-3xl shadow-2xl overflow-hidden border border-white/20">
                    
                    <div class="bg-indigo-600/90 p-5 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Espace Authentification</h2>
                    </div>

                    <div class="p-6 md:p-8">
                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 ml-1">Identifiant Email</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </span>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                        class="block w-full pl-10 pr-3 py-2.5 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all border bg-white/60"
                                        placeholder="votre.nom@inptic.ga">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                            </div>

                            <div x-data="{ showPass: false }">
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 ml-1">Mot de passe</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </span>
                                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" required
                                        class="block w-full pl-10 pr-10 py-2.5 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all border bg-white/60"
                                        placeholder="••••••••">
                                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 transition-colors">
                                        <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.888 9.888L3 3m18 18l-6.888-6.888" /></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                            </div>

                            <div class="flex items-center justify-between py-1">
                                <label class="inline-flex items-center group cursor-pointer">
                                    <input type="checkbox" name="remember" class="rounded-md border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 transition-all cursor-pointer">
                                    <span class="ml-2 text-xs text-gray-600 group-hover:text-indigo-600 transition-colors">Rester connecté</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold" href="{{ route('password.request') }}">
                                        Oublié ?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95 transition-all duration-150">
                                SE CONNECTER
                            </button>
                        </form>
                    </div>
                </div>
                
                <p class="mt-4 text-center text-white/60 text-[10px] uppercase tracking-widest font-medium">
                    &copy; {{ date('Y') }} INPTIC - GI-3B System
                </p>
            </div>
        </main>
    </div>
</body>
</html>