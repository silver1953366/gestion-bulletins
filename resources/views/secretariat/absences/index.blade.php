{{-- resources/views/secretariat/absences/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Gestion des absences - Secrétariat</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Gestion des absences</h2>
            <p class="text-slate-500 text-sm mt-1">Saisie et suivi des heures d'absence</p>
        </div>
        <a href="{{ route('secretariat.absences.create') }}" 
           class="bg-teal-600 text-white px-6 py-3 rounded-2xl font-black uppercase text-sm hover:bg-teal-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i> Saisir absence
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Filtrer par étudiant</label>
                <select name="etudiant" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                    <option value="">Tous les étudiants</option>
                    @foreach($etudiants as $etudiant)
                        <option value="{{ $etudiant->id }}" {{ request('etudiant') == $etudiant->id ? 'selected' : '' }}>
                            {{ $etudiant->prenom }} {{ $etudiant->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Filtrer par matière</label>
                <select name="matiere" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                    <option value="">Toutes les matières</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ request('matiere') == $matiere->id ? 'selected' : '' }}>
                            {{ $matiere->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-slate-800 text-white px-4 py-3 rounded-2xl font-black uppercase text-sm hover:bg-slate-900">
                    <i class="fas fa-search mr-2"></i> Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Liste des absences --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Étudiant</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Matière</th>
                        <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Heures</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Justification</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Date</th>
                        <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $absence)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-rose-100 flex items-center justify-center">
                                    <i class="fas fa-user text-rose-600"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-800">{{ $absence->etudiant->prenom }} {{ $absence->etudiant->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-sm text-slate-600">{{ $absence->matiere->libelle ?? 'N/A' }}</td>
                        <td class="p-5 text-center">
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">{{ $absence->heures }} h</span>
                        </td>
                        <td class="p-5 text-sm text-slate-500">{{ $absence->justification ?? 'Non justifiée' }}</td>
                        <td class="p-5 text-sm text-slate-500">{{ $absence->created_at->format('d/m/Y') }}</td>
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('secretariat.absences.edit', $absence->id) }}" class="p-2 bg-amber-50 rounded-xl text-amber-600 hover:bg-amber-100">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('secretariat.absences.destroy', $absence->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 rounded-xl text-rose-600 hover:bg-rose-100" onclick="return confirm('Supprimer cette absence ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('secretariat.absences.penalites', $absence->etudiant_id) }}" class="p-2 bg-indigo-50 rounded-xl text-indigo-600 hover:bg-indigo-100">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </div>
                        </td>
                    </table>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 italic">
                            Aucune absence enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100">
            {{ $absences->links() }}
        </div>
    </div>
</x-layouts.secretariat>