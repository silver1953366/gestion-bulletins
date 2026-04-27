<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Configuration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-amber-600 italic">Gestion des ECUE</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                    Catalogue <span class="text-amber-500 underline decoration-amber-100 decoration-4 underline-offset-4">Matières</span>
                </h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-matiere')" class="bg-slate-900 text-white px-8 py-4 rounded-3xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                Nouvelle Matière
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        search: '', 
        currentMatiere: { id: '', code: '', libelle: '', coefficient: 1, credits: 1, ue_id: '' },
        
        // Logique de filtrage pour la création
        filiereId: '',
        niveauId: '',
        selectedUePrefix: '',
        selectedLevelCode: '',

        generateAutoCode() {
            if (this.selectedUePrefix && this.currentMatiere.libelle.length >= 2) {
                let text = this.currentMatiere.libelle.replace(/[aeiou\s]/ig, '').substring(0, 3).toUpperCase();
                if(text.length < 2) text = this.currentMatiere.libelle.substring(0, 3).toUpperCase();
                this.currentMatiere.code = `${this.selectedLevelCode}-${this.selectedUePrefix}-${text}${Math.floor(10 + Math.random() * 90)}`;
            }
        },

        resetForm() {
            this.filiereId = '';
            this.niveauId = '';
            this.currentMatiere = { id: '', code: '', libelle: '', coefficient: 1, credits: 1, ue_id: '' };
        }
    }">

        {{-- Barre de recherche --}}
        <div class="bg-white rounded-[2.5rem] p-4 border border-slate-100 shadow-sm">
            <div class="relative">
                <input type="text" x-model="search" placeholder="Rechercher une matière ou un code..." 
                    class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-amber-500 transition-all">
                <svg class="w-5 h-5 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
            </div>
        </div>

        {{-- Tableau des matières --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 bg-slate-50/50">
                        <th class="px-10 py-6">Code ECUE</th>
                        <th class="px-10 py-6">Libellé</th>
                        <th class="px-10 py-6 text-center">UE / Filière / Niveau</th>
                        <th class="px-10 py-6 text-center">Coef / ECTS</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($matieres as $matiere)
                    <tr class="hover:bg-slate-50/50 transition group" x-show="search === '' || '{{ strtolower($matiere->libelle) }}'.includes(search.toLowerCase()) || '{{ strtolower($matiere->code) }}'.includes(search.toLowerCase())">
                        <td class="px-10 py-5">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg font-black text-[9px] border border-slate-200 uppercase">
                                {{ $matiere->code }}
                            </span>
                        </td>
                        <td class="px-10 py-5 font-black text-slate-800 text-sm italic uppercase">
                            {{ $matiere->libelle }}
                        </td>
                        <td class="px-10 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-black text-[9px] uppercase">{{ $matiere->ue->code }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase italic mt-1 leading-none">
                                    {{ $matiere->ue->semestre->classe->niveau->code }} — {{ $matiere->ue->semestre->classe->filiere->nom }}
                                </span>
                            </div>
                        </td>
                        <td class="px-10 py-5 text-center">
                            <div class="inline-flex items-center gap-2">
                                <span class="text-[9px] font-black bg-slate-900 text-white px-2 py-0.5 rounded italic">C:{{ $matiere->coefficient }}</span>
                                <span class="text-[9px] font-black bg-amber-500 text-white px-2 py-0.5 rounded italic">E:{{ $matiere->credits }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-5 text-right flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="currentMatiere = {{ $matiere->toJson() }}; $dispatch('open-modal', 'edit-matiere')" class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/></svg>
                            </button>
                            <button @click="currentMatiere = {{ $matiere->toJson() }}; $dispatch('open-modal', 'confirm-delete-matiere')" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-6 bg-slate-50/50 border-t border-slate-100">
                {{ $matieres->links() }}
            </div>
        </div>

        {{-- MODALE : AJOUT --}}
        <x-modal name="add-matiere" focusable>
            <div class="p-10" x-on:close.stop="resetForm()">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Nouvel <span class="text-amber-500">ECUE</span></h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Sélectionnez le parcours pour filtrer</p>
                </div>

                <form action="{{ route('admin.matieres.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">1. Filière</label>
                            <select x-model="filiereId" class="w-full px-4 py-3 bg-white border-none rounded-xl font-bold text-[11px] focus:ring-2 focus:ring-amber-500 shadow-sm transition">
                                <option value="">Toutes les filières</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">2. Niveau d'étude</label>
                            <select x-model="niveauId" class="w-full px-4 py-3 bg-white border-none rounded-xl font-bold text-[11px] focus:ring-2 focus:ring-amber-500 shadow-sm transition">
                                <option value="">Tous les niveaux</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->nom ?? $niveau->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-amber-600 uppercase tracking-widest ml-1 italic">3. Sélectionner l'Unité (UE)</label>
                        <select name="ue_id" required 
                            x-on:change="
                                let opt = $event.target.options[$event.target.selectedIndex];
                                selectedUePrefix = opt.getAttribute('data-code');
                                selectedLevelCode = opt.getAttribute('data-level-code');
                                generateAutoCode();
                            "
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl font-black text-xs focus:ring-2 focus:ring-amber-500 shadow-sm">
                            <option value="" disabled selected>— Filtrer par filière/niveau —</option>
                            @foreach($ues as $ue)
                                <option 
                                    x-show="(!filiereId || '{{ $ue->semestre->classe->filiere_id }}' == filiereId) && (!niveauId || '{{ $ue->semestre->classe->niveau_id }}' == niveauId)"
                                    value="{{ $ue->id }}" 
                                    data-code="{{ $ue->code }}" 
                                    data-level-code="{{ $ue->semestre->classe->niveau->code }}">
                                    [{{ $ue->semestre->classe->niveau->code }}] — {{ $ue->semestre->classe->filiere->nom }} — {{ $ue->code }} : {{ $ue->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">4. Nom de la matière</label>
                            <input type="text" name="libelle" x-model="currentMatiere.libelle" x-on:input="generateAutoCode()" required 
                                class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500 shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Code Système (Auto)</label>
                            <input type="text" name="code" x-model="currentMatiere.code" required readonly 
                                class="w-full px-5 py-4 bg-amber-50 border-none rounded-2xl font-black text-xs text-amber-700 uppercase italic cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-slate-900 p-6 rounded-[2.5rem] space-y-1 text-center shadow-2xl">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Coefficient</label>
                            <input type="number" name="coefficient" value="1" min="1" class="w-full text-center bg-transparent border-none text-4xl font-black text-white focus:ring-0">
                        </div>
                        <div class="bg-amber-500 p-6 rounded-[2.5rem] space-y-1 text-center shadow-2xl">
                            <label class="text-[8px] font-black text-amber-100 uppercase tracking-widest">ECTS</label>
                            <input type="number" name="credits" value="1" min="1" class="w-full text-center bg-transparent border-none text-4xl font-black text-white focus:ring-0">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close'); resetForm()" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase text-slate-400 hover:bg-slate-100 transition">Annuler</button>
                        <button type="submit" class="px-12 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE : ÉDITION --}}
        <x-modal name="edit-matiere" focusable>
            <div class="p-10">
                <h2 class="text-xl font-black text-slate-900 uppercase italic mb-8 tracking-tighter">Édition <span class="text-amber-500" x-text="currentMatiere.code"></span></h2>
                <form :action="'{{ route('admin.matieres.index') }}/' + currentMatiere.id" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nom de la matière</label>
                        <input type="text" name="libelle" x-model="currentMatiere.libelle" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="coefficient" x-model="currentMatiere.coefficient" class="px-5 py-4 bg-slate-50 border-none rounded-xl font-bold text-center">
                        <input type="number" name="credits" x-model="currentMatiere.credits" class="px-5 py-4 bg-slate-50 border-none rounded-xl font-bold text-center">
                    </div>
                    <input type="hidden" name="code" x-model="currentMatiere.code">
                    <input type="hidden" name="ue_id" x-model="currentMatiere.ue_id">
                    <button type="submit" class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg">Mettre à jour</button>
                </form>
            </div>
        </x-modal>

        {{-- MODALE : CONFIRMATION DE SUPPRESSION (LA NOUVELLE) --}}
        <x-modal name="confirm-delete-matiere" focusable>
            <div class="p-12 text-center">
                {{-- Icône Alerte --}}
                <div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 shadow-sm border border-rose-100">
                    <svg class="w-12 h-12 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Attention !</h2>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-8 leading-relaxed">
                    Vous êtes sur le point de supprimer définitivement la matière : <br>
                    <span class="text-rose-600 bg-rose-50 px-3 py-1 rounded-lg mt-2 inline-block font-black italic" x-text="currentMatiere.libelle"></span>
                </p>

                <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl mb-8">
                    <p class="text-[9px] font-black text-amber-700 uppercase tracking-tight italic">
                        ⚠️ Cette action est irréversible et supprimera également les liaisons avec les notes si elles existent.
                    </p>
                </div>

                <form :action="'{{ route('admin.matieres.index') }}/' + currentMatiere.id" method="POST" class="flex flex-col md:flex-row justify-center gap-4">
                    @csrf @method('DELETE')
                    
                    <button type="button" @click="$dispatch('close')" 
                        class="px-10 py-4 rounded-2xl font-black text-[10px] uppercase text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">
                        Finalement, non
                    </button>
                    
                    <button type="submit" 
                        class="px-12 py-4 bg-rose-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-rose-200 hover:bg-rose-700 transition-all">
                        Oui, supprimer
                    </button>
                </form>
            </div>
        </x-modal>

    </div>
</x-app-layout>