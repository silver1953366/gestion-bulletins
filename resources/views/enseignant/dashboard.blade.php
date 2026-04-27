{{-- resources/views/enseignant/dashboard.blade.php --}}
<x-layouts.enseignant>
    <x-slot name="title">Tableau de Bord - Enseignant</x-slot>

    <div>
        {{-- En-tête --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div class="space-y-1">
                <h2 class="font-black text-4xl text-slate-900 tracking-tight flex items-center gap-3 italic">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-indigo-600">Tableau de Bord</span>
                    <span class="text-[10px] bg-slate-900 text-white py-1 px-3 rounded-full font-black uppercase tracking-tighter not-italic">Enseignant</span>
                </h2>
                <div class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse"></span>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">GESTION DES NOTES</p>
                </div>
            </div>
            
            <div class="group flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="h-11 w-11 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg transition-transform group-hover:rotate-6">
                    <i class="fas fa-chalkboard-teacher text-white"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Session Active</span>
                    <span class="text-sm font-black text-slate-800 italic uppercase">
                        {{ \App\Models\AnneeAcademique::where('active', true)->first()->libelle ?? '2024-2025' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Cartes de Statistiques Rapides --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-book-open text-xl"></i>
                    </div>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mes Matières</p>
                <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ $stats['total_matieres'] ?? 0 }}</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mes Étudiants</p>
                <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ $stats['total_etudiants'] ?? 0 }}</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <i class="fas fa-pen-ruler text-xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase">Taux</p>
                        <p class="text-xs font-black text-amber-600">{{ number_format($stats['taux_remplissage'] ?? 0, 1) }}%</p>
                    </div>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Notes Saisies</p>
                <div class="flex items-baseline gap-1">
                    <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ $stats['notes_saisies'] ?? 0 }}</p>
                    <span class="text-xs font-bold text-slate-400 italic">/ {{ $stats['notes_attendues'] ?? 0 }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl group-hover:bg-rose-600 group-hover:text-white transition-all">
                        <i class="fas fa-user-clock text-xl"></i>
                    </div>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Heures d'Absence</p>
                <div class="flex items-baseline gap-1">
                    <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ $stats['total_absences'] ?? 0 }}</p>
                    <span class="text-xs font-bold text-slate-400 italic">Hrs</span>
                </div>
            </div>
        </div>

        {{-- Section Principale --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Sélecteur de Matière --}}
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight italic uppercase">Mes Étudiants</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Sélectionnez une matière pour gérer les notes</p>
                        </div>
                        
                        <div class="relative">
                            <select id="matiere_select" class="appearance-none bg-slate-50 border-0 rounded-2xl px-6 py-3 pr-12 text-sm font-black text-slate-700 uppercase italic tracking-tight focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                <option value="">Choisir une matière</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere['id'] }}" {{ $selectedMatiereId == $matiere['id'] ? 'selected' : '' }}>
                                        {{ $matiere['libelle'] }} (Coeff {{ $matiere['coefficient'] }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tableau des Étudiants et Notes --}}
                @if($selectedMatiere && $etudiants->count() > 0)
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/50">
                                        <th class="text-left p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">N°</th>
                                        <th class="text-left p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Étudiant</th>
                                        <th class="text-left p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Matricule</th>
                                        <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">CC /20</th>
                                        <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Examen /20</th>
                                        <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Moyenne</th>
                                        <th class="text-center p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiants as $index => $etudiant)
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/30 transition" data-etudiant-id="{{ $etudiant['id'] }}">
                                        <td class="p-6 text-sm font-black text-slate-500">{{ $index + 1 }}</td>
                                        <td class="p-6">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                                    <span class="text-indigo-600 font-black text-sm">{{ substr($etudiant['prenom'] ?? '', 0, 1) }}{{ substr($etudiant['nom'] ?? '', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-slate-800">{{ $etudiant['prenom'] ?? '' }} {{ $etudiant['nom'] ?? '' }}</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $etudiant['classe'] ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-6 text-sm font-mono font-bold text-slate-500">{{ $etudiant['matricule'] ?? 'N/A' }}</td>
                                        <td class="p-6 text-center">
                                            <input type="number" step="0.01" min="0" max="20" 
                                                   value="{{ $etudiant['notes']['cc'] ?? '' }}"
                                                   data-etudiant="{{ $etudiant['id'] }}"
                                                   data-matiere="{{ $selectedMatiereId }}"
                                                   data-type="CC"
                                                   class="note-input w-20 px-3 py-2 text-center rounded-xl border border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 font-mono font-bold">
                                        </td>
                                        <td class="p-6 text-center">
                                            <input type="number" step="0.01" min="0" max="20" 
                                                   value="{{ $etudiant['notes']['examen'] ?? '' }}"
                                                   data-etudiant="{{ $etudiant['id'] }}"
                                                   data-matiere="{{ $selectedMatiereId }}"
                                                   data-type="EXAMEN"
                                                   class="note-input w-20 px-3 py-2 text-center rounded-xl border border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 font-mono font-bold">
                                        </td>
                                        <td class="p-6 text-center">
                                            <div class="moyenne-display inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black {{ ($etudiant['moyenne'] ?? 0) >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ number_format($etudiant['moyenne'] ?? 0, 2) }}
                                            </div>
                                        </td>
                                        <td class="p-6 text-center">
                                            <button class="save-note-btn px-4 py-2 bg-indigo-600 text-white text-[9px] font-black uppercase rounded-xl hover:bg-indigo-700 transition-all"
                                                    data-etudiant="{{ $etudiant['id'] }}"
                                                    data-matiere="{{ $selectedMatiereId }}">
                                                <i class="fas fa-save mr-1"></i> Sauvegarder
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Statistiques de la matière sélectionnée --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 text-white">
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-80">Moyenne Classe</p>
                            <p class="text-4xl font-black mt-2">{{ number_format($statsMatiere['moyenne_classe'] ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white">
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-80">Taux Réussite</p>
                            <p class="text-4xl font-black mt-2">{{ number_format($statsMatiere['taux_reussite'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-6 text-white">
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-80">Meilleure Note</p>
                            <p class="text-4xl font-black mt-2">{{ number_format($statsMatiere['meilleure_note'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                @elseif($selectedMatiere && $etudiants->count() == 0)
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-16 text-center">
                        <div class="inline-flex p-6 bg-slate-50 rounded-full mb-6">
                            <i class="fas fa-user-graduate text-slate-300 text-4xl"></i>
                        </div>
                        <p class="text-slate-400 font-black italic">Aucun étudiant inscrit pour cette matière.</p>
                    </div>
                @else
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-16 text-center">
                        <div class="inline-flex p-6 bg-indigo-50 rounded-full mb-6">
                            <i class="fas fa-hand-point-up text-indigo-400 text-4xl"></i>
                        </div>
                        <p class="text-slate-400 font-black italic">Sélectionnez une matière pour commencer la saisie des notes.</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar : Récapitulatif et Actions --}}
            <div class="lg:col-span-4 space-y-8">
                
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[3rem] p-8 text-white">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-3 bg-white/10 rounded-2xl backdrop-blur">
                            <i class="fas fa-chart-simple text-indigo-400"></i>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300 italic">Récapitulatif</h3>
                            <p class="text-[9px] text-white/40 font-bold mt-1">Vue d'ensemble</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @forelse($matieres as $matiere)
                        <div class="border-b border-white/10 pb-4 last:border-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-tight">{{ $matiere['libelle'] }}</p>
                                    <p class="text-[8px] text-indigo-300 font-black uppercase mt-1">Coeff {{ $matiere['coefficient'] }} • {{ $matiere['credits'] ?? 'N/A' }} crédits</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black italic {{ ($matiere['moyenne_classe'] ?? 0) >= 10 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ number_format($matiere['moyenne_classe'] ?? 0, 2) }}
                                    </p>
                                    <p class="text-[8px] text-white/40 uppercase tracking-wider">Moy. classe</p>
                                </div>
                            </div>
                            <div class="flex justify-between text-[9px] font-bold text-white/40 mt-2">
                                <span>✓ {{ $matiere['notes_saisies'] ?? 0 }}/{{ $matiere['total_etudiants'] ?? 0 }} notes</span>
                                <span>📊 Taux: {{ number_format($matiere['taux_remplissage'] ?? 0, 0) }}%</span>
                            </div>
                            <div class="h-1.5 bg-white/10 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-indigo-400 rounded-full transition-all" style="width: {{ $matiere['taux_remplissage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-chalkboard text-4xl text-white/10 mb-4"></i>
                            <p class="text-white/30 text-sm italic font-black">Aucune matière assignée</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-8 text-slate-400 italic">Actions Immédiates</h3>
                    <div class="space-y-4">
                        <button id="export_csv_btn" class="w-full flex items-center justify-between p-5 bg-slate-50 rounded-3xl hover:bg-slate-900 hover:text-white transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:bg-white/10 transition-colors">
                                    <i class="fas fa-file-excel text-xs text-emerald-600 group-hover:text-white"></i>
                                </div>
                                <span class="text-xs font-black uppercase italic tracking-tighter">Exporter les notes (CSV)</span>
                            </div>
                            <i class="fas fa-download text-[10px] opacity-20 group-hover:opacity-100 transition-opacity"></i>
                        </button>
                        
                        <a href="{{ route('enseignant.statistiques', ['matiere' => $selectedMatiereId ?? '']) }}" 
                           class="w-full flex items-center justify-between p-5 bg-slate-50 rounded-3xl hover:bg-slate-900 hover:text-white transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:bg-white/10 transition-colors">
                                    <i class="fas fa-chart-pie text-xs text-indigo-600 group-hover:text-white"></i>
                                </div>
                                <span class="text-xs font-black uppercase italic tracking-tighter">Statistiques détaillées</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-8 right-8 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl transform translate-y-24 opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-400"></i>
            <span class="text-sm font-black">Note sauvegardée avec succès</span>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const matiereSelect = document.getElementById('matiere_select');
        if (matiereSelect) {
            matiereSelect.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = '{{ route("enseignant.dashboard") }}?matiere=' + this.value;
                }
            });
        }

        const noteInputs = document.querySelectorAll('.note-input');
        const saveButtons = document.querySelectorAll('.save-note-btn');
        const toast = document.getElementById('toast-notification');

        function showToast(message = 'Note sauvegardée avec succès') {
            const toastSpan = toast.querySelector('span');
            if (toastSpan) toastSpan.textContent = message;
            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 2000);
        }

        async function saveNote(etudiantId, matiereId, type, note) {
            const formData = new FormData();
            formData.append('etudiant_id', etudiantId);
            formData.append('matiere_id', matiereId);
            formData.append('type', type);
            formData.append('note', note);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route("enseignant.notes.save") }}', {
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
                                moyenneDiv.className = `moyenne-display inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black ${data.nouvelle_moyenne >= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`;
                            }
                        }
                    }
                    return true;
                } else {
                    showToast('Erreur: ' + (data.message || 'Unknown error'));
                    return false;
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur de connexion');
                return false;
            }
        }

        saveButtons.forEach(btn => {
            btn.addEventListener('click', async function() {
                const etudiantId = this.dataset.etudiant;
                const matiereId = this.dataset.matiere;
                const row = this.closest('tr');
                const ccInput = row.querySelector('input[data-type="CC"]');
                const examenInput = row.querySelector('input[data-type="EXAMEN"]');
                
                if (ccInput && ccInput.value.trim() !== '') {
                    await saveNote(etudiantId, matiereId, 'CC', ccInput.value);
                }
                if (examenInput && examenInput.value.trim() !== '') {
                    await saveNote(etudiantId, matiereId, 'EXAMEN', examenInput.value);
                }
            });
        });

        noteInputs.forEach(input => {
            input.addEventListener('blur', async function() {
                const etudiantId = this.dataset.etudiant;
                const matiereId = this.dataset.matiere;
                const type = this.dataset.type;
                const note = this.value;
                if (note.trim() !== '') {
                    await saveNote(etudiantId, matiereId, type, note);
                }
            });
        });

        const exportBtn = document.getElementById('export_csv_btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                const matiereId = matiereSelect?.value;
                if (matiereId) {
                    window.location.href = '{{ route("enseignant.notes.export") }}?matiere=' + matiereId;
                } else {
                    showToast('Veuillez sélectionner une matière');
                }
            });
        }
    });
    </script>
    @endpush

    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 0.5;
        }
        input[type="number"]:hover::-webkit-inner-spin-button,
        input[type="number"]:hover::-webkit-outer-spin-button {
            opacity: 1;
        }
        .note-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
    </style>
</x-layouts.enseignant>