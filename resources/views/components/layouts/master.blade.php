<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'LP ASUR - Admin ERP' }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Scrollbar moderne pour la zone principale */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: true }">

    <div class="flex h-screen overflow-hidden">
        
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
            
            @include('admin.partials.navbar')

            <main class="p-6 md:p-8">
                {{-- Affichage du Header de page si défini --}}
                @if (isset($header))
                    <header class="mb-8">
                        {{ $header }}
                    </header>
                @endif

                {{-- Alertes Flash (Succès/Erreurs) --}}
                @if (session('success'))
                    <div class="mb-6 flex items-center p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm animate-fade-in">
                        <i class="fas fa-check-circle text-emerald-500 mr-3"></i>
                        <span class="text-sm font-bold text-emerald-800">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-center p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm animate-fade-in">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-sm font-bold text-red-800">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Zone d'injection du contenu spécifique --}}
                <div class="animate-fade-in">
                    {{ $slot }}
                </div>
            </main>

            <footer class="mt-auto p-6 text-center border-t border-slate-100 bg-white/50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    &copy; {{ date('Y') }} INPTIC - Plateforme de Gestion Académique (DAR)
                </p>
            </footer>
        </div>
    </div>

    {{-- Stack pour le JavaScript spécifique aux pages --}}
    @stack('scripts')
</body>
</html>