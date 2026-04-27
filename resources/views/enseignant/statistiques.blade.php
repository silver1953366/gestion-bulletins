{{-- resources/views/enseignant/statistiques.blade.php --}}
<x-layouts.enseignant>
    <x-slot name="title">Statistiques détaillées - Enseignant</x-slot>

    <div>
        {{-- En-tête --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div class="space-y-1">
                <h2 class="font-black text-4xl text-slate-900 tracking-tight flex items-center gap-3 italic">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-indigo-600">Statistiques détaillées</span>
                </h2>
                <div class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse"></span>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">ANALYSE DES PERFORMANCES</p>
                </div>
            </div>
            
            <a href="{{ route('enseignant.dashboard') }}" 
               class="group flex items-center gap-2 px-5 py-3 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                <i class="fas fa-arrow-left text-indigo-600 text-sm"></i>
                <span class="text-xs font-black uppercase tracking-tight">Retour au tableau</span>
            </a>
        </div>

        {{-- Sélecteur de matière --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight italic uppercase">Sélectionnez une matière</h3>
                    <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Analyse détaillée des notes</p>
                </div>
                
                <div class="relative">
                    <select id="matiere_select" class="appearance-none bg-slate-50 border-0 rounded-2xl px-6 py-3 pr-12 text-sm font-black text-slate-700 uppercase italic tracking-tight focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">Choisir une matière</option>
                        @foreach($matieres ?? [] as $matiere)
                            <option value="{{ $matiere['id'] }}" {{ ($selectedMatiereId ?? '') == $matiere['id'] ? 'selected' : '' }}>
                                {{ $matiere['libelle'] }} (Coeff {{ $matiere['coefficient'] }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(($selectedMatiereId ?? false) && isset($statistiques) && $statistiques)
        <div class="space-y-8">
            {{-- Cartes KPI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 text-white transform hover:scale-105 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-chart-line text-3xl opacity-60"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">Moyenne</span>
                    </div>
                    <p class="text-4xl font-black">{{ number_format($statistiques['moyenne_classe'] ?? 0, 2) }}</p>
                    <p class="text-xs font-bold opacity-80 mt-2">Moyenne de la classe</p>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white transform hover:scale-105 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-trophy text-3xl opacity-60"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">Meilleure</span>
                    </div>
                    <p class="text-4xl font-black">{{ number_format($statistiques['meilleure_note'] ?? 0, 2) }}</p>
                    <p class="text-xs font-bold opacity-80 mt-2">Meilleure note</p>
                </div>

                <div class="bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-6 text-white transform hover:scale-105 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-chart-simple text-3xl opacity-60"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">Minimale</span>
                    </div>
                    <p class="text-4xl font-black">{{ number_format($statistiques['moins_bonne_note'] ?? 0, 2) }}</p>
                    <p class="text-xs font-bold opacity-80 mt-2">Note la plus basse</p>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 text-white transform hover:scale-105 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-chart-pie text-3xl opacity-60"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">Réussite</span>
                    </div>
                    <p class="text-4xl font-black">{{ number_format($statistiques['taux_reussite'] ?? 0, 1) }}%</p>
                    <p class="text-xs font-bold opacity-80 mt-2">Taux de réussite</p>
                </div>
            </div>

            {{-- Graphique de distribution --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-3 bg-indigo-50 rounded-2xl">
                        <i class="fas fa-chart-bar text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Distribution des notes</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1">Répartition des étudiants par tranche de notes</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @php
                        $tranches = [
                            '18-20' => ['min' => 18, 'max' => 20, 'couleur' => 'bg-emerald-500', 'label' => 'Excellent (18-20)'],
                            '16-17.99' => ['min' => 16, 'max' => 18, 'couleur' => 'bg-emerald-400', 'label' => 'Très bien (16-18)'],
                            '14-15.99' => ['min' => 14, 'max' => 16, 'couleur' => 'bg-blue-500', 'label' => 'Bien (14-16)'],
                            '12-13.99' => ['min' => 12, 'max' => 14, 'couleur' => 'bg-indigo-500', 'label' => 'Assez bien (12-14)'],
                            '10-11.99' => ['min' => 10, 'max' => 12, 'couleur' => 'bg-amber-500', 'label' => 'Passable (10-12)'],
                            '0-9.99' => ['min' => 0, 'max' => 10, 'couleur' => 'bg-rose-500', 'label' => 'Insuffisant (0-10)'],
                        ];
                        $totalEtudiants = isset($statistiques['distribution']) ? array_sum($statistiques['distribution']) : 0;
                    @endphp

                    @foreach($tranches as $tranche => $data)
                        @php
                            $count = $statistiques['distribution'][$tranche] ?? 0;
                            $pourcentage = $totalEtudiants > 0 ? ($count / $totalEtudiants) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-black mb-2">
                                <span class="text-slate-600">{{ $data['label'] }}</span>
                                <span class="text-slate-400">{{ $count }} étudiant(s) ({{ number_format($pourcentage, 1) }}%)</span>
                            </div>
                            <div class="h-8 bg-slate-100 rounded-xl overflow-hidden">
                                <div class="h-full {{ $data['couleur'] }} rounded-xl flex items-center justify-end px-4 transition-all duration-500"
                                     style="width: {{ $pourcentage }}%">
                                    @if($pourcentage > 15)
                                        <span class="text-white text-xs font-black">{{ number_format($pourcentage, 0) }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Statistiques CC vs Examen --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-emerald-50 rounded-2xl">
                            <i class="fas fa-pen-ruler text-emerald-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Contrôle Continu (CC)</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Statistiques des évaluations CC</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Moyenne CC</span>
                            <span class="text-2xl font-black text-emerald-600">{{ number_format($statistiques['stats_cc']['moyenne'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Écart-type</span>
                            <span class="text-2xl font-black text-slate-800">{{ number_format($statistiques['stats_cc']['ecart_type'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Taux de saisie</span>
                            <span class="text-2xl font-black text-emerald-600">{{ number_format($statistiques['stats_cc']['taux_saisie'] ?? 0, 0) }}%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-indigo-50 rounded-2xl">
                            <i class="fas fa-clipboard-list text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Examen final</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Statistiques des examens</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Moyenne Examen</span>
                            <span class="text-2xl font-black text-indigo-600">{{ number_format($statistiques['stats_examen']['moyenne'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Écart-type</span>
                            <span class="text-2xl font-black text-slate-800">{{ number_format($statistiques['stats_examen']['ecart_type'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                            <span class="text-sm font-black text-slate-600">Taux de saisie</span>
                            <span class="text-2xl font-black text-indigo-600">{{ number_format($statistiques['stats_examen']['taux_saisie'] ?? 0, 0) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Liste des étudiants avec notes --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-indigo-100 rounded-2xl">
                            <i class="fas fa-users text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Détail des étudiants</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">Classement par moyenne générale</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="text-left p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rang</th>
                                <th class="text-left p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Étudiant</th>
                                <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">CC /20</th>
                                <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Examen /20</th>
                                <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Moyenne</th>
                                <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Appréciation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $etudiantsTries = collect($statistiques['details_etudiants'] ?? [])->sortByDesc('moyenne')->values();
                            @endphp
                            @forelse($etudiantsTries as $index => $etudiant)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition">
                                <td class="p-6">
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-black text-sm
                                        {{ $index == 0 ? 'bg-amber-100 text-amber-700' : ($index == 1 ? 'bg-slate-100 text-slate-600' : ($index == 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-50 text-slate-400')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                            <span class="text-indigo-600 font-black text-sm">{{ substr($etudiant['prenom'] ?? '', 0, 1) }}{{ substr($etudiant['nom'] ?? '', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-800">{{ $etudiant['prenom'] ?? '' }} {{ $etudiant['nom'] ?? '' }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $etudiant['matricule'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                  </td>
                                <td class="p-6 text-center">
                                    <span class="font-mono font-bold text-slate-700">{{ number_format($etudiant['cc'] ?? 0, 2) }}</span>
                                  </td>
                                <td class="p-6 text-center">
                                    <span class="font-mono font-bold text-slate-700">{{ number_format($etudiant['examen'] ?? 0, 2) }}</span>
                                  </td>
                                <td class="p-6 text-center">
                                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black 
                                        {{ ($etudiant['moyenne'] ?? 0) >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ number_format($etudiant['moyenne'] ?? 0, 2) }}
                                    </div>
                                  </td>
                                <td class="p-6 text-center">
                                    <span class="text-xs font-black {{ ($etudiant['moyenne'] ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ ($etudiant['moyenne'] ?? 0) >= 10 ? 'ADMIS' : 'NON ADMIS' }}
                                    </span>
                                  </td>
                             </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-400 italic">
                                    Aucun étudiant trouvé pour cette matière
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-16 text-center">
                <div class="inline-flex p-6 bg-indigo-50 rounded-full mb-6">
                    <i class="fas fa-chart-pie text-indigo-400 text-4xl"></i>
                </div>
                <p class="text-slate-400 font-black italic">Sélectionnez une matière pour afficher les statistiques détaillées.</p>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const matiereSelect = document.getElementById('matiere_select');
        if (matiereSelect) {
            matiereSelect.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = '{{ route("enseignant.statistiques") }}?matiere=' + this.value;
                }
            });
        }
    });
    </script>
    @endpush
</x-layouts.enseignant>