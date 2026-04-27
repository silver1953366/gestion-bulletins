{{-- resources/views/secretariat/resultats/credits.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Crédits ECTS - Secrétariat</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.resultats.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour au suivi des résultats
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
            <div class="flex items-center gap-3">
                <i class="fas fa-coins text-teal-600 text-xl"></i>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 italic">Suivi des crédits ECTS</h2>
                    <p class="text-slate-500 text-sm mt-1">Crédits obtenus par étudiant (60 crédits requis pour valider l'année)</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left p-4 text-xs font-black text-slate-400 uppercase">Étudiant</th>
                            <th class="text-left p-4 text-xs font-black text-slate-400 uppercase">Classe</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Crédits S5</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Crédits S6</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Total crédits</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditsStats ?? [] as $stat)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center">
                                        <span class="text-teal-600 font-black text-sm">
                                            {{ substr($stat['prenom'] ?? '', 0, 1) }}{{ substr($stat['nom'] ?? '', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $stat['prenom'] ?? '' }} {{ $stat['nom'] ?? '' }}</p>
                                        <p class="text-xs text-slate-400">ID: {{ $stat['id'] ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-bold">
                                    {{ $stat['classe'] ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $creditsS5 = $stat['credits_s5'] ?? 0;
                                    $validS5 = $creditsS5 >= 30;
                                @endphp
                                <span class="font-bold {{ $validS5 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $creditsS5 }}
                                </span>
                                <span class="text-xs text-slate-400">/30</span>
                                @if($validS5)
                                    <i class="fas fa-check-circle text-emerald-500 text-xs ml-1"></i>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $creditsS6 = $stat['credits_s6'] ?? 0;
                                    $validS6 = $creditsS6 >= 30;
                                @endphp
                                <span class="font-bold {{ $validS6 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $creditsS6 }}
                                </span>
                                <span class="text-xs text-slate-400">/30</span>
                                @if($validS6)
                                    <i class="fas fa-check-circle text-emerald-500 text-xs ml-1"></i>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @php
                                    $totalCredits = $stat['total_credits'] ?? 0;
                                    $valide = $totalCredits >= 60;
                                    $pourcentage = min(($totalCredits / 60) * 100, 100);
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="text-2xl font-black {{ $valide ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $totalCredits }}
                                    </span>
                                    <span class="text-xs text-slate-400">/60</span>
                                    <div class="w-24 mt-1 bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-teal-500 h-1.5 rounded-full transition-all" style="width: {{ $pourcentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                @if($valide)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black">
                                        <i class="fas fa-check-circle"></i> Année validée
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-black">
                                        <i class="fas fa-clock"></i> En cours
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center">
                                <div class="inline-flex p-6 bg-slate-50 rounded-full mb-4">
                                    <i class="fas fa-chart-line text-slate-300 text-4xl"></i>
                                </div>
                                <p class="text-slate-400 font-black italic">Aucune donnée de crédits disponible</p>
                                <p class="text-xs text-slate-300 mt-1">Les crédits apparaîtront après la saisie des notes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(!empty($creditsStats))
            <div class="mt-6 p-4 bg-slate-50 rounded-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span>Année validée (≥ 60 crédits)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <span>En cours (&lt; 60 crédits)</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500">
                        <i class="fas fa-info-circle"></i> 60 crédits requis pour valider l'année
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.secretariat>