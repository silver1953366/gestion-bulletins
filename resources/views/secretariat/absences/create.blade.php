{{-- resources/views/secretariat/absences/create.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Saisir une absence</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.absences.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h2 class="text-2xl font-black text-slate-900 italic mb-6">Nouvelle absence</h2>
        
        <form action="{{ route('secretariat.absences.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Étudiant *</label>
                    <select name="etudiant_id" class="w-full px-5 py-3 rounded-2xl border border-slate-200" required>
                        <option value="">Sélectionner un étudiant</option>
                        @foreach($etudiants as $etudiant)
                            <option value="{{ $etudiant->id }}">{{ $etudiant->prenom }} {{ $etudiant->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Matière *</label>
                    <select name="matiere_id" class="w-full px-5 py-3 rounded-2xl border border-slate-200" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Nombre d'heures *</label>
                    <input type="number" name="heures" min="1" max="100" value="{{ old('heures') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200" required>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Justification</label>
                    <textarea name="justification" rows="3" class="w-full px-5 py-3 rounded-2xl border border-slate-200">{{ old('justification') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('secretariat.absences.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase text-sm">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-2xl font-black uppercase text-sm hover:bg-teal-700">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-layouts.secretariat>