<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-sky-600 italic uppercase">Départements</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Unités <span class="text-sky-600 underline decoration-sky-100 underline-offset-8">Académiques</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-dept')" class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest hover:bg-sky-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:-translate-y-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Nouveau Département
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentDept: { id: '', nom: '' }, 
        editAction: '',
        openEdit(dept) {
            this.currentDept = dept;
            this.editAction = '/admin/departements/' + dept.id;
            $dispatch('open-modal', 'edit-dept');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse border-spacing-0">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Nom du Département</th>
                        <th class="px-10 py-6">Statistiques</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-medium">
                    @forelse($departements as $dept)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <span class="font-black text-slate-900 text-lg uppercase italic tracking-tighter">{{ $dept->nom }}</span>
                        </td>
                        <td class="px-10 py-5">
                            <span class="inline-flex items-center px-4 py-1.5 bg-sky-50 text-sky-600 rounded-full font-black text-[10px] uppercase tracking-widest italic">
                                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                                {{ $dept->filieres_count ?? 0 }} Filière(s)
                            </span>
                        </td>
                        <td class="px-10 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEdit({{ json_encode($dept) }})" class="p-2.5 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <form action="{{ route('admin.departements.destroy', $dept) }}" method="POST" onsubmit="return confirm('Supprimer ce département ?')">
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
                        <td colspan="3" class="py-20 text-center opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400">Aucun département défini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departements->hasPages())
            <div class="px-2">{{ $departements->links() }}</div>
        @endif

        {{-- MODALE AJOUT - Parfaitement centrée --}}
        <x-modal name="add-dept" focusable>
            <div class="p-10 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Nouveau <span class="text-sky-600 italic">Département</span></h2>
                    <button x-on:click="$dispatch('close')" class="text-slate-300 hover:text-rose-600 transition-colors uppercase font-black text-xs">Fermer</button>
                </div>
                
                <form action="{{ route('admin.departements.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Désignation Officielle</label>
                        <input type="text" name="nom" required placeholder="Ex: Informatique et Multimédia" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-sky-600 transition-all placeholder:text-slate-300 placeholder:italic placeholder:font-medium">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-sky-600 transition-all shadow-xl shadow-slate-200">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE MODIFICATION - Parfaitement centrée --}}
        <x-modal name="edit-dept" focusable>
            <div class="p-10 transform transition-all">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Édition <span class="text-sky-600 italic">Structure</span></h2>
                    <span class="px-3 py-1 bg-sky-50 text-sky-600 rounded-lg text-[10px] font-black" x-text="'REF: ' + currentDept.id"></span>
                </div>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom du département</label>
                        <input type="text" name="nom" x-model="currentDept.nom" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-sky-600 transition-all">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Abandonner</button>
                        <button type="submit" class="px-10 py-4 bg-sky-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-sky-100">Appliquer les changements</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>