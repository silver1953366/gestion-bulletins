<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LP ASUR - ERP SYSTEM') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Empêche le clignotement des éléments Alpine.js au chargement */
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar pour un look ERP moderne */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animations de transition pour les modales */
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        
        @include('admin.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">
            
            @include('admin.partials.navbar')

            <div class="flex-1 overflow-y-auto bg-slate-50/50 p-6 md:p-8 custom-scrollbar">
                
                {{-- En-tête de page (Breadcrumbs, Titres) --}}
                @if (isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endif

                {{-- Notifications Flash --}}
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-circle"></i>
                                <span class="text-sm font-bold">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">✕</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show"
                             class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="text-sm font-bold">{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-rose-400 hover:text-rose-600">✕</button>
                        </div>
                    @endif

                    {{-- Contenu de la vue injecté ici --}}
                    <div class="animate-fade-in">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <footer class="h-10 bg-white border-t border-slate-100 px-8 flex items-center justify-between shrink-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    &copy; {{ date('Y') }} INPTIC - Gestion Académique
                </p>
                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest italic">
                    LP ASUR v2.0
                </p>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>