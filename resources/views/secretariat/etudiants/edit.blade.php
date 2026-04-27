{{-- resources/views/secretariat/etudiants/edit.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Modifier étudiant</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.etudiants.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h2 class="text-2xl font-black text-slate-900 italic mb-6">Modifier {{ $etudiant->prenom }} {{ $etudiant->nom }}</h2>
        
        <form action="{{ route('secretariat.etudiants.update', $etudiant->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom', $etudiant->nom) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400" required>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $etudiant->prenom) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400" required>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance', $etudiant->date_naissance) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance', $etudiant->lieu_naissance) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Baccalauréat</label>
                    <input type="text" name="bac" value="{{ old('bac', $etudiant->bac) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Provenance</label>
                    <input type="text" name="provenance" value="{{ old('provenance', $etudiant->provenance) }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400">
                </div>
            </div>
            
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('secretariat.etudiants.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase text-sm">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-2xl font-black uppercase text-sm hover:bg-teal-700">
                    <i class="fas fa-save mr-2"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-layouts.secretariat>