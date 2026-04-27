<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[9px] font-black uppercase tracking-[0.2em]">
                        <li class="text-slate-400">Configuration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-indigo-600 italic">Cycles Académiques</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">Calendrier <span class="text-indigo-600">Académique</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-annee')" class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-4 h-4 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" stroke-linecap="round"/>
                </svg>
                Ouvrir une session
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        currentAnnee: { id: null, libelle: '', active: false }, 
        editAction: '',
        deleteAction: '',
        openEdit(annee) {
            this.currentAnnee = { ...annee };
            this.editAction = '{{ url('admin/annees') }}/' + annee.id;
            $dispatch('open-modal', 'edit-annee');
        },
        openDelete(annee) {
            this.currentAnnee = annee;
            this.deleteAction = '{{ url('admin/annees') }}/' + annee.id;
            $dispatch('open-modal', 'confirm-delete');
        }
    }">
        
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden text-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-8 py-5">Période Académique</th>
                        <th class="px-8 py-5 text-center">État du Système</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($annees as $annee)
                    <tr class="hover:bg-slate-50/80 transition group">
                        <td class="px-8 py-4">
                            <span class="font-black text-slate-800 text-lg italic uppercase tracking-tighter">{{ $annee->libelle }}</span>
                        </td>
                        <td class="px-8 py-4 text-center">
                            @if($annee->active)
                                <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-600 uppercase tracking-widest border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    Session Active
                                </span>
                            @else
                                <span class="py-1.5 px-4 rounded-full text-[9px] font-black bg-slate-100 text-slate-400 uppercase tracking-widest italic">
                                    Archivée
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEdit({{ $annee->toJson() }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                @if(!$annee->active)
                                <button @click="openDelete({{ $annee->toJson() }})" class="p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center opacity-30 font-black uppercase text-[10px] tracking-[0.3em] italic text-slate-400">Aucune année configurée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($annees->hasPages())
            <div class="px-2">{{ $annees->links() }}</div>
        @endif

        {{-- MODALE AJOUT --}}
        <x-modal name="add-annee" focusable>
            <div class="p-8">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Nouveau <span class="text-indigo-600 italic">Cycle</span></h2>
                
                <form action="{{ route('admin.annees.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Libellé de l'année</label>
                        <input type="text" name="libelle" required placeholder="Ex: 2025-2026" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" name="active" value="1" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <div>
                            <span class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Activer maintenant</span>
                            <span class="text-[9px] text-slate-400 font-bold italic">Désactive automatiquement le cycle précédent.</span>
                        </div>
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400">Annuler</button>
                        <button type="submit" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-100">Créer le cycle</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE EDIT --}}
        <x-modal name="edit-annee" focusable>
            <div class="p-8">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Édition <span class="text-indigo-600 italic">Session</span></h2>
                
                <form :action="editAction" method="POST" class="space-y-5">
                    @csrf @method('PUT')
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Libellé</label>
                        <input type="text" name="libelle" x-model="currentAnnee.libelle" required class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 cursor-pointer">
                        <input type="checkbox" name="active" value="1" x-model="currentAnnee.active" class="w-5 h-5 text-indigo-600 border-indigo-300 rounded focus:ring-indigo-500">
                        <div>
                            <span class="block text-[11px] font-black text-indigo-700 uppercase tracking-widest">Définir comme active</span>
                            <span class="text-[9px] text-indigo-400 font-bold italic">Redirige l'ensemble des flux sur cette période.</span>
                        </div>
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400">Annuler</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE CONFIRMATION SUPPRESSION --}}
        <x-modal name="confirm-delete" focusable>
            <div class="p-8">
                <div class="flex items-center gap-4 text-rose-600 mb-4">
                    <div class="p-3 bg-rose-50 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black uppercase italic tracking-tighter">Action <span class="text-slate-900">Irréversible</span></h2>
                </div>

                <p class="text-[11px] font-bold text-slate-500 leading-relaxed mb-6">
                    Vous êtes sur le point de supprimer l'année <span class="text-slate-900 font-black italic" x-text="currentAnnee.libelle"></span>. 
                    <br><br>
                    <span class="text-rose-600 uppercase tracking-widest text-[9px]">Avertissement :</span> Cette action supprimera également toutes les <span class="text-slate-700">inscriptions</span>, <span class="text-slate-700">bulletins</span> et <span class="text-slate-700">résultats</span> associés. Cette opération ne peut pas être annulée.
                </p>

                <form :action="deleteAction" method="POST">
                    @csrf @method('DELETE')
                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')" class="px-5 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400">Abandonner</button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">Confirmer la suppression</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>