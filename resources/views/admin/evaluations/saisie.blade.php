<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-widest">
                    <li class="text-slate-400">Administration</li>
                    <li>
                        <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                        </svg>
                    </li>
                    <li class="text-amber-600 italic">Notes & Évaluations</li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                Saisie des <span class="text-amber-500">Notes</span>
            </h1>
        </div>
    </x-slot>

    <div class="max-w-4xl animate-fade-in">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-edit text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase italic leading-none">Configuration de la session</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">Sélectionnez les paramètres pour ouvrir le bordereau de notes.</p>
                    </div>
                </div>

                <form action="{{ route('admin.evaluations.saisie') }}" method="GET" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">
                                Classe / Niveau
                            </label>
                            <div class="relative group">
                                <select name="classe_id" required 
                                    class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent focus:border-amber-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-xs text-slate-700 transition-all appearance-none cursor-pointer">
                                    <option value="">Choisir une classe...</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-slate-300">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic block">
                                Matière (UE)
                            </label>
                            <div class="relative group">
                                <select name="matiere_id" required 
                                    class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent focus:border-amber-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-xs text-slate-700 transition-all appearance-none cursor-pointer">
                                    <option value="">Choisir la matière...</option>
                                    @foreach($matieres as $matiere)
                                        <option value="{{ $matiere->id }}">{{ $matiere->libelle }} ({{ $matiere->code }})</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-slate-300">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center gap-2 text-amber-600 bg-amber-50 px-4 py-2 rounded-xl">
                            <i class="fas fa-info-circle text-xs"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Vérifiez les attributions avant la saisie</span>
                        </div>

                        <button type="submit" 
                            class="inline-flex items-center gap-4 bg-slate-900 text-white px-10 py-5 rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-200 group">
                            <span>Accéder au bordereau</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Rappel des Coefficients</h4>
                <p class="text-[11px] text-slate-400 font-medium italic leading-relaxed">
                    Le système applique automatiquement les crédits ECTS définis dans la section "Matières & Crédits". Assurez-vous que l'année académique en cours est bien activée.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>