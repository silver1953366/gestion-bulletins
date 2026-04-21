<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentClasse: { id: '', nom: '', filiere_id: '', niveau_id: '', annee_universitaire: '' },
        editRoute: '',
        openEditModal(classe) {
            this.currentClasse = classe;
            this.editRoute = `/admin/classes/${classe.id}`;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Gestion des Classes</h1>
                    <p class="text-slate-500 text-sm font-bold italic mt-1">Structure pédagogique — INPTIC</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 gap-3 group">
                    <svg class="w-5 h-5 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                    Créer une Classe
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-8 py-6">Nom de la Classe</th>
                        <th class="px-8 py-6">Filière & Niveau</th>
                        <th class="px-8 py-6 text-center">Année Univ.</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($classes as $classe)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-slate-900 text-lg italic uppercase tracking-tighter">{{ $classe->nom }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-indigo-600 font-black text-xs uppercase">{{ $classe->filiere->nom ?? 'N/A' }}</span>
                                <span class="text-[10px] font-bold text-slate-400 italic">{{ $classe->niveau->nom ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-4 py-1.5 bg-slate-100 rounded-full text-xs font-black text-slate-500 italic">
                                {{ $classe->annee_universitaire }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEditModal({{ $classe->toJson() }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
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
                        <td colspan="4" class="px-8 py-20 text-center text-slate-400 font-bold italic">Aucune classe trouvée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $classes->links() }}
            </div>
        </div>

        <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-xl overflow-hidden relative z-10 animate-scale-in">
                <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-2xl font-black text-slate-900 italic uppercase tracking-tighter">Nouvelle Classe</h2>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-rose-600 transition">✕</button>
                </div>
                <form action="{{ route('admin.classes.store') }}" method="POST" class="p-10 space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom (Ex: LP ASUR)</label>
                            <input type="text" name="nom" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-600 rounded-2xl font-bold text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Année Universitaire</label>
                            <input type="text" name="annee_universitaire" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-600 rounded-2xl font-bold text-sm" placeholder="2025-2026">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Filière</label>
                            <select name="filiere_id" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-600 rounded-2xl font-bold text-sm">
                                <option value="">Choisir...</option>
                                @foreach($filieres as $f) <option value="{{ $f->id }}">{{ $f->nom }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Niveau</label>
                            <select name="niveau_id" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-600 rounded-2xl font-bold text-sm">
                                <option value="">Choisir...</option>
                                @foreach($niveaux as $n) <option value="{{ $n->id }}">{{ $n->nom }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-xl mt-4">
                        Enregistrer la formation
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-xl overflow-hidden relative z-10 animate-scale-in border-2 border-indigo-500/20">
                <div class="px-10 py-8 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center">
                    <h2 class="text-2xl font-black text-indigo-900 italic uppercase tracking-tighter">Modifier la Classe</h2>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-indigo-600 transition">✕</button>
                </div>
                <form :action="editRoute" method="POST" class="p-10 space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la classe</label>
                            <input type="text" name="nom" x-model="currentClasse.nom" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Année Universitaire</label>
                            <input type="text" name="annee_universitaire" x-model="currentClasse.annee_universitaire" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Filière</label>
                            <select name="filiere_id" x-model="currentClasse.filiere_id" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                @foreach($filieres as $f) <option value="{{ $f->id }}">{{ $f->nom }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Niveau</label>
                            <select name="niveau_id" x-model="currentClasse.niveau_id" required class="w-full px-5 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                @foreach($niveaux as $n) <option value="{{ $n->id }}">{{ $n->nom }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition shadow-xl mt-4">
                        Mettre à jour la classe
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>