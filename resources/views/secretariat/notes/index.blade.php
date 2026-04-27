{{-- resources/views/secretariat/notes/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Gestion des notes - Secrétariat</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Gestion des notes</h2>
            <p class="text-slate-500 text-sm mt-1">Saisie des notes : CC, Examen, Rattrapage</p>
        </div>
        <button id="exportBtn" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-black uppercase text-sm hover:bg-emerald-700 transition shadow-lg flex items-center gap-2">
            <i class="fas fa-file-excel"></i> Exporter CSV
        </button>
    </div>

    {{-- Sélecteur de matière --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight italic uppercase">Sélectionner une matière</h3>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Saisie des notes par matière</p>
            </div>
            
            <div class="relative w-96">
                <select id="matiere_select" class="appearance-none bg-slate-50 border-0 rounded-2xl px-6 py-3 pr-12 text-sm font-black text-slate-700 uppercase italic tracking-tight focus:ring-2 focus:ring-teal-500 cursor-pointer w-full">
                    <option value="">Choisir une matière</option>
                    @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}" {{ $selectedMatiereId == $matiere->id ? 'selected' : '' }}>
                                            {{ $matiere->code }} - {{ $matiere->libelle }}
                                        </option>
                    @endforeach
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                    <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des notes --}}
    @if($selectedMatiere && $etudiants->count() > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">{{ $selectedMatiere->libelle }}</h3>
                        <p class="text-sm text-slate-500">Coefficient: {{ $selectedMatiere->coefficient }} | Crédits: {{ $selectedMatiere->credits }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="saveAllBtn" class="px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-black uppercase hover:bg-teal-700">
                            <i class="fas fa-save-all mr-1"></i> Sauvegarder tout
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">N°</th>
                            <th class="text-left p-5 text-xs font-black text-slate-400 uppercase">Étudiant</th>
                            <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">CC /20</th>
                            <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Examen /20</th>
                            <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Rattrapage /20</th>
                            <th class="text-center p-5 text-xs font-black text-slate-400 uppercase">Moyenne</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($etudiants as $index => $etudiant)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition" data-etudiant-id="{{ $etudiant['id'] }}">
                            <td class="p-5 text-sm font-black text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-teal-100 flex items-center justify-center">
                                        <span class="text-teal-600 font-black text-sm">{{ substr($etudiant['prenom'] ?? '', 0, 1) }}{{ substr($etudiant['nom'] ?? '', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800">{{ $etudiant['prenom'] }} {{ $etudiant['nom'] }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $etudiant['matricule'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       value="{{ $etudiant['notes']['cc'] ?? '' }}"
                                       data-etudiant="{{ $etudiant['id'] }}"
                                       data-type="CC"
                                       class="note-input w-24 px-3 py-2 text-center rounded-xl border border-slate-200 focus:border-teal-400 font-mono font-bold">
                            </td>
                            <td class="p-5 text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       value="{{ $etudiant['notes']['examen'] ?? '' }}"
                                       data-etudiant="{{ $etudiant['id'] }}"
                                       data-type="EXAMEN"
                                       class="note-input w-24 px-3 py-2 text-center rounded-xl border border-slate-200 focus:border-teal-400 font-mono font-bold">
                            </td>
                            <td class="p-5 text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       value="{{ $etudiant['notes']['rattrapage'] ?? '' }}"
                                       data-etudiant="{{ $etudiant['id'] }}"
                                       data-type="RATTRAPAGE"
                                       class="note-input w-24 px-3 py-2 text-center rounded-xl border border-amber-200 focus:border-amber-400 font-mono font-bold bg-amber-50">
                            </td>
                            <td class="p-5 text-center">
                                <div class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black moyenne-display 
                                    {{ ($etudiant['moyenne'] ?? 0) >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ number_format($etudiant['moyenne'] ?? 0, 2) }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Légende --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 mt-4">
            <div class="flex items-center gap-6 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-amber-100 rounded border border-amber-200"></div>
                    <span>Rattrapage (remplace la note finale)</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-info-circle text-slate-400"></i>
                    <span>Moyenne = CC (40%) + Examen (60%)</span>
                </div>
            </div>
        </div>
    @elseif($selectedMatiere && $etudiants->count() == 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-16 text-center">
            <i class="fas fa-users-slash text-6xl text-slate-300 mb-4"></i>
            <p class="text-slate-400 font-black italic">Aucun étudiant inscrit dans cette classe</p>
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-16 text-center">
            <i class="fas fa-hand-point-up text-6xl text-teal-300 mb-4"></i>
            <p class="text-slate-400 font-black italic">Sélectionnez une matière pour commencer la saisie des notes</p>
        </div>
    @endif

    {{-- Toast notification --}}
    <div id="toast" class="fixed bottom-8 right-8 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl transform translate-y-24 opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-400"></i>
            <span class="text-sm font-black">Note sauvegardée</span>
        </div>
    </div>
</x-layouts.secretariat>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const matiereSelect = document.getElementById('matiere_select');
    const noteInputs = document.querySelectorAll('.note-input');
    const toast = document.getElementById('toast');
    const exportBtn = document.getElementById('exportBtn');
    
    let saveTimeout = null;
    
    function showToast(message) {
        const span = toast.querySelector('span');
        if (span) span.textContent = message;
        toast.classList.remove('translate-y-24', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-24', 'opacity-0');
        }, 2000);
    }
    
    async function saveNote(etudiantId, type, note) {
        const matiereId = matiereSelect.value;
        if (!matiereId) return;
        
        const formData = new FormData();
        formData.append('etudiant_id', etudiantId);
        formData.append('matiere_id', matiereId);
        formData.append('type', type);
        formData.append('note', note);
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            const response = await fetch('{{ route("secretariat.notes.save") }}', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message);
                if (data.nouvelle_moyenne !== undefined) {
                    const row = document.querySelector(`tr[data-etudiant-id="${etudiantId}"]`);
                    if (row) {
                        const moyenneDiv = row.querySelector('.moyenne-display');
                        if (moyenneDiv) {
                            moyenneDiv.textContent = data.nouvelle_moyenne.toFixed(2);
                            const newClass = data.nouvelle_moyenne >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
                            moyenneDiv.className = `inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black moyenne-display ${newClass}`;
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur de connexion');
        }
    }
    
    // Sauvegarde automatique au blur
    noteInputs.forEach(input => {
        input.addEventListener('blur', async function() {
            const etudiantId = this.dataset.etudiant;
            const type = this.dataset.type;
            const note = this.value;
            if (note !== '') {
                await saveNote(etudiantId, type, note);
            }
        });
    });
    
    // Changement de matière
    if (matiereSelect) {
        matiereSelect.addEventListener('change', function() {
            if (this.value) {
                window.location.href = '{{ route("secretariat.notes.index") }}?matiere=' + this.value;
            }
        });
    }
    
    // Export CSV
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const matiereId = matiereSelect?.value;
            if (matiereId) {
                window.location.href = '{{ route("secretariat.notes.export") }}?matiere=' + matiereId;
            } else {
                showToast('Veuillez sélectionner une matière');
            }
        });
    }
});
</script>
@endpush