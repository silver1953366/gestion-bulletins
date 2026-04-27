{{-- resources/views/secretariat/etudiants/fiche.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Fiche étudiant - {{ $etudiant->prenom }} {{ $etudiant->nom }}</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.etudiants.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-teal-600 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Fiche pédagogique</h2>
                    <p class="text-slate-500">{{ $etudiant->prenom }} {{ $etudiant->nom }} - #{{ $etudiant->id }}</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 mb-4">Informations personnelles</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Nom complet:</span>
                            <span>{{ $etudiant->prenom }} {{ $etudiant->nom }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Date naissance:</span>
                            <span>{{ $etudiant->date_naissance ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Lieu naissance:</span>
                            <span>{{ $etudiant->lieu_naissance ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Bac:</span>
                            <span>{{ $etudiant->bac ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Provenance:</span>
                            <span>{{ $etudiant->provenance ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-black text-slate-800 mb-4">Résultats académiques</h3>
                    <div class="space-y-3">
                        @php
                            // Récupérer le résultat annuel (premier de la collection ou null)
                            $resultatAnnuel = $etudiant->resultatsAnnuel->first();
                            $moyenneGenerale = $resultatAnnuel ? $resultatAnnuel->moyenne : 0;
                            $decision = $resultatAnnuel ? $resultatAnnuel->decision : 'En cours';
                            
                            // Calculer le total des crédits
                            $creditsTotal = $etudiant->resultatsSemestres->sum('credits_total');
                        @endphp
                        
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Moyenne générale:</span>
                            <span class="text-xl font-black text-teal-600">{{ number_format($moyenneGenerale, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Décision:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-black 
                                {{ $decision == 'ADMIS' ? 'bg-emerald-100 text-emerald-700' : ($decision == 'REDOUBLEMENT' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $decision }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-600">Crédits obtenus:</span>
                            <span class="font-bold text-slate-800">{{ $creditsTotal }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section des matières et notes --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <h3 class="text-lg font-black text-slate-800 mb-4">Résultats par matière</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="text-left p-3 text-xs font-black text-slate-400 uppercase">Matière</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">CC</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">Examen</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">Rattrapage</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">Moyenne</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($etudiant->resultatsMatieres as $resultat)
                            <tr class="border-b border-slate-50">
                                <td class="p-3 font-bold text-slate-700">{{ $resultat->matiere->libelle ?? 'N/A' }}</td>
                                <td class="p-3 text-center">
                                    @php
                                        $cc = $etudiant->evaluations->where('matiere_id', $resultat->matiere_id)->where('type', 'CC')->first();
                                    @endphp
                                    {{ $cc ? number_format($cc->note, 2) : '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    @php
                                        $examen = $etudiant->evaluations->where('matiere_id', $resultat->matiere_id)->where('type', 'EXAMEN')->first();
                                    @endphp
                                    {{ $examen ? number_format($examen->note, 2) : '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    @php
                                        $rattrapage = $etudiant->evaluations->where('matiere_id', $resultat->matiere_id)->where('type', 'RATTRAPAGE')->first();
                                    @endphp
                                    {{ $rattrapage ? number_format($rattrapage->note, 2) : '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="font-bold {{ ($resultat->moyenne ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($resultat->moyenne ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-black 
                                        {{ ($resultat->moyenne ?? 0) >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ ($resultat->moyenne ?? 0) >= 10 ? 'Validé' : 'Non validé' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                    Aucun résultat enregistré
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section des absences --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <h3 class="text-lg font-black text-slate-800 mb-4">Relevé des absences</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="text-left p-3 text-xs font-black text-slate-400 uppercase">Matière</th>
                                <th class="text-center p-3 text-xs font-black text-slate-400 uppercase">Heures</th>
                                <th class="text-left p-3 text-xs font-black text-slate-400 uppercase">Date</th>
                                <th class="text-left p-3 text-xs font-black text-slate-400 uppercase">Justification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($etudiant->absences as $absence)
                            <tr class="border-b border-slate-50">
                                <td class="p-3">{{ $absence->matiere->libelle ?? 'N/A' }}</td>
                                <td class="p-3 text-center">{{ $absence->heures }} h</td>
                                <td class="p-3">{{ $absence->created_at->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $absence->justification ?? 'Non justifiée' }}</td>
                             </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400 italic">
                                    Aucune absence enregistrée
                                </td>
                             </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.secretariat>