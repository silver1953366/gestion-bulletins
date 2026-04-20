<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue | Gestion des Bulletins INPTIC</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-welcome {
            background-image: url('/images/INPTIC2.jpeg');
            background-size: cover;
            background-position: center;
            filter: blur(4px) brightness(0.6); /* Arrière-plan flouté et assombri */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            transform: scale(1.1); /* Évite les bords blancs dû au flou */
        }
    </style>
</head>
<body class="h-full antialiased font-sans text-white">

    <div class="bg-welcome"></div>

    <div class="relative z-10 min-h-screen flex flex-col">
        
        <nav class="flex justify-between items-center px-8 py-6">
            <img src="/logo/logoinptic.png" alt="INPTIC" class="h-16 drop-shadow-md">
            <div class="hidden md:block">
                <span class="text-sm font-medium tracking-widest uppercase opacity-75">République Gabonaise</span>
            </div>
        </nav>

        <main class="flex-grow flex items-center px-8">
            <div class="max-w-3xl" 
                 x-data="{ show: false }" 
                 x-init="setTimeout(() => show = true, 200)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 -translate-x-12"
                 x-transition:enter-end="opacity-100 translate-x-0">
                
                <h2 class="text-indigo-400 font-bold tracking-widest uppercase mb-2">Plateforme Académique</h2>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                    Gestion Intégrée des <span class="text-indigo-500">Bulletins</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-300 mb-10 leading-relaxed max-w-2xl">
                    Accédez en temps réel à vos résultats académiques, relevés de notes et suivis pédagogiques. 
                    Un espace dédié aux étudiants et parents pour l'excellence de l'INPTIC.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all transform hover:scale-105 active:scale-95 shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        ACCÉDER AU PORTAIL
                    </a>

                    <button class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold rounded-xl transition-all border border-white/30">
                        GUIDE D'UTILISATION
                    </button>
                </div>
            </div>
        </main>

        <footer class="p-8 flex flex-col md:flex-row justify-between items-center text-sm opacity-60">
            <p>&copy; {{ date('Y') }} INPTIC - Génie Informatique (GI-3B)</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <span>Libreville, Gabon</span>
                <a href="#" class="hover:text-indigo-400">Support Technique</a>
            </div>
        </footer>
    </div>
</body>
</html>