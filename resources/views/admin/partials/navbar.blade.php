<header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 shrink-0 z-40 shadow-sm">
    
    {{-- Barre de recherche & Session --}}
    <div class="flex items-center gap-8">
        <div class="hidden md:flex relative group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" 
                   placeholder="Rechercher un étudiant, une UE..." 
                   class="pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-2xl w-80 text-xs font-bold focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-400">
        </div>
        
        <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-xl border border-emerald-100">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Session 2025-2026</span>
        </div>
    </div>

    {{-- Actions & Profil --}}
    <div class="flex items-center gap-4 md:gap-6">
        
        <button class="relative p-2.5 text-slate-400 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 border-2 border-white rounded-full"></span>
        </button>

        <div class="h-8 w-[1px] bg-slate-100 mx-2 hidden sm:block"></div>

        {{-- Dropdown Profil --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-slate-50 transition-all focus:outline-none border border-transparent hover:border-slate-100">
                
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-slate-900 leading-none uppercase italic">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </p>
                    {{-- CORRECTION ICI : On accède uniquement au nom du rôle --}}
                    <p class="text-[9px] font-black text-indigo-600 uppercase mt-1 tracking-tighter italic">
                        {{ is_object(Auth::user()->role) ? Auth::user()->role->nom : (Auth::user()->role ?? 'Administrateur') }}
                    </p>
                </div>

                <div class="h-11 w-11 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black shadow-lg italic border-2 border-white shrink-0 overflow-hidden hover:scale-105 transition-transform">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <span class="text-sm">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</span>
                    @endif
                </div>
            </button>

            {{-- Modal / Dropdown --}}
            <div x-show="open" 
                 @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-3 w-72 bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 p-4 z-50">
                
                {{-- Header du menu --}}
                <div class="px-5 py-5 mb-3 bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] text-white shadow-inner relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-1">Compte Authentifié</p>
                        <p class="text-[11px] font-bold truncate opacity-90">{{ Auth::user()->email }}</p>
                    </div>
                    <i class="fas fa-shield-check absolute -right-2 -bottom-2 text-white/5 text-5xl"></i>
                </div>

                {{-- Liens --}}
                <div class="space-y-1">
                    <a href="#" class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 text-slate-600 font-bold text-xs transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span>Mon Profil</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-0 group-hover:opacity-30 transition-all"></i>
                    </a>

                    <a href="{{ route('admin.parametres.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 text-slate-600 font-bold text-xs transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                <i class="fas fa-cog"></i>
                            </div>
                            <span>Paramètres</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-0 group-hover:opacity-30 transition-all"></i>
                    </a>
                </div>

                <div class="my-3 border-t border-slate-50"></div>

                {{-- Déconnexion --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 p-3 rounded-2xl hover:bg-rose-50 text-rose-600 font-black text-[10px] uppercase tracking-widest transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-rose-100/50 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all">
                            <i class="fas fa-power-off"></i>
                        </div>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>