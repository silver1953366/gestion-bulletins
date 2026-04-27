{{-- resources/views/secretariat/resultats/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Suivi des résultats - Secrétariat</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Suivi des résultats</h2>
            <p class="text-slate-500 text-sm mt-1">Moyennes, crédits et décisions du jury</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('secretariat.resultats.moyennes') }}" class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 text-white hover:scale-105 transition">
            <i class="fas fa-chart-line text-3xl mb-3"></i>
            <h3 class="text-lg font-black">Moyennes</h3>
            <p class="text-sm opacity-80">Consulter les moyennes par matière</p>
        </a>
        
        <a href="{{ route('secretariat.resultats.credits') }}" class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white hover:scale-105 transition">
            <i class="fas fa-coins text-3xl mb-3"></i>
            <h3 class="text-lg font-black">Crédits ECTS</h3>
            <p class="text-sm opacity-80">Suivi des crédits par étudiant</p>
        </a>
        
        <a href="{{ route('secretariat.resultats.jury') }}" class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 text-white hover:scale-105 transition">
            <i class="fas fa-gavel text-3xl mb-3"></i>
            <h3 class="text-lg font-black">Décisions Jury</h3>
            <p class="text-sm opacity-80">Validation des délibérations</p>
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h3 class="text-lg font-black text-slate-800 mb-6">Résultats par étudiant</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left p-3 text-xs font-black text-slate-400">Étudiant</th>
                        <th class="text-center p-3 text-xs font-black text-slate-400">Moyenne S5</th>
                        <th class="text-center p-3 text-xs font-black text-slate-400">Moyenne S6</th>
                        <th class="text-center p-3 text-xs font-black text-slate-400">Crédits</th>
                        <th class="text-center p-3 text-xs font-black text-slate-400">Décision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resultats as $resultat)
                    <tr class="border-b border-slate-50">
                        <td class="p-3 font-bold">{{ $resultat->etudiant->prenom }} {{ $resultat->etudiant->nom }}</td>
                        <td class="p-3 text-center">{{ number_format($resultat->moyenne_s5 ?? 0, 2) }}</td>
                        <td class="p-3 text-center">{{ number_format($resultat->moyenne_s6 ?? 0, 2) }}</td>
                        <td class="p-3 text-center">{{ $resultat->credits_total ?? 0 }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-black 
                                {{ $resultat->decision == 'ADMIS' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $resultat->decision ?? 'En attente' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-400">Aucun résultat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.secretariat>