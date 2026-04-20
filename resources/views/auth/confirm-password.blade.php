<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité | INPTIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .bg-auth { background-image: url('/images/inptic.jpg'); background-size: cover; background-position: center; }
    </style>
</head>
<body class="h-full overflow-hidden antialiased font-sans bg-auth">
    <div class="fixed inset-0 bg-gradient-to-br from-blue-900/50 to-black/80 z-0"></div>

    <div class="relative z-10 h-full flex flex-col">
        <header class="flex justify-between items-center px-6 py-4">
            <img src="/logo/logoinptic.png" alt="Logo INPTIC" class="h-12 w-auto drop-shadow-lg">
            <img src="/logo/Drapeau_du_Gabon.png" alt="Gabon" class="h-10 w-auto shadow-md rounded">
        </header>

        <main class="flex-grow flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
                    <div class="bg-amber-500/90 p-5 text-center text-white">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight">Zone Sécurisée</h2>
                    </div>

                    <div class="p-8 text-center">
                        <p class="text-sm text-gray-600 mb-6">
                            Cette action est sensible. Veuillez confirmer votre mot de passe avant de continuer.
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                            @csrf
                            <div class="text-left">
                                <label for="password" class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Mot de passe</label>
                                <input id="password" type="password" name="password" required class="block w-full px-4 py-3 border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all border bg-white/60">
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>

                            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg transform active:scale-95 transition-all">
                                CONFIRMER
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>