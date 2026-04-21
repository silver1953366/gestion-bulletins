<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentAbsence: { id: '', etudiant_id: '', matiere_id: '', heures: 0, justification: '' },
        editRoute: '',
        openEditModal(absence) {
            this.currentAbsence = absence;
            this.editRoute = `/admin/absences/${absence.id}`;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Registre des Absences</h1>
                    <p class="text-slate-500 text-sm font-bold italic mt-1">Suivi des pénalités et justifications — INPTIC</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-8 py-4 bg-rose-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-rose-700 transition shadow-xl shadow-rose-100 gap-3 group">
                    <svg class="w-5 h-5 transform group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    Signaler une Absence
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-8 py-6">Étudiant</th>
                        <th class="px-8 py-6">Matière</th>
                        <th class="px-8 py-6 text-center">Volume</th>
                        <th class="px-8 py-6">Justification</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($absences as $absence)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900 uppercase tracking-tighter">{{ $absence->etudiant->nom }}</p>
                            <p class="text-xs font-bold text-indigo-500 italic">{{ $absence->etudiant->prenom }}</p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-slate-600 uppercase">{{ $absence->matiere->nom ?? 'N/A' }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-50 text-rose-600 font-black text-[10px] uppercase">
                                {{ $absence->heures }} Heures
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            @if($absence->justification)
                                <span class="group/note relative cursor-help">
                                    <span class="text-[9px] font-black uppercase text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">Justifiée</span>
                                    <div class="absolute bottom-full left-0 mb-2 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-xl opacity-0 group-hover/note:opacity-100 transition pointer-events-none z-10">
                                        {{ $absence->justification }}
                                    </div>
                                </span>
                            @else
                                <span class="text-[9px] font-black uppercase text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">Non Justifiée</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button @click="openEditModal({{ $absence->toJson() }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/></svg>
                                </button>
                                <form action="{{ route('admin.absences.destroy', $absence) }}" method="POST" onsubmit="return confirm('Supprimer ce record ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-bold">Aucune absence enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                {{ $absences->links() }}
            </div>
        </div>

        <div x-show="showCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 animate-scale-in">
                <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-2xl font-black text-slate-900 italic uppercase tracking-tighter">Signaler une absence</h2>
                    <button @click="showCreateModal = false" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-slate-400 hover:text-rose-600 transition">✕</button>
                </div>
                <form action="{{ route('admin.absences.store') }}" method="POST" class="p-10 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Étudiant concerné</label>
                        <select name="etudiant_id" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-rose-500 rounded-2xl font-bold text-sm">
                            <option value="">Sélectionner l'étudiant</option>
                            @foreach($etudiants as $e)
                                <option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Matière / Cours</label>
                        <select name="matiere_id" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-rose-500 rounded-2xl font-bold text-sm">
                            <option value="">Sélectionner la matière</option>
                            @foreach($matieres as $m)
                                <option value="{{ $m->id }}">{{ $m->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Heures</label>
                            <input type="number" name="heures" value="1" min="1" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-rose-500 rounded-2xl font-bold text-sm">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Justification (Motif)</label>
                        <textarea name="justification" rows="2" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-rose-500 rounded-2xl font-bold text-sm" placeholder="Ex: Certificat médical..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-5 bg-rose-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition shadow-xl shadow-rose-100 mt-4">
                        Enregistrer l'absence
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>
            <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden relative z-10 animate-scale-in border-2 border-indigo-500/20">
                <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-indigo-50/30">
                    <h2 class="text-2xl font-black text-indigo-900 italic uppercase tracking-tighter">Modifier l'enregistrement</h2>
                    <button @click="showEditModal = false" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-slate-400 hover:text-indigo-600 transition">✕</button>
                </div>
                <form :action="editRoute" method="POST" class="p-10 space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Étudiant</label>
                        <select name="etudiant_id" x-model="currentAbsence.etudiant_id" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                            @foreach($etudiants as $e)
                                <option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Matière</label>
                        <select name="matiere_id" x-model="currentAbsence.matiere_id" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                            @foreach($matieres as $m)
                                <option value="{{ $m->id }}">{{ $m->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Volume Horaire</label>
                        <input type="number" name="heures" x-model="currentAbsence.heures" min="1" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Justification</label>
                        <textarea name="justification" x-model="currentAbsence.justification" rows="2" class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition shadow-xl shadow-indigo-100 mt-4">
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>