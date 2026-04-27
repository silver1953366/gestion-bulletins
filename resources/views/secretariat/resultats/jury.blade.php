{{-- resources/views/secretariat/resultats/jury.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Décisions du jury - Secrétariat</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.resultats.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour au suivi des résultats
        </a>
    </div>

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Admis</p>
                    <p class="text-4xl font-black">{{ $stats['admis'] ?? 0 }}</p>
                </div>
                <i class="fas fa-check-circle text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Redoublants</p>
                    <p class="text-4xl font-black">{{ $stats['redoublants'] ?? 0 }}</p>
                </div>
                <i class="fas fa-repeat text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Exclus</p>
                    <p class="text-4xl font-black">{{ $stats['exclus'] ?? 0 }}</p>
                </div>
                <i class="fas fa-ban text-5xl opacity-50"></i>
            </div>
        </div>
    </div>

    {{-- Formulaire de validation --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-gavel text-teal-600 text-xl"></i>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 italic">Décisions du jury</h2>
                        <p class="text-slate-500 text-sm mt-1">Validation des délibérations</p>
                    </div>
                </div>
                <button type="submit" form="juryForm" class="bg-teal-600 text-white px-6 py-3 rounded-2xl font-black uppercase text-sm hover:bg-teal-700 transition">
                    <i class="fas fa-save mr-2"></i> Enregistrer les décisions
                </button>
            </div>
        </div>

        <form id="juryForm" action="{{ route('secretariat.resultats.validate') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left p-4 text-xs font-black text-slate-400 uppercase">Étudiant</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne S5</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne S6</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Moyenne générale</th>
                            <th class="text-center p-4 text-xs font-black text-slate-400 uppercase">Décision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultats as $resultat)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center">
                                        <i class="fas fa-user-graduate text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $resultat->etudiant->prenom }} {{ $resultat->etudiant->nom }}</p>
                                        <p class="text-xs text-slate-400">ID: {{ $resultat->etudiant->id }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="resultats[{{ $loop->index }}][id]" value="{{ $resultat->id }}">
                            </td>
                            <td class="p-4 text-center font-bold">{{ number_format($resultat->moyenne_s5 ?? 0, 2) }}</td>
                            <td class="p-4 text-center font-bold">{{ number_format($resultat->moyenne_s6 ?? 0, 2) }}</td>
                            <td class="p-4 text-center">
                                <span class="text-xl font-black {{ ($resultat->moyenne ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ number_format($resultat->moyenne ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <select name="resultats[{{ $loop->index }}][decision]" class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-sm">
                                    <option value="ADMIS" {{ $resultat->decision == 'ADMIS' ? 'selected' : '' }} class="text-emerald-600">✅ ADMIS</option>
                                    <option value="REDOUBLEMENT" {{ $resultat->decision == 'REDOUBLEMENT' ? 'selected' : '' }} class="text-amber-600">🔄 REDOUBLEMENT</option>
                                    <option value="EXCLU" {{ $resultat->decision == 'EXCLU' ? 'selected' : '' }} class="text-rose-600">❌ EXCLU</option>
                                </select>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400 italic">
                                Aucun résultat à afficher
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</x-layouts.secretariat>