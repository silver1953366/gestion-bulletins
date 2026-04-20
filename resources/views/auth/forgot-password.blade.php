<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération | INPTIC</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bg-forgot {
            background-image: url('/images/inptic.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="h-full overflow-hidden antialiased font-sans bg-forgot">
    
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
                <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
                    
                    <div class="bg-indigo-600/90 p-5 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Mot de passe oublié</h2>
                    </div>

                    <div class="p-6 md:p-8">
                        <p class="text-xs text-gray-500 text-center mb-6 leading-relaxed">
                            Pas de panique ! Entrez votre email et nous vous enverrons un lien pour réinitialiser votre accès.
                        </p>

                        @if (session('status'))
                            <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1 ml-1">Adresse Email académique</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </span>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                        class="block w-full pl-10 pr-3 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all border bg-white/60"
                                        placeholder="votre.email@inptic.ga">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                            </div>

                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95 transition-all duration-150">
                                ENVOYER LE LIEN
                            </button>
                        </form>

                        <div class="mt-6 text-center">
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center justify-center group transition-all">
                                <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                                Retour à la connexion
                            </a>
                        </div>
                    </div>
                </div>
                
                <p class="mt-4 text-center text-white/60 text-[10px] uppercase tracking-widest font-medium">
                    &copy; {{ date('Y') }} INPTIC - Service de Récupération
                </p>
            </div>
        </main>
    </div>
</body>
</html>