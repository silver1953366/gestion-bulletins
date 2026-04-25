<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-emerald-600 italic uppercase">Filières</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Offre <span class="text-emerald-600 underline decoration-emerald-100 underline-offset-8">Académique</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-filiere')" class="inline-flex items-center justify-center px-8 py-4 bg-emerald-600 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-emerald-100 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:rotate-12 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Ajouter une Filière
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentFiliere: { id: '', nom: '', departement_id: '' }, 
        editAction: '',
        openEdit(filiere) {
            this.currentFiliere = filiere;
            this.editAction = '/admin/filieres/' + filiere.id;
            $dispatch('open-modal', 'edit-filiere');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Libellé de la Filière</th>
                        <th class="px-10 py-6">Département Parent</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($filieres as $filiere)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <span class="font-black text-slate-900 text-lg uppercase italic tracking-tighter">{{ $filiere->nom }}</span>
                        </td>
                        <td class="px-10 py-5">
                            <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full font-black text-[10px] uppercase tracking-widest italic border border-emerald-100">
                                {{ $filiere->departement->nom ?? 'Non classé' }}
                            </span>
                        </td>
                        <td class="px-10 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEdit({{ json_encode($filiere) }})" class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <form action="{{ route('admin.filieres.destroy', $filiere) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette filière ?')">
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
                        <td colspan="3" class="py-20 text-center opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400 text-emerald-900">Aucune filière enregistrée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($filieres->hasPages())
            <div class="px-2">{{ $filieres->links() }}</div>
        @endif

        {{-- MODALE AJOUT - Centrage vertical et horizontal garanti --}}
        <x-modal name="add-filiere" focusable>
            <div class="p-10 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Nouvelle <span class="text-emerald-600 italic">Filière</span></h2>
                    <button x-on:click="$dispatch('close')" class="text-slate-300 hover:text-rose-600 transition-colors uppercase font-black text-[10px]">Fermer</button>
                </div>
                
                <form action="{{ route('admin.filieres.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la filière</label>
                        <input type="text" name="nom" required placeholder="Ex: Génie Informatique" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all placeholder:text-slate-300 placeholder:italic">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Département de rattachement</label>
                        <select name="departement_id" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="">Sélectionner un département...</option>
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-emerald-50">Enregistrer la filière</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE MODIFICATION - Centrage vertical et horizontal garanti --}}
        <x-modal name="edit-filiere" focusable>
            <div class="p-10 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Édition <span class="text-emerald-600 italic text-lg">Académique</span></h2>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-tighter" x-text="'ID: ' + currentFiliere.id"></span>
                </div>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la filière</label>
                        <input type="text" name="nom" x-model="currentFiliere.nom" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Changer de Département</label>
                        <select name="departement_id" x-model="currentFiliere.departement_id" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Fermer</button>
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>