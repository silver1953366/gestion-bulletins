{{-- resources/views/secretariat/resultats/moyennes.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Moyennes par matière - Secrétariat</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.resultats.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour au suivi des résultats
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
            <div class="flex items-center gap-3">
                <i class="fas fa-chart-line text-teal-600 text-xl"></i>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 italic">Moyennes par matière</h2>
                    <p class="text-slate-500 text-sm mt-1">Consultation des moyennes par matière et par classe</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Filtrer par classe</label>
                    <select name="classe_id" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                        <option value="">Toutes les classes</option>
                        @php
                            $classes = \App\Models\Classe::all();
                        @endphp
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Filtrer par matière</label>
                    <select name="matiere_id" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                        <option value="">Toutes les matières</option>
                        @php
                            $matieres = \App\Models\Matiere::all();
                        @endphp
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-teal-600 text-white px-4 py-3 rounded-2xl font-black uppercase text-sm hover:bg-teal-700">
                        <i class="fas fa-search mr-2"></i> Filtrer
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left p-4 text-xs font-black text-slate-400 uppercase">Matière</th>
                            <th class="text-left p-4 text-xs font-black text-slate-400 uppercase">Classe</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Nb étudiants</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne min</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne max</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne classe</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Taux réussite</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistiques ?? [] as $stat)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30">
                            <td class="p-4 font-bold text-slate-700">{{ $stat['matiere'] }}</td>
                            <td class="p-4 text-slate-600">{{ $stat['classe'] }}</td>
                            <td class="p-4 text-center">{{ $stat['nb_etudiants'] }}</td>
                            <td class="p-4 text-center text-rose-600 font-bold">{{ number_format($stat['min'], 2) }}</td>
                            <td class="p-4 text-center text-emerald-600 font-bold">{{ number_format($stat['max'], 2) }}</td>
                            <td class="p-4 text-center font-bold text-indigo-600">{{ number_format($stat['moyenne'], 2) }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-black 
                                    {{ $stat['taux_reussite'] >= 70 ? 'bg-emerald-100 text-emerald-700' : ($stat['taux_reussite'] >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ number_format($stat['taux_reussite'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400 italic">
                                Aucune donnée disponible
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.secretariat>