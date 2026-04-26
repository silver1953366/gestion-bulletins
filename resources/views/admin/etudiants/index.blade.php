<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
                    Annuaire <span class="text-indigo-600 font-extrabold">Étudiants</span>
                </h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-2">
                    Gestion des dossiers académiques • INPTIC {{ date('Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div x-data="studentManager()" class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Inscrits</p>
                <p class="text-4xl font-black text-slate-900 italic">{{ $etudiants->total() }}</p>
            </div>
            <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100">
                <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Dossiers Finalisés</p>
                <p class="text-4xl font-black text-white italic">{{ $etudiants->where('is_finalized', true)->count() }}</p>
            </div>
            <div class="bg-amber-400 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100">
                <p class="text-[10px] font-black text-amber-900 uppercase tracking-widest mb-1">En attente</p>
                <p class="text-4xl font-black text-slate-900 italic">{{ $etudiants->where('is_finalized', false)->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-10 py-8 text-center w-20 italic">#</th>
                            <th class="px-10 py-8">Profil & Étudiant</th>
                            <th class="px-10 py-8">Matricule</th>
                            <th class="px-10 py-8">Cursus & Origine</th>
                            <th class="px-10 py-8 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($etudiants as $etudiant)
                        <tr class="group hover:bg-indigo-50/30 transition-all duration-300">
                             <td class="px-10 py-6 text-center">
                                <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-[10px] font-black">
                                    {{ $loop->iteration }}
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl overflow-hidden bg-indigo-50 flex items-center justify-center shadow-inner border border-slate-100">
                                        @if($etudiant->photo)
                                            <img src="{{ asset('storage/' . $etudiant->photo) }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-indigo-600 font-black text-lg">{{ substr($etudiant->nom, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-black text-slate-900 uppercase italic leading-none">{{ $etudiant->nom }} {{ $etudiant->prenom }}</p>
                                            @if(!$etudiant->is_finalized)
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[8px] font-black rounded uppercase">À Finaliser</span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $etudiant->studentProfile->user->email ?? 'Pas d\'email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                @if($etudiant->is_finalized && isset($etudiant->studentProfile->matricule))
                                    <span class="px-4 py-2 bg-indigo-50 rounded-xl text-[10px] font-black tracking-widest text-indigo-600 border border-indigo-100 italic">
                                        {{ $etudiant->studentProfile->matricule }}
                                    </span>
                                @else
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Génération en attente...</span>
                                @endif
                            </td>
                            <td class="px-10 py-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-slate-900 uppercase">BAC : <span class="text-indigo-600">{{ $etudiant->bac ?? '---' }}</span></p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase italic">{{ $etudiant->provenance ?? 'Origine inconnue' }}</p>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-right space-x-1">
                                <button @click="showStudent({{ $etudiant->toJson() }})" class="p-3 bg-white border border-slate-200 text-slate-400 rounded-xl hover:text-indigo-600 hover:border-indigo-200 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                
                                <button @click="editStudent({{ $etudiant->toJson() }})" class="p-3 bg-white border border-slate-200 text-slate-400 rounded-xl hover:text-emerald-600 hover:border-emerald-200 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <button @click="confirmDelete({{ $etudiant->id }})" class="p-3 bg-white border border-slate-200 text-rose-300 rounded-xl hover:text-rose-600 hover:border-rose-200 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>

                                @if(!$etudiant->is_finalized)
                                    <button @click="openFinalize({{ $etudiant->toJson() }})" class="ml-2 px-4 py-3 bg-amber-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-amber-100 hover:bg-slate-900 transition-all">
                                        Finaliser
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-10 py-20 text-center font-black text-slate-300 uppercase tracking-widest text-[10px]">Aucun dossier étudiant</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-10 bg-slate-50/30 border-t border-slate-50">{{ $etudiants->links() }}</div>
        </div>

        <x-modal name="show-etudiant" focusable>
            <div class="p-0 bg-white overflow-hidden rounded-[3rem]">
                <div class="h-40 bg-indigo-600 relative">
                    <div class="absolute -bottom-16 left-10 p-2 bg-white rounded-[2.5rem] shadow-2xl border border-slate-50">
                        <div class="h-32 w-32 rounded-[2rem] bg-slate-100 overflow-hidden flex items-center justify-center">
                            <template x-if="currentEtudiant.photo">
                                <img :src="'/storage/' + currentEtudiant.photo" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!currentEtudiant.photo">
                                <span class="text-4xl font-black text-indigo-600" x-text="currentEtudiant.nom ? currentEtudiant.nom.substring(0,1) : '?'"></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="pt-20 px-10 pb-10">
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <h3 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter" x-text="currentEtudiant.nom + ' ' + currentEtudiant.prenom"></h3>
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em]" x-text="currentEtudiant.is_finalized ? (currentEtudiant.student_profile?.matricule || 'GÉNÉRÉ') : 'INSCRIPTION EN ATTENTE'"></p>
                        </div>
                        <span :class="currentEtudiant.is_finalized ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'" class="px-6 py-2 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm" x-text="currentEtudiant.is_finalized ? 'Dossier Finalisé' : 'En attente de finalisation'"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="p-8 bg-slate-50 rounded-[2.5rem] space-y-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-2">État Civil</p>
                            <p class="text-xs font-bold text-slate-600 uppercase">Date de naissance : <span class="text-slate-900 font-black italic" x-text="currentEtudiant.date_naissance || '---'"></span></p>
                            <p class="text-xs font-bold text-slate-600 uppercase">Lieu : <span class="text-slate-900 font-black italic" x-text="currentEtudiant.lieu_naissance || '---'"></span></p>
                        </div>
                        <div class="p-8 bg-slate-50 rounded-[2.5rem] space-y-3">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 pb-2">Parcours Académique</p>
                            <p class="text-xs font-bold text-slate-600 uppercase">Série du BAC : <span class="text-slate-900 font-black italic" x-text="currentEtudiant.bac || '---'"></span></p>
                            <p class="text-xs font-bold text-slate-600 uppercase">Provenance : <span class="text-slate-900 font-black italic" x-text="currentEtudiant.provenance || '---'"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal>

        <x-modal name="edit-etudiant" focusable>
            <form :action="'/admin/etudiants/' + currentEtudiant.id" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
                @csrf @method('PUT')
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic">Modifier le <span class="text-indigo-500">Dossier</span></h2>
                    <div class="text-right px-4 py-2 bg-slate-900 rounded-2xl">
                        <p class="text-[8px] font-black text-slate-400 uppercase">Matricule Gelé</p>
                        <p class="text-[10px] font-black text-white italic" x-text="currentEtudiant.student_profile?.matricule || 'SANS MATRICULE'"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-2 tracking-widest">Nom</label>
                        <input type="text" name="nom" x-model="currentEtudiant.nom" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-2 tracking-widest">Prénom</label>
                        <input type="text" name="prenom" x-model="currentEtudiant.prenom" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                    </div>
                    <div class="col-span-2 space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase ml-2 tracking-widest">Photo de profil</label>
                        <input type="file" name="photo" class="w-full px-6 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold">
                    </div>
                    <input type="date" name="date_naissance" x-model="currentEtudiant.date_naissance" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                    <input type="text" name="lieu_naissance" x-model="currentEtudiant.lieu_naissance" placeholder="Lieu" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                    <input type="text" name="bac" x-model="currentEtudiant.bac" placeholder="BAC" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm uppercase">
                    <input type="text" name="provenance" x-model="currentEtudiant.provenance" placeholder="Lycée" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" @click="$dispatch('close')" class="px-6 py-3 text-[10px] font-black uppercase text-slate-400">Annuler</button>
                    <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition shadow-xl">Appliquer les changements</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="finalize-etudiant" focusable>
            <div class="p-10 bg-white">
                <div class="flex justify-between items-start mb-8">
                    <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter">
                        Finaliser <br><span class="text-amber-500" x-text="currentEtudiant.nom + ' ' + currentEtudiant.prenom"></span>
                    </h2>
                    <div class="px-6 py-2 bg-amber-50 text-amber-600 rounded-full text-[9px] font-black uppercase italic tracking-widest">Dossier en attente</div>
                </div>
                
                <form :action="'/admin/etudiants/finalize/' + currentEtudiant.id" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-4 italic">Date de Naissance officielle</label>
                            <input type="date" name="date_naissance" required class="w-full px-6 py-4 bg-slate-100 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-4 italic">Lieu de Naissance</label>
                            <input type="text" name="lieu_naissance" required placeholder="Ex: Libreville" class="w-full px-6 py-4 bg-slate-100 border-none rounded-2xl font-bold text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-4 italic">Série du BAC</label>
                            <input type="text" name="bac" required placeholder="Ex: BAC C" class="w-full px-6 py-4 bg-slate-100 border-none rounded-2xl font-bold text-sm uppercase">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-4 italic">Lycée / Provenance</label>
                            <input type="text" name="provenance" required placeholder="Ex: Lycée Technique" class="w-full px-6 py-4 bg-slate-100 border-none rounded-2xl font-bold text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-10">
                        <button type="button" @click="$dispatch('close')" class="text-[10px] font-black uppercase text-slate-400 hover:text-rose-500 transition">Finaliser plus tard</button>
                        <button type="submit" class="px-12 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-widest shadow-2xl hover:bg-indigo-600 transition-all">
                            Générer Matricule & Clôturer
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="confirm-delete-etudiant" focusable>
            <form :action="'/admin/etudiants/' + deleteId" method="POST" class="p-10 text-center">
                @csrf @method('DELETE')
                <div class="h-20 w-20 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase italic mb-2 tracking-tighter">Suppression du dossier</h3>
                <p class="text-xs font-bold text-slate-400 mb-8 uppercase tracking-widest">Attention : Cette action est irréversible.</p>
                <div class="flex justify-center gap-4">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-4 bg-slate-100 text-slate-400 rounded-2xl text-[10px] font-black uppercase">Abandonner</button>
                    <button type="submit" class="px-8 py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase shadow-xl shadow-rose-100">Confirmer</button>
                </div>
            </form>
        </x-modal>

        @if(session('finalized_success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="fixed bottom-10 right-10 z-50">
            <div class="bg-slate-900 text-white p-6 rounded-[2.5rem] shadow-2xl border border-emerald-500/30 flex items-center gap-6">
                <div class="h-14 w-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-400">Opération réussie</p>
                    <p class="text-sm font-black uppercase italic leading-none mt-1">Matricule généré : {{ session('matricule') }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Dossier marqué comme finalisé</p>
                </div>
            </div>
        </div>
        @endif

    </div>

    <script>
        function studentManager() {
            return {
                currentEtudiant: {},
                deleteId: null,
                etudiantsList: @json($etudiants->items()),

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const finalizeId = urlParams.get('finalize');
                    
                    if(finalizeId) {
                        let etu = this.etudiantsList.find(e => e.id == finalizeId);
                        if(etu) {
                            setTimeout(() => this.openFinalize(etu), 500);
                        }
                    }
                },

                showStudent(etudiant) {
                    this.currentEtudiant = etudiant;
                    this.$dispatch('open-modal', 'show-etudiant');
                },

                editStudent(etudiant) {
                    this.currentEtudiant = JSON.parse(JSON.stringify(etudiant));
                    this.$dispatch('open-modal', 'edit-etudiant');
                },

                confirmDelete(id) {
                    this.deleteId = id;
                    this.$dispatch('open-modal', 'confirm-delete-etudiant');
                },

                openFinalize(etudiant) {
                    this.currentEtudiant = etudiant;
                    this.$dispatch('open-modal', 'finalize-etudiant');
                }
            }
        }
    </script>
</x-app-layout>