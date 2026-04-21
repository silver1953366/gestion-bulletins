<div class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <div class="mx-auto w-24 h-24 bg-rose-500/10 rounded-3xl flex items-center justify-center mb-8 border border-rose-500/20">
            <svg class="w-12 h-12 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-8V5m0 0a2 2 0 100 4 2 2 0 000-4zm-3.342 11.013a11.035 11.035 0 0110.511-1.123m-2.433-1.637l1.32 1.32m-5.414-1.32l-1.32 1.32M6.342 7.987a11.035 11.035 0 00-10.511 1.123m2.433 1.637l-1.32-1.32m5.414 1.32l1.32-1.32" />
            </svg>
        </div>

        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Accès Refusé</h1>
        <div class="mt-4 p-4 bg-slate-800/50 rounded-2xl border border-slate-700">
            <p class="text-slate-400 text-sm leading-relaxed">
                Désolé, <span class="text-indigo-400 font-bold">{{ Auth::user()->first_name }}</span>. 
                Vos privilèges actuels (<span class="text-rose-400 font-mono">{{ Auth::user()->role_name }}</span>) 
                ne vous permettent pas d'accéder à cette ressource.
            </p>
        </div>

        <div class="mt-8 flex flex-col gap-3">
            <a href="{{ route('dashboard') }}" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl transition shadow-lg shadow-indigo-500/20">
                Retour au Tableau de Bord
            </a>
            <button onclick="window.history.back()" class="w-full py-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-2xl transition">
                Page précédente
            </button>
        </div>

        <p class="mt-8 text-[10px] text-slate-600 uppercase tracking-[0.2em] font-bold">
            ID de tentative : {{ now()->timestamp }} • IP: {{ request()->ip() }}
        </p>
    </div>
</div>