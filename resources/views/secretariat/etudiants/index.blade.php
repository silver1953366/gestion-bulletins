{{-- resources/views/secretariat/etudiants/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Gestion des étudiants - Secrétariat</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Gestion des étudiants</h2>
            <p class="text-slate-500 text-sm mt-1">Liste complète des étudiants inscrits</p>
        </div>
        <a href="{{ route('secretariat.etudiants.create') }}" 
           class="bg-teal-600 text-white px-6 py-3 rounded-2xl font-black uppercase text-sm hover:bg-teal-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i> Nouvel étudiant
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">ID</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Nom & Prénom</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Date naissance</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Bac</th>
                        <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Classe</th>
                        <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($etudiants as $etudiant)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition">
                        <td class="p-5 text-sm font-mono font-bold text-slate-500">{{ $etudiant->id }}</td>
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center">
                                    <span class="text-teal-600 font-black">{{ substr($etudiant->prenom ?? '', 0, 1) }}{{ substr($etudiant->nom ?? '', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-black text-slate-800">{{ $etudiant->prenom }} {{ $etudiant->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-sm text-slate-600">{{ $etudiant->date_naissance ?? 'N/A' }}</td>
                        <td class="p-5 text-sm text-slate-600">{{ $etudiant->bac ?? 'N/A' }}</td>
                        <td class="p-5">
                            @php
                                $classe = $etudiant->inscriptions->first()?->classe;
                            @endphp
                            <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-bold">{{ $classe->nom ?? 'Non inscrit' }}</span>
                        </td>
                        <td class="p-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('secretariat.etudiants.show', $etudiant->id) }}" 
                                   class="p-2 bg-indigo-50 rounded-xl text-indigo-600 hover:bg-indigo-100 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('secretariat.etudiants.fiche', $etudiant->id) }}" 
                                   class="p-2 bg-teal-50 rounded-xl text-teal-600 hover:bg-teal-100 transition">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                                <a href="{{ route('secretariat.etudiants.edit', $etudiant->id) }}" 
                                   class="p-2 bg-amber-50 rounded-xl text-amber-600 hover:bg-amber-100 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('secretariat.etudiants.destroy', $etudiant->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 rounded-xl text-rose-600 hover:bg-rose-100 transition" 
                                            onclick="return confirm('Supprimer cet étudiant ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 italic">
                            Aucun étudiant enregistré
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100">
            {{ $etudiants->links() }}
        </div>
    </div>
</x-layouts.secretariat>