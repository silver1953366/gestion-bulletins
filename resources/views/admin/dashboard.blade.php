<x-layouts.master>
    <x-slot name="title">Tableau de Bord - INPTIC</x-slot>

    {{-- En-tête du Dashboard --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div class="space-y-1">
            <h2 class="font-black text-4xl text-slate-900 tracking-tight flex items-center gap-3 italic">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-indigo-600">Tableau de Bord</span>
                <span class="text-[10px] bg-slate-900 text-white py-1 px-3 rounded-full font-black uppercase tracking-tighter not-italic">v2.1</span>
            </h2>
            <div class="flex items-center gap-3">
                <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse"></span>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">ERP LP ASUR • INPTIC LIBREVILLE</p>
            </div>
        </div>
        
        <div class="group flex items-center gap-4 bg-white p-2 pr-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="h-11 w-11 rounded-xl bg-slate-900 flex items-center justify-center shadow-lg transition-transform group-hover:rotate-6">
                <i class="fas fa-calendar-check text-white"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Session Active</span>
                <span class="text-sm font-black text-slate-800 italic uppercase">
                    {{ \App\Models\AnneeAcademique::where('active', true)->first()->libelle ?? 'Session Close' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Cartes de Statistiques Rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Effectif Global --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full italic">{{ $stats['total_filieres'] ?? 0 }} Filières</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Effectif Global</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ number_format($stats['total_etudiants'] ?? 0) }}</p>
        </div>

        {{-- Moyenne Générale --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition-all">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Top Moyenne</p>
                    <p class="text-xs font-black text-amber-600">{{ $academicStats['meilleure_moyenne'] ?? 0 }}</p>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Moyenne Générale</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ number_format($academicStats['moyenne_generale'] ?? 0, 2) }}</p>
        </div>

        {{-- Taux de Réussite --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fas fa-award text-xl"></i>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase">Diplômés</p>
                    <p class="text-xs font-black text-emerald-600">{{ $studentsByStatus['diplomes'] ?? 0 }}</p>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Taux de Réussite</p>
            <p class="text-4xl font-black text-slate-900 leading-none mt-1">{{ $academicStats['taux_reussite'] ?? 0 }}%</p>
        </div>

        {{-- Heures d'Absence --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group">
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

    {{-- Section Principale : Alertes et Statistiques Semestrielles --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 space-y-8">
            {{-- Bloc Alertes --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight italic uppercase">Anomalies & Vigilance</h3>
                        <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Flux d'événements en temps réel</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-rose-600 bg-rose-50 px-4 py-1.5 rounded-full uppercase italic border border-rose-100">
                             {{ count($alerts ?? []) }} Alertes Critiques
                        </span>
                    </div>
                </div>

                <div class="p-8 space-y-4">
                    @forelse($alerts ?? [] as $alert)
                    <div class="group flex items-start p-6 rounded-3xl bg-white border border-slate-100 hover:border-rose-100 hover:shadow-xl hover:shadow-rose-50/50 transition-all duration-300">
                        <div class="p-4 bg-slate-50 rounded-2xl mr-6 group-hover:bg-rose-500 group-hover:text-white transition-all">
                            <i class="{{ $alert['icon'] }} text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-black text-slate-800 uppercase italic tracking-tight">Incident Détecté</h4>
                                <span class="text-[9px] font-bold text-slate-300 uppercase italic">Priorité Haute</span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed font-bold">{{ $alert['message'] }}</p>
                            <div class="mt-5 flex gap-3">
                                <button class="px-5 py-2 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition shadow-lg shadow-slate-200">RÉSOUDRE</button>
                                <button class="px-5 py-2 bg-white text-slate-400 border border-slate-100 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 transition">IGNORER</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-16">
                        <div class="inline-flex p-8 bg-emerald-50 rounded-full mb-6">
                            <i class="fas fa-shield-virus text-emerald-500 text-4xl"></i>
                        </div>
                        <p class="text-slate-400 font-black italic tracking-widest uppercase text-sm">Système intègre. Aucune anomalie critique.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            {{-- Moyennes Semestrielles --}}
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white flex items-center justify-between">
                <div>
                    <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] mb-2">Moyennes Semestrielles</h4>
                    <div class="flex gap-10">
                        <div>
                            <p class="text-xs font-bold text-slate-400 italic">Semestre 5</p>
                            <p class="text-3xl font-black italic">{{ $academicStats['moyenne_s5'] ?? '0.00' }}</p>
                        </div>
                        <div class="w-[1px] bg-slate-800"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 italic">Semestre 6</p>
                            <p class="text-3xl font-black italic text-indigo-400">{{ $academicStats['moyenne_s6'] ?? '0.00' }}</p>
                        </div>
                    </div>
                </div>
                <i class="fas fa-chart-bar text-5xl text-white/5"></i>
            </div>
        </div>

        {{-- Sidebar du Dashboard : Saisie des Notes & Actions --}}
        <div class="lg:col-span-4 space-y-8">
            {{-- Widget de Progression des Notes --}}
            <div class="relative bg-indigo-600 rounded-[3rem] p-10 shadow-2xl shadow-indigo-200 overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                
                <div class="relative z-10 text-white">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="p-3 bg-white/20 backdrop-blur-xl rounded-2xl">
                             <i class="fas fa-keyboard text-white"></i>
                        </span>
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] italic text-indigo-100">Saisie des Notes</h3>
                    </div>

                    <div class="flex items-end gap-3 mb-6">
                        <span class="text-7xl font-black leading-none tracking-tighter italic">{{ $stats['taux_remplissage_notes'] ?? 0 }}%</span>
                    </div>

                    <div class="h-4 w-full bg-white/20 rounded-full mb-10 overflow-hidden backdrop-blur-sm p-1">
                        <div class="h-full bg-white rounded-full transition-all duration-1000 ease-out shadow-[0_0_20px_rgba(255,255,255,0.8)]" 
                             style="width: {{ $stats['taux_remplissage_notes'] ?? 0 }}%"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/10">
                            <p class="text-[10px] font-black text-indigo-100 uppercase mb-2 italic">Attente Soutenance</p>
                            <div class="flex justify-between items-end">
                                <p class="text-3xl font-black leading-none italic">{{ $soutenanceStats['en_attente_soutenance'] ?? 0 }}</p>
                                <span class="text-[10px] font-black uppercase opacity-60">Étudiants</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bloc Actions Rapides --}}
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-8 text-slate-400 italic">Actions Immédiates</h3>
                <div class="space-y-4">
                    <a href="{{ route('admin.etudiants.index') }}" class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl hover:bg-slate-900 hover:text-white transition-all group border border-transparent">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:bg-white/10 transition-colors">
                                <i class="fas fa-plus text-xs text-indigo-600 group-hover:text-white"></i>
                            </div>
                            <span class="text-xs font-black uppercase italic tracking-tighter">Nouvelle Inscription</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </a>
                    
                    <a href="{{ route('admin.bulletins.index') }}" class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl hover:bg-slate-900 hover:text-white transition-all group border border-transparent">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:bg-white/10 transition-colors">
                                <i class="fas fa-file-pdf text-xs text-rose-600 group-hover:text-white"></i>
                            </div>
                            <span class="text-xs font-black uppercase italic tracking-tighter">Édition Bulletins</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] opacity-20"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.master>