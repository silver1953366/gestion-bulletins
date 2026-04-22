<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter italic uppercase">
                    Grand <span class="text-amber-500">Jury</span> Annuel
                </h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-1">
                    Validation des Crédits (60 ECTS) • Session 2025-2026
                </p>
            </div>
            <div class="flex gap-4 italic text-xs font-bold text-slate-400 uppercase">
                <span class="px-3 py-1 bg-slate-100 rounded-full">INPTIC</span>
                <span class="px-3 py-1 bg-slate-100 rounded-full text-amber-600">LP ASUR / DAR</span>
            </div>
        </div>
    </x-slot>

    {{-- Section Header & Moteur de Calcul --}}
    <div class="bg-slate-900 rounded-[3rem] p-8 mb-10 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-amber-500 mb-2">Centre de Commande</h2>
                <p class="text-3xl font-light leading-tight">Générer les délibérations finales pour une promotion complète.</p>
            </div>

            {{-- Formulaire de Calcul Batch --}}
            <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 backdrop-blur-sm">
                <form action="{{ route('admin.jury.calculer-promo') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                    @csrf
                    <div class="flex-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-1 block">Année Académique</label>
                        <select name="annee_id" required class="w-full bg-slate-800 border-none rounded-xl text-xs font-bold text-white focus:ring-amber-500">
                            @foreach($annees as $annee)
                                <option value="{{ $annee->id }}">{{ $annee->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2 mb-1 block">Promotion</label>
                        <select name="classe_id" required class="w-full bg-slate-800 border-none rounded-xl text-xs font-bold text-white focus:ring-amber-500">
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="sm:mt-5 px-8 py-3 bg-amber-500 hover:bg-white text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all duration-300 shadow-lg shadow-amber-500/20">
                        Lancer le Jury
                    </button>
                </form>
            </div>
        </div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 rounded-full -mr-40 -mt-40 blur-3xl text-right"></div>
    </div>

    {{-- Alertes de Succès/Erreur --}}
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl animate-fade-in">
            <p class="text-emerald-700 text-[10px] font-black uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Liste des Résultats --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($resultats as $res)
            <div class="bg-white border border-slate-100 p-6 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between hover:border-amber-200 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-500 group">
                
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center font-black text-slate-400 group-hover:bg-amber-100 group-hover:text-amber-600 transition-colors duration-500">
                        {{ substr($res->etudiant->user->name ?? $res->etudiant->nom, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-black text-slate-900 uppercase italic text-lg tracking-tighter">
                            {{ $res->etudiant->user->full_name ?? $res->etudiant->nom }}
                        </h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-black uppercase">Mention :</span>
                            <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest">{{ $res->mention }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-12 items-center mt-6 md:mt-0">
                    {{-- Moyenne Annuelle --}}
                    <div class="text-center md:text-right">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Moyenne Annuelle</p>
                        <p class="text-2xl font-black text-slate-800 tracking-tighter">
                            {{ number_format($res->moyenne, 2) }}<span class="text-slate-300 text-sm ml-1">/20</span>
                        </p>
                    </div>

                    {{-- Décision --}}
                    <div class="w-48 text-center md:text-right">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-2">Décision du Jury</p>
                        <span class="inline-block px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter border
                            {{ $res->decision == 'Diplômé(e)' 
                                ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                                : ($res->decision == 'Reprise de soutenance' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-rose-50 text-rose-600 border-rose-100') 
                            }}">
                            {{ $res->decision }}
                        </span>
                    </div>

                    {{-- Action --}}
                    <div class="md:ml-4">
                        <a href="#" class="p-3 bg-slate-50 rounded-xl text-slate-300 hover:bg-slate-900 hover:text-white transition-all duration-300 block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-slate-200 rounded-[3rem] py-32 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-slate-400 font-black uppercase text-xs tracking-widest">Aucun procès-verbal généré</h3>
                <p class="text-slate-300 text-xs mt-2 italic font-medium italic">Sélectionnez une promotion et lancez le jury pour commencer.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($resultats, 'links'))
        <div class="mt-8 px-6">
            {{ $resultats->links() }}
        </div>
    @endif
</x-app-layout>