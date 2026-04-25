<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Scolarité</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-rose-600 italic uppercase tracking-tighter">Registre des Absences</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Gestion des <span class="text-rose-600">Absences</span></h1>
            </div>

            {{-- Déclencheur de la modale d'ajout via dispatch --}}
            <button @click="$dispatch('open-modal', 'add-absence')" class="bg-slate-900 text-white px-8 py-4 rounded-3xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                Signaler une absence
            </button>
        </div>
    </x-slot>

    {{-- Scope Alpine.js identique à celui des Matières --}}
    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentAbsence: {}, 
        editAction: '',
        openEdit(absence) {
            this.currentAbsence = absence;
            this.editAction = '/admin/absences/' + absence.id;
            $dispatch('open-modal', 'edit-absence');
        }
    }">
        
        {{-- Tableau des records --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden text-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 bg-slate-50/30">
                        <th class="px-10 py-6">Étudiant</th>
                        <th class="px-10 py-6">Matière</th>
                        <th class="px-10 py-6 text-center">Volume</th>
                        <th class="px-10 py-6 text-center">Status</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($absences as $absence)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <span class="block font-black text-slate-800 uppercase italic tracking-tighter">{{ $absence->etudiant->nom }} {{ $absence->etudiant->prenom }}</span>
                        </td>
                        <td class="px-10 py-5 font-bold text-slate-500 uppercase text-[10px] italic">
                            {{ $absence->matiere->libelle ?? 'N/A' }}
                        </td>
                        <td class="px-10 py-5 text-center">
                            <span class="bg-rose-50 text-rose-600 px-3 py-1 rounded-full font-black text-[10px] uppercase border border-rose-100">
                                {{ $absence->heures }} H
                            </span>
                        </td>
                        <td class="px-10 py-5 text-center">
                            @if($absence->justification)
                                <span class="text-[9px] font-black uppercase text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100 italic">Justifiée</span>
                            @else
                                <span class="text-[9px] font-black uppercase text-slate-300 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100 italic">Non justifiée</span>
                            @endif
                        </td>
                        <td class="px-10 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Appel de la fonction openEdit avec JSON --}}
                                <button @click="openEdit({{ $absence->toJson() }})" class="p-2 text-slate-300 hover:text-rose-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                                </button>
                                
                                <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" onsubmit="return confirm('Supprimer ce record ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-300 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <p class="opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400">Registre vide</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($absences->hasPages())
            <div class="px-2">{{ $absences->links() }}</div>
        @endif

        {{-- MODALE AJOUT : Nommée 'add-absence' --}}
        <x-modal name="add-absence" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Signaler une <span class="text-rose-600 italic">Absence</span></h2>
                
                <form action="{{ route('admin.absences.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Étudiant</label>
                        <select name="etudiant_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>Choisir un étudiant...</option>
                            @foreach($etudiants as $e)
                                <option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Matière</label>
                        <select name="matiere_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase italic focus:ring-2 focus:ring-rose-500">
                            <option value="" disabled selected>Matière concernée...</option>
                            @foreach($matieres as $m)
                                <option value="{{ $m->id }}">{{ $m->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Heures</label>
                            <input type="number" name="heures" value="2" min="1" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-rose-500">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Justification (Optionnel)</label>
                        <textarea name="justification" rows="2" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs italic focus:ring-2 focus:ring-rose-500" placeholder="Ex: Certificat médical..."></textarea>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-100 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-600 transition-all shadow-lg">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE MODIFICATION : Nommée 'edit-absence' --}}
        <x-modal name="edit-absence" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Rectifier <span class="text-rose-600 italic">l'absence</span></h2>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Étudiant</label>
                        <select name="etudiant_id" x-model="currentAbsence.etudiant_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase">
                            @foreach($etudiants as $e)
                                <option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Matière</label>
                        <select name="matiere_id" x-model="currentAbsence.matiere_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase italic">
                            @foreach($matieres as $m)
                                <option value="{{ $m->id }}">{{ $m->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Heures</label>
                            <input type="number" name="heures" x-model="currentAbsence.heures" min="1" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Justification</label>
                        <textarea name="justification" x-model="currentAbsence.justification" rows="2" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs italic focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-100 transition-all">Fermer</button>
                        <button type="submit" class="px-8 py-4 bg-rose-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>