{{-- resources/views/components/layouts/secretariat.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Espace Secrétariat Pédagogique - INPTIC' }}</title>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .sidebar-item.active {
            @apply bg-teal-600 text-white shadow-lg;
        }
        .sidebar-item.active i {
            @apply text-white;
        }
        .sidebar-item:hover:not(.active) {
            @apply bg-teal-50 text-teal-700;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            @apply bg-slate-100;
        }
        ::-webkit-scrollbar-thumb {
            @apply bg-slate-300 rounded-full;
        }
        ::-webkit-scrollbar-thumb:hover {
            @apply bg-slate-400;
        }
    </style>
    
    {{ $styles ?? '' }}
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        
        {{-- Sidebar Secrétariat --}}
        <aside class="w-72 bg-white shadow-xl border-r border-slate-100 flex flex-col fixed inset-y-0 left-0 z-30">
            {{-- Logo --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-gradient-to-br from-teal-600 to-teal-800 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black italic tracking-tight text-slate-800">INPTIC</h1>
                        <p class="text-[9px] font-black uppercase text-teal-600 tracking-widest">Secrétariat Pédagogique</p>
                    </div>
                </div>
            </div>
            
            {{-- Navigation --}}
            <nav class="flex-1 py-8 px-4 space-y-2">
                <a href="{{ route('secretariat.dashboard') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('secretariat.dashboard') ? 'active bg-teal-600 text-white shadow-lg' : 'text-slate-600 hover:bg-teal-50' }}">
                    <i class="fas fa-chart-line w-5 text-lg"></i>
                    <span class="font-black uppercase text-sm tracking-tight">Tableau de Bord</span>
                </a>
                
                <div class="pt-4">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest px-5 mb-3">GESTION ACADÉMIQUE</p>
                    
                    <a href="{{ route('secretariat.etudiants.index') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50">
                        <i class="fas fa-user-graduate w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Étudiants</span>
                    </a>
                    
                    <a href="{{ route('secretariat.notes.index') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50">
                        <i class="fas fa-pen-ruler w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Saisie des Notes</span>
                    </a>
                    
                    <a href="{{ route('secretariat.absences.index') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50">
                        <i class="fas fa-user-clock w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Absences</span>
                    </a>
                </div>
                
                <div class="pt-4">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest px-5 mb-3">ÉVALUATION & BULLETINS</p>
                    
                    <a href="{{ route('secretariat.bulletins.index') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50">
                        <i class="fas fa-file-pdf w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Bulletins</span>
                    </a>
                    
                    <a href="{{ route('secretariat.resultats.index') }}" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50">
                        <i class="fas fa-chart-pie w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Suivi des Résultats</span>
                    </a>
                </div>
                
                <div class="pt-6 mt-6 border-t border-slate-100">
                    <a href="#" class="sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-slate-600 hover:bg-teal-50 opacity-50 cursor-not-allowed">
                        <i class="fas fa-user-circle w-5 text-lg"></i>
                        <span class="font-black uppercase text-sm tracking-tight">Mon Profil</span>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full sidebar-item flex items-center gap-4 px-5 py-3 rounded-2xl transition-all duration-300 text-rose-600 hover:bg-rose-50">
                            <i class="fas fa-sign-out-alt w-5 text-lg"></i>
                            <span class="font-black uppercase text-sm tracking-tight">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </nav>
            
            {{-- Footer Sidebar --}}
            <div class="p-6 border-t border-slate-100">
                <div class="bg-slate-50 rounded-2xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center">
                            <i class="fas fa-user-tie text-teal-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-slate-800 truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                            <p class="text-[9px] font-bold text-teal-600 uppercase">Secrétariat Pédagogique</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        {{-- Main Content --}}
        <main class="flex-1 ml-72 overflow-y-auto">
            <div class="p-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    
    <script>
        document.querySelectorAll('.sidebar-item').forEach(item => {
            if (item.href && item.href === window.location.href) {
                item.classList.add('active', 'bg-teal-600', 'text-white', 'shadow-lg');
            }
        });
    </script>
    
    {{ $scripts ?? '' }}
</body>
</html>