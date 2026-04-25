<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Configuration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-indigo-600 italic uppercase">Cycles Académiques</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Calendrier <span class="text-indigo-600">Académique</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-annee')" class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" stroke-linecap="round"/>
                </svg>
                Ouvrir une session
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentAnnee: { libelle: '', active: false }, 
        editAction: '',
        openEdit(annee) {
            this.currentAnnee = annee;
            this.editAction = '/admin/annees/' + annee.id;
            $dispatch('open-modal', 'edit-annee');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden text-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Période Académique</th>
                        <th class="px-10 py-6 text-center">État du Système</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($annees as $annee)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <span class="font-black text-slate-900 text-xl italic uppercase tracking-tighter">{{ $annee->libelle }}</span>
                        </td>
                        <td class="px-10 py-5 text-center">
                            @if($annee->active)
                                <span class="inline-flex items-center gap-2 py-2 px-5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 uppercase tracking-[0.15em] border border-emerald-100">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    Session Active
                                </span>
                            @else
                                <span class="py-2 px-5 rounded-full text-[10px] font-black bg-slate-100 text-slate-400 uppercase tracking-[0.15em] italic">
                                    Archivée
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-5">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEdit({{ $annee->toJson() }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                @if(!$annee->active)
                                <form action="{{ route('admin.annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Attention : La suppression peut corrompre les données liées.')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20 text-center opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400">Aucune année configurée</td>
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
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Ouvrir une <span class="text-indigo-600 italic">Session</span></h2>
                
                <form action="{{ route('admin.annees.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé de l'année</label>
                        <input type="text" name="libelle" required placeholder="Ex: 2025-2026" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <label class="flex items-center gap-4 p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" name="active" value="1" class="w-6 h-6 text-indigo-600 border-slate-300 rounded-lg focus:ring-indigo-500">
                        <div>
                            <span class="block text-xs font-black text-slate-700 uppercase tracking-widest">Activer immédiatement</span>
                            <span class="text-[10px] text-slate-400 font-bold italic">Ceci désactivera l'année actuellement en cours.</span>
                        </div>
                    </label>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg">Lancer le cycle</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE EDIT --}}
        <x-modal name="edit-annee" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Modifier la <span class="text-indigo-600 italic">Session</span></h2>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" x-model="currentAnnee.libelle" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <label class="flex items-center gap-4 p-5 bg-indigo-50/50 rounded-[1.5rem] border border-indigo-100 cursor-pointer">
                        <input type="checkbox" name="active" value="1" x-model="currentAnnee.active" class="w-6 h-6 text-indigo-600 border-indigo-300 rounded-lg focus:ring-indigo-500">
                        <div>
                            <span class="block text-xs font-black text-indigo-700 uppercase tracking-widest">Définir comme active</span>
                            <span class="text-[10px] text-indigo-400 font-bold italic">Bascule l'ensemble du système sur cette période.</span>
                        </div>
                    </label>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>