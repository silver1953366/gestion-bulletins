{{-- resources/views/secretariat/dashboard.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Tableau de Bord - Secrétariat Pédagogique</x-slot>

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="space-y-1">
            <h2 class="font-black text-4xl text-slate-900 tracking-tight flex items-center gap-3 italic">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-teal-600">Tableau de Bord</span>
                <span class="text-[10px] bg-slate-900 text-white py-1 px-3 rounded-full font-black uppercase tracking-tighter not-italic">Secrétariat</span>
            </h2>
            <div class="flex items-center gap-3">
                <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse"></span>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">GESTION PÉDAGOGIQUE</p>
            </div>
        </div>
        
        <div class="group flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="h-11 w-11 rounded-xl bg-teal-600 flex items-center justify-center shadow-lg transition-transform group-hover:rotate-6">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Année Académique</span>
                <span class="text-sm font-black text-slate-800 italic uppercase">
                    {{\App\Models\AnneeAcademique::where('active', true)->first()->libelle ?? '2024-2025'}}
                </span>
            </div>
        </div>
    </div>

    {{-- Cartes Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-teal-50 text-teal-600 rounded-2xl group-hover:bg-teal-600 group-hover:text-white transition-all">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Étudiants</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ number_format($stats['total_etudiants'] ?? 0) }}</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Taux Réussite</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ number_format($stats['taux_reussite'] ?? 0, 1) }}%</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Taux</p>
                    <p class="text-xs font-black text-amber-600">{{ number_format($stats['taux_saisie_notes'] ?? 0, 1) }}%</p>
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
                    <i class="fas fa-chart-simple text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Moyenne Générale</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ number_format($stats['moyenne_generale'] ?? 0, 2) }}</p>
        </div>
    </div>

    {{-- Section Principale --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Colonne gauche : Alertes & Dernières inscriptions --}}
        <div class="lg:col-span-7 space-y-8">
            
            {{-- Alertes --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight italic uppercase">Alertes & Vigilance</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Suivi des anomalies</p>
                    </div>
                    <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-4 py-1.5 rounded-full uppercase">
                        {{ count($alerts ?? []) }} Alerte(s)
                    </span>
                </div>
                <div class="p-8 space-y-4">
                    @forelse($alerts ?? [] as $alert)
                    <div class="flex items-start p-5 rounded-2xl bg-{{ $alert['color'] ?? 'amber' }}-50 border border-{{ $alert['color'] ?? 'amber' }}-100">
                        <div class="p-2 bg-{{ $alert['color'] ?? 'amber' }}-100 rounded-xl mr-4">
                            <i class="fas fa-{{ $alert['icon'] ?? 'exclamation-triangle' }} text-{{ $alert['color'] ?? 'amber' }}-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-700">{{ $alert['message'] }}</p>
                        </div>
                        <a href="{{ $alert['link'] ?? '#' }}" class="text-[9px] font-black text-{{ $alert['color'] ?? 'amber' }}-600 hover:underline">Voir</a>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <i class="fas fa-check-circle text-emerald-500 text-4xl mb-4"></i>
                        <p class="text-slate-400 font-black">Aucune alerte critique</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Dernières inscriptions --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-teal-50 to-white">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-plus text-teal-600"></i>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Dernières inscriptions</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50">
                                <th class="text-left p-4 text-[9px] font-black text-slate-400 uppercase">Étudiant</th>
                                <th class="text-left p-4 text-[9px] font-black text-slate-400 uppercase">Classe</th>
                                <th class="text-left p-4 text-[9px] font-black text-slate-400 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInscriptions ?? [] as $inscription)
                            <tr class="border-b border-slate-50">
                                <td class="p-4 text-sm font-bold text-slate-700">{{ $inscription['etudiant'] }}</td>
                                <td class="p-4 text-xs text-slate-500">{{ $inscription['classe'] }}</td>
                                <td class="p-4 text-xs text-slate-400">{{ $inscription['date'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-slate-400 italic">Aucune inscription récente</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50/30">
                    <a href="{{ route('secretariat.etudiants.index') }}" class="text-[9px] font-black text-teal-600 uppercase tracking-wider hover:underline flex items-center gap-2">
                        Voir tous <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Sidebar droite : Actions Rapides --}}
        <div class="lg:col-span-5 space-y-8">
            
            {{-- Widget Saisie Rapide Notes --}}
            <div class="bg-gradient-to-br from-teal-600 to-teal-800 rounded-[2rem] p-8 text-white">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-pen-ruler text-2xl opacity-80"></i>
                    <h3 class="text-[10px] font-black uppercase tracking-wider">Saisie des notes</h3>
                </div>
                <div class="space-y-4">
                    <div class="bg-white/10 rounded-2xl p-4">
                        <select id="quick_matiere" class="w-full bg-white/20 border-0 rounded-xl text-white text-sm font-black py-3 px-4">
                            <option value="">Sélectionner une matière</option>
                            @foreach($matieres ?? [] as $matiere)
                                <option value="{{ $matiere['id'] }}" class="text-slate-900">{{ $matiere['libelle'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button onclick="window.location.href='{{ route('secretariat.notes.index') }}' + '?matiere=' + document.getElementById('quick_matiere').value" 
                            class="w-full bg-white text-teal-700 py-3 rounded-xl font-black uppercase text-xs hover:bg-teal-50 transition">
                        <i class="fas fa-arrow-right mr-2"></i> Accéder à la saisie
                    </button>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-6">Actions rapides</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('secretariat.etudiants.create') }}" class="p-4 bg-slate-50 rounded-2xl text-center hover:bg-teal-50 transition group">
                        <i class="fas fa-plus-circle text-teal-600 text-xl mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[9px] font-black uppercase">Nouvel étudiant</span>
                    </a>
                    <a href="{{ route('secretariat.absences.create') }}" class="p-4 bg-slate-50 rounded-2xl text-center hover:bg-teal-50 transition group">
                        <i class="fas fa-user-clock text-amber-600 text-xl mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[9px] font-black uppercase">Saisir absence</span>
                    </a>
                    <a href="{{ route('secretariat.bulletins.index') }}" class="p-4 bg-slate-50 rounded-2xl text-center hover:bg-teal-50 transition group">
                        <i class="fas fa-file-pdf text-rose-600 text-xl mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[9px] font-black uppercase">Générer bulletin</span>
                    </a>
                    <a href="{{ route('secretariat.resultats.index') }}" class="p-4 bg-slate-50 rounded-2xl text-center hover:bg-teal-50 transition group">
                        <i class="fas fa-chart-line text-indigo-600 text-xl mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[9px] font-black uppercase">Décisions jury</span>
                    </a>
                </div>
            </div>

            {{-- Stats rapides décisions jury --}}
            <div class="bg-slate-900 rounded-[2rem] p-6 text-white">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-gavel text-amber-400"></i>
                    <h3 class="text-[10px] font-black uppercase tracking-wider">Décisions du Jury</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-300">Admis</span>
                        <span class="text-xl font-black text-emerald-400">{{ $juryStats['admis'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-300">Redoublants</span>
                        <span class="text-xl font-black text-amber-400">{{ $juryStats['redoublants'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-300">Exclus</span>
                        <span class="text-xl font-black text-rose-400">{{ $juryStats['exclus'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.secretariat>