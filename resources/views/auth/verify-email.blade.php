<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification | INPTIC</title>
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
            <img src="/logo/logoinptic.png" alt="Logo INPTIC" class="h-12 w-auto">
            <img src="/logo/Drapeau_du_Gabon.png" alt="Gabon" class="h-10 w-auto rounded shadow">
        </header>

        <main class="flex-grow flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
                    <div class="bg-indigo-600/90 p-5 text-center text-white">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.66l7-4.67a2 2 0 012.22 0l7 4.66a2 2 0 01.89 1.67V19a2 2 0 01-2 2H5a2 2 0 01-2-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l9 6 9-6" /></svg>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight">Vérifiez votre Email</h2>
                    </div>

                    <div class="p-8">
                        <p class="text-sm text-gray-600 text-center mb-6">
                            Merci de votre inscription ! Un lien de vérification a été envoyé sur votre boîte mail. Cliquez dessus pour activer votre compte.
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-medium text-center">
                                Un nouveau lien a été envoyé à l'adresse fournie lors de l'inscription.
                            </div>
                        @endif

                        <div class="flex flex-col gap-4">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition-all active:scale-95">
                                    RENVOYER L'EMAIL
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="text-center">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-indigo-600 underline font-medium">
                                    Se déconnecter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>