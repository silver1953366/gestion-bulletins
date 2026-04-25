<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Structure</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-indigo-600 italic uppercase">Offre de Formation</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Gestion des <span class="text-indigo-600">Classes</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-classe')" class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/>
                </svg>
                Créer une Classe
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentClasse: { nom: '', filiere_id: '', niveau_id: '', annee_universitaire: '' }, 
        editAction: '',
        openEdit(classe) {
            this.currentClasse = classe;
            this.editAction = '/admin/classes/' + classe.id;
            $dispatch('open-modal', 'edit-classe');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden text-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Nom de la Classe</th>
                        <th class="px-10 py-6">Filière & Niveau</th>
                        <th class="px-10 py-6 text-center">Année Univ.</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($classes as $classe)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <span class="font-black text-slate-900 text-xl italic uppercase tracking-tighter">{{ $classe->nom }}</span>
                        </td>
                        <td class="px-10 py-5">
                            <div class="flex flex-col">
                                <span class="text-indigo-600 font-black text-[10px] uppercase tracking-widest">{{ $classe->filiere->nom ?? 'N/A' }}</span>
                                <span class="text-[10px] font-bold text-slate-400 italic">{{ $classe->niveau->nom ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-5 text-center">
                            <span class="px-4 py-1.5 bg-slate-100 rounded-full text-[10px] font-black text-slate-500 italic uppercase">
                                {{ $classe->annee_universitaire }}
                            </span>
                        </td>
                        <td class="px-10 py-5">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEdit({{ $classe->toJson() }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <form action="{{ route('admin.classes.destroy', $classe) }}" method="POST" onsubmit="return confirm('Supprimer cette classe ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400">Aucune classe répertoriée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($classes->hasPages())
            <div class="px-2">{{ $classes->links() }}</div>
        @endif

        {{-- MODALE AJOUT --}}
        <x-modal name="add-classe" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Nouvelle <span class="text-indigo-600 italic">Formation</span></h2>
                
                <form action="{{ route('admin.classes.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la classe</label>
                            <input type="text" name="nom" required placeholder="Ex: LP ASUR" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Année Universitaire</label>
                            <input type="text" name="annee_universitaire" required placeholder="2025-2026" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Filière</label>
                            <select name="filiere_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase tracking-tighter">
                                <option value="">Choisir...</option>
                                @foreach($filieres as $f) <option value="{{ $f->id }}">{{ $f->nom }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Niveau</label>
                            <select name="niveau_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase italic">
                                <option value="">Choisir...</option>
                                @foreach($niveaux as $n) <option value="{{ $n->id }}">{{ $n->nom }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE EDIT --}}
        <x-modal name="edit-classe" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Modifier la <span class="text-indigo-600 italic">Classe</span></h2>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                            <input type="text" name="nom" x-model="currentClasse.nom" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Année</label>
                            <input type="text" name="annee_universitaire" x-model="currentClasse.annee_universitaire" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Filière</label>
                            <select name="filiere_id" x-model="currentClasse.filiere_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase tracking-tighter">
                                @foreach($filieres as $f) <option value="{{ $f->id }}">{{ $f->nom }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Niveau</label>
                            <select name="niveau_id" x-model="currentClasse.niveau_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase italic">
                                @foreach($niveaux as $n) <option value="{{ $n->id }}">{{ $n->nom }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">Fermer</button>
                        <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>