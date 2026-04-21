<x-app-layout>
    <div x-data="{ 
        typeEvaluation: 'CC',
        showSuccess: true 
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-widest">
                            <li class="text-slate-400">Administration</li>
                            <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                            <li class="text-amber-600 italic">Saisie des notes</li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                        {{ $matiere->libelle }} <span class="text-slate-300">/</span> <span class="text-amber-500">{{ $classe->nom }}</span>
                    </h1>
                </div>

                <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Type de session :</label>
                    <select x-model="typeEvaluation" class="border-none bg-slate-50 rounded-xl font-bold text-xs focus:ring-0 text-slate-700">
                        <option value="CC">Contrôle Continu (40%)</option>
                        <option value="EXAMEN">Examen Final (60%)</option>
                        <option value="RATTRAPAGE">Rattrapage</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Étudiant</th>
                        <th class="px-8 py-5 text-center">Note Actuelle</th>
                        <th class="px-8 py-5 text-center w-64">Nouvelle Note / 20</th>
                        <th class="px-8 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($etudiants as $etudiant)
                        @php
                            // On cherche si une note existe déjà pour le type sélectionné
                            $noteExistante = $etudiant->evaluations->where('type', 'CC')->first(); // Par défaut CC
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition group">
                            <form action="{{ route('admin.evaluations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                <input type="hidden" name="matiere_id" value="{{ $matiere->id }}">
                                <input type="hidden" name="type" :value="typeEvaluation">

                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-black text-slate-400 text-xs uppercase">
                                            {{ substr($etudiant->nom, 0, 1) }}{{ substr($etudiant->prenom, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900 uppercase italic text-sm leading-none">{{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium mt-1 italic tracking-tight">Matricule : {{ $etudiant->matricule ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    <template x-if="typeEvaluation === 'CC'">
                                        <span class="font-bold text-slate-400">{{ $etudiant->evaluations->where('type', 'CC')->first()?->note ?? '--' }}</span>
                                    </template>
                                    <template x-if="typeEvaluation === 'EXAMEN'">
                                        <span class="font-bold text-slate-400">{{ $etudiant->evaluations->where('type', 'EXAMEN')->first()?->note ?? '--' }}</span>
                                    </template>
                                    <template x-if="typeEvaluation === 'RATTRAPAGE'">
                                        <span class="font-bold text-rose-400">{{ $etudiant->evaluations->where('type', 'RATTRAPAGE')->first()?->note ?? '--' }}</span>
                                    </template>
                                </td>

                                <td class="px-8 py-5">
                                    <div class="relative flex items-center justify-center">
                                        <input type="number" 
                                               name="note" 
                                               step="0.01" 
                                               min="0" 
                                               max="20" 
                                               required
                                               placeholder="00.00"
                                               class="w-32 text-center font-black text-xl bg-slate-50 border-2 border-transparent focus:border-amber-500 focus:bg-white focus:ring-0 rounded-2xl transition-all placeholder-slate-200">
                                    </div>
                                </td>

                                <td class="px-8 py-5 text-right">
                                    <button type="submit" class="inline-flex items-center justify-center h-12 px-6 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-slate-100 gap-2 group">
                                        <span>Valider</span>
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-slate-400 italic font-medium">
                                Aucun étudiant inscrit dans cette classe.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100">
                <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Pondération</h4>
                <p class="text-xs text-amber-900 font-medium italic leading-relaxed">
                    Le calcul final pour l'INPTIC est : <br>
                    <strong>(CC × 0.4) + (Examen × 0.6)</strong>.
                </p>
            </div>
            <div class="p-6 bg-slate-900 rounded-[2rem] text-white shadow-xl shadow-slate-200">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Rattrapage</h4>
                <p class="text-xs text-slate-200 font-medium italic leading-relaxed">
                    La note de rattrapage remplace automatiquement la note d'examen si elle est supérieure.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>