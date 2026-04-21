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
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: true }">
    
    <div class="flex h-screen overflow-hidden">
        
        @include('admin.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">
            
            @include('admin.partials.navbar')

            <div class="flex-1 overflow-y-auto bg-slate-50/50 p-6 md:p-8 custom-scrollbar">
                
                {{-- En-tête de page optionnel (Breadcrumbs, Titres, Actions) --}}
                @if (isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endif

                {{-- Alertes de session (Succès / Erreur) --}}
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-xl shadow-sm flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Le contenu des vues --}}
                <div class="animate-fade-in">
                    {{ $slot }}
                </div>
            </div>

            <footer class="h-10 bg-white border-t border-slate-100 px-8 flex items-center justify-between shrink-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    &copy; {{ date('Y') }} INPTIC - Plateforme de Gestion Académique
                </p>
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic">
                    LP ASUR v2.0
                </p>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>