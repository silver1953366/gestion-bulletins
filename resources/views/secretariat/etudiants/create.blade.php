{{-- resources/views/secretariat/etudiants/create.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Ajouter un étudiant</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.etudiants.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h2 class="text-2xl font-black text-slate-900 italic mb-6">Ajouter un nouvel étudiant</h2>
        
        <form action="{{ route('secretariat.etudiants.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100" required>
                    @error('nom') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100" required>
                    @error('prenom') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Baccalauréat</label>
                    <input type="text" name="bac" value="{{ old('bac') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Provenance</label>
                    <input type="text" name="provenance" value="{{ old('provenance') }}" 
                           class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Classe d'inscription</label>
                    <select name="classe_id" class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400">
                        <option value="">-- Sans inscription --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('secretariat.etudiants.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase text-sm hover:bg-slate-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-teal-600 text-white rounded-2xl font-black uppercase text-sm hover:bg-teal-700">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-layouts.secretariat>