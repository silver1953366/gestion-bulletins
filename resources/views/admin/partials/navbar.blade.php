<header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 shrink-0 z-40 shadow-sm">
    
    <div class="flex items-center gap-8">
        <div class="hidden md:flex relative group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" 
                   placeholder="Rechercher un étudiant, une UE ou une note..." 
                   class="pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-2xl w-80 text-xs font-bold focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-slate-400">
        </div>
        
        <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-indigo-50 rounded-xl border border-indigo-100">
            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Session 2025-2026</span>
        </div>
    </div>

    <div class="flex items-center gap-4 md:gap-6">
        
        <button class="relative p-2.5 text-slate-400 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 border-2 border-white rounded-full"></span>
        </button>

        <div class="h-8 w-[1px] bg-slate-100 mx-2 hidden sm:block"></div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-slate-50 transition-all focus:outline-none border border-transparent hover:border-slate-100">
                
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-slate-900 leading-none uppercase italic">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </p>
                    <p class="text-[9px] font-black text-indigo-500 uppercase mt-1 tracking-tighter italic">
                        {{ Auth::user()->role ?? 'Administrateur' }}
                    </p>
                </div>

                <div class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black shadow-lg italic border-2 border-white shrink-0 overflow-hidden">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                    @endif
                </div>
            </button>

            <div x-show="open" 
                 @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-3 w-64 bg-white rounded-[2rem] shadow-2xl border border-slate-100 p-3 z-50">
                
                <div class="px-4 py-4 mb-2 bg-slate-900 rounded-[1.5rem] text-white">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">Identifiant INPTIC</p>
                    <p class="text-xs font-bold truncate opacity-90">{{ Auth::user()->email }}</p>
                </div>

                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-white transition-colors">
                            <i class="fas fa-user-circle text-slate-400 group-hover:text-indigo-600"></i>
                        </div>
                        Mon Profil
                    </a>

                    <a href="{{ route('admin.parametres.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-white transition-colors">
                            <i class="fas fa-shield-alt text-slate-400 group-hover:text-indigo-600"></i>
                        </div>
                        Sécurité & Accès
                    </a>
                </div>

                <hr class="my-2 border-slate-50">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-rose-50 text-rose-600 font-black text-xs uppercase tracking-widest transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-rose-100/50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                            <i class="fas fa-power-off"></i>
                        </div>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>