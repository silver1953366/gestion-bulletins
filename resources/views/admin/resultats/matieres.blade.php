<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Moyennes Matières</h1>
                <p class="text-slate-500 text-sm font-medium italic">Application des règles de pondération et pénalités d'absences [cite: 51, 86]</p>
            </div>
            
            <div x-data="{ open: false }" class="relative">
                <button @click="open = true" class="w-full lg:w-auto px-8 py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-600 transition shadow-xl shadow-amber-100 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Lancer Calcul Batch 
                </button>

                <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-md" x-cloak>
                    <div @click.away="open = false" class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg p-12 border border-slate-100">
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" /></svg>
                            </div>
                            <h2 class="text-2xl font-black text-slate-900 italic">Traitement de masse</h2>
                            <p class="text-slate-400 text-sm mt-2">Sélectionnez la cible pour le recalcul automatique</p>
                        </div>

                        <form action="{{ route('admin.resultats.matieres.calculer') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Classe</label>
                                <select name="classe_id" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-amber-500 rounded-2xl font-bold text-sm text-slate-700">
                                    @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->nom }}</option> @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Matière</label>
                                <select name="matiere_id" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-amber-500 rounded-2xl font-bold text-sm text-slate-700">
                                    @foreach($matieres as $m) <option value="{{ $m->id }}">{{ $m->nom }}</option> @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg mt-6">
                                Démarrer le recalcul
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-6">Étudiant</th>
                        <th class="px-8 py-6">Matière</th>
                        <th class="px-8 py-6 text-center">Note CC (40%)</th>
                        <th class="px-8 py-6 text-center">Note Exam (60%)</th>
                        <th class="px-8 py-6 text-center">Moyenne Finale</th>
                        <th class="px-8 py-6 text-right">Rattrapage </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($resultats as $res)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-8 py-5">
                            <div class="font-black text-slate-900 uppercase italic">{{ $res->etudiant->nom }}</div>
                            <div class="text-[10px] text-slate-400 font-bold tracking-tighter">{{ $res->etudiant->matricule }}</div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-slate-500">{{ $res->matiere->nom }}</span>
                        </td>
                        <td class="px-8 py-5 text-center font-bold text-slate-400 italic">
                            {{ number_format($res->etudiant->notes->where('matiere_id', $res->matiere_id)->where('type','CC')->avg('valeur'), 2) }}
                        </td>
                        <td class="px-8 py-5 text-center font-bold text-slate-400 italic">
                            {{ number_format($res->etudiant->notes->where('matiere_id', $res->matiere_id)->where('type','EXAMEN')->first()?->valeur, 2) }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="inline-block px-4 py-2 rounded-xl font-black text-lg {{ $res->moyenne >= 10 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                {{ number_format($res->moyenne, 2) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($res->utilise_rattrapage)
                                <span class="text-[10px] font-black bg-slate-900 text-white px-2 py-1 rounded">OUI</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 italic uppercase">Initial</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-medium italic">
                            Aucune moyenne calculée pour le moment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>