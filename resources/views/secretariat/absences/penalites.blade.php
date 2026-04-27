{{-- resources/views/secretariat/absences/penalites.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Pénalités - {{ $etudiant->prenom }} {{ $etudiant->nom }}</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.absences.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-rose-600 to-rose-800 rounded-3xl p-8 text-white">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-5xl mb-4"></i>
                    <h3 class="text-2xl font-black">Pénalités</h3>
                    <div class="mt-6 p-6 bg-white/10 rounded-2xl">
                        <p class="text-sm opacity-80">Total des heures d'absence</p>
                        <p class="text-5xl font-black">{{ $totalAbsences }}</p>
                    </div>
                    <div class="mt-4 p-6 bg-white/10 rounded-2xl">
                        <p class="text-sm opacity-80">Points de pénalité</p>
                        <p class="text-5xl font-black text-amber-300">{{ $penalites }}</p>
                        <p class="text-xs mt-2">(1 point par tranche de 10h d'absence)</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-rose-50 to-white border-b border-slate-100">
                    <h3 class="text-lg font-black text-slate-800">Détail des absences</h3>
                    <p class="text-sm text-slate-500">{{ $etudiant->prenom }} {{ $etudiant->nom }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left p-4 text-xs font-black text-slate-400">Matière</th>
                                <th class="text-center p-4 text-xs font-black text-slate-400">Heures</th>
                                <th class="text-left p-4 text-xs font-black text-slate-400">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absences as $absence)
                            <tr class="border-b border-slate-50">
                                <td class="p-4">{{ $absence->matiere->libelle ?? 'N/A' }}</td>
                                <td class="p-4 text-center">{{ $absence->heures }} h</td>
                                <td class="p-4">{{ $absence->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.secretariat>