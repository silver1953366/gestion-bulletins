<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentAnnee: { id: '', libelle: '', active: false },
        editRoute: '',
        openEditModal(annee) {
            this.currentAnnee = annee;
            this.editRoute = `/admin/annees/${annee.id}`;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Calendrier Académique</h1>
                    <p class="text-slate-500 text-sm font-bold italic mt-1">Gestion des cycles annuels — INPTIC</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-xl shadow-slate-200 gap-3 group">
                    <svg class="w-5 h-5 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    Nouvelle Année
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-8 py-6">Période Académique</th>
                        <th class="px-8 py-6">État du Système</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($annees as $annee)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-slate-900 text-xl italic uppercase tracking-tighter">{{ $annee->libelle }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($annee->active)
                                <span class="inline-flex items-center gap-2 py-2 px-5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 uppercase tracking-[0.15em] border border-emerald-100">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    Session Active
                                </span>
                            @else
                                <span class="py-2 px-5 rounded-full text-[10px] font-black bg-slate-100 text-slate-400 uppercase tracking-[0.15em]">
                                    Archivée
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEditModal({{ $annee->toJson() }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                @if(!$annee->active)
                                <form action="{{ route('admin.annees.destroy', $annee) }}" method="POST" onsubmit="return confirm('Attention : La suppression d\'une année peut corrompre les données liées. Continuer ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <p class="text-slate-400 font-bold italic">Aucune année configurée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $annees->links() }}
            </div>
        </div>

        <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 animate-scale-in">
                <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-2xl font-black text-slate-900 italic uppercase tracking-tighter">Ouvrir une session</h2>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-rose-600 transition">✕</button>
                </div>
                <form action="{{ route('admin.annees.store') }}" method="POST" class="p-10 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Libellé de l'année</label>
                        <input type="text" name="libelle" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-slate-900 rounded-2xl font-bold text-sm" placeholder="Ex: 2025-2026">
                    </div>
                    <label class="flex items-center gap-4 p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" name="active" value="1" class="w-6 h-6 text-slate-900 border-slate-300 rounded-lg focus:ring-slate-900">
                        <div>
                            <span class="block text-xs font-black text-slate-700 uppercase tracking-widest">Activer immédiatement</span>
                            <span class="text-[10px] text-slate-400 font-bold italic">Ceci désactivera l'année actuellement en cours.</span>
                        </div>
                    </label>
                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-xl mt-4">
                        Lancer le cycle académique
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 animate-scale-in border-2 border-indigo-500/20">
                <div class="px-10 py-8 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center">
                    <h2 class="text-2xl font-black text-indigo-900 italic uppercase tracking-tighter">Modifier l'année</h2>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-indigo-600 transition">✕</button>
                </div>
                <form :action="editRoute" method="POST" class="p-10 space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Libellé</label>
                        <input type="text" name="libelle" x-model="currentAnnee.libelle" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                    </div>
                    <label class="flex items-center gap-4 p-5 bg-indigo-50/50 rounded-[1.5rem] border border-indigo-100 cursor-pointer">
                        <input type="checkbox" name="active" value="1" x-model="currentAnnee.active" class="w-6 h-6 text-indigo-600 border-indigo-300 rounded-lg focus:ring-indigo-500">
                        <div>
                            <span class="block text-xs font-black text-indigo-700 uppercase tracking-widest">Définir comme active</span>
                            <span class="text-[10px] text-indigo-400 font-bold italic">Bascule le système sur cette période.</span>
                        </div>
                    </label>
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition shadow-xl mt-4">
                        Mettre à jour la session
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>