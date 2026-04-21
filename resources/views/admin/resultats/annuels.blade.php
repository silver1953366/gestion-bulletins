<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-black text-slate-900 italic uppercase">Résultats de Fin d'Année</h1>
            <div class="flex gap-4 italic text-xs font-bold text-slate-400">
                <span>INPTIC</span> • <span>LP ASUR</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-900 rounded-[3rem] p-10 mb-10 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-amber-500 mb-2">Statut Global de la Promotion</h2>
            <p class="text-3xl font-light">Délibération du Jury Final</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach($resultats as $res)
        <div class="bg-white border border-slate-100 p-6 rounded-[2rem] flex items-center justify-between hover:border-amber-200 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center font-black text-slate-400 group-hover:bg-amber-100 group-hover:text-amber-600 transition">
                    {{ substr($res->etudiant->nom, 0, 1) }}
                </div>
                <div>
                    <h4 class="font-black text-slate-900 uppercase italic">{{ $res->etudiant->nom }}</h4>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">{{ $res->mention }}</span>
                </div>
            </div>

            <div class="flex gap-16 items-center">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-300 uppercase">Moyenne</p>
                    <p class="text-xl font-black text-slate-800">{{ number_format($res->moyenne, 2) }}</p>
                </div>
                <div class="w-48 text-right">
                    <p class="text-[10px] font-black text-slate-300 uppercase mb-1">Décision</p>
                    <span class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-tighter {{ $res->decision == 'Diplômé(e)' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $res->decision }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>