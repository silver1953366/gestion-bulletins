{{-- resources/views/secretariat/bulletins/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Bulletins - Secrétariat Pédagogique</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Bulletins de notes</h2>
            <p class="text-slate-500 text-sm mt-1">Génération des bulletins S5, S6 et annuel</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Formulaire de génération --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-teal-50 rounded-2xl">
                    <i class="fas fa-file-pdf text-teal-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800">Générer un bulletin</h3>
            </div>
            
            <form action="{{ route('secretariat.bulletins.generate') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Étudiant *</label>
                        <select name="etudiant_id" class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100" required>
                            <option value="">Sélectionner un étudiant</option>
                            @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}">{{ $etudiant->prenom }} {{ $etudiant->nom }} (ID: {{ $etudiant->id }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Type de bulletin *</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="S5" class="peer sr-only" required>
                                <div class="p-4 text-center rounded-2xl border-2 border-slate-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:bg-slate-50 transition">
                                    <i class="fas fa-book-open text-slate-400 peer-checked:text-teal-600 text-xl mb-2 block"></i>
                                    <span class="text-sm font-black peer-checked:text-teal-600">Semestre 5</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="S6" class="peer sr-only" required>
                                <div class="p-4 text-center rounded-2xl border-2 border-slate-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:bg-slate-50 transition">
                                    <i class="fas fa-book-open text-slate-400 peer-checked:text-teal-600 text-xl mb-2 block"></i>
                                    <span class="text-sm font-black peer-checked:text-teal-600">Semestre 6</span>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="ANNUEL" class="peer sr-only" required>
                                <div class="p-4 text-center rounded-2xl border-2 border-slate-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:bg-slate-50 transition">
                                    <i class="fas fa-calendar-alt text-slate-400 peer-checked:text-teal-600 text-xl mb-2 block"></i>
                                    <span class="text-sm font-black peer-checked:text-teal-600">Annuel</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-teal-600 text-white py-4 rounded-2xl font-black uppercase text-sm hover:bg-teal-700 transition shadow-lg flex items-center justify-center gap-2 mt-6">
                        <i class="fas fa-file-pdf"></i> Générer le bulletin
                    </button>
                </div>
            </form>
        </div>

        {{-- Liste des bulletins générés --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-history text-teal-600"></i>
                    <h3 class="text-xl font-black text-slate-800">Bulletins générés</h3>
                </div>
                <p class="text-xs text-slate-400 mt-1">Historique des bulletins créés</p>
            </div>
            
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($bulletins as $bulletin)
                <div class="p-5 hover:bg-slate-50 transition group">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-emerald-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-800">
                                        {{ $bulletin->etudiant->prenom ?? '' }} {{ $bulletin->etudiant->nom ?? '' }}
                                    </p>
                                    <p class="text-xs text-slate-400">ID: {{ $bulletin->etudiant_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 ml-14">
                                <span class="px-2 py-1 bg-teal-100 text-teal-700 rounded-lg text-xs font-black">
                                    <i class="fas fa-tag mr-1"></i> {{ $bulletin->type }}
                                </span>
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-xs font-black">
                                    <i class="fas fa-calendar mr-1"></i> {{ $bulletin->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('secretariat.bulletins.download', $bulletin->id) }}" 
                               class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-xs font-black hover:bg-emerald-600 transition flex items-center gap-1 opacity-0 group-hover:opacity-100">
                                <i class="fas fa-download"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <div class="inline-flex p-6 bg-slate-50 rounded-full mb-4">
                        <i class="fas fa-file-pdf text-slate-300 text-4xl"></i>
                    </div>
                    <p class="text-slate-400 font-black italic">Aucun bulletin généré</p>
                    <p class="text-xs text-slate-300 mt-1">Utilisez le formulaire pour créer un bulletin</p>
                </div>
                @endforelse
            </div>
            
            @if($bulletins->count() > 0)
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">{{ $bulletins->total() }} bulletin(s) au total</span>
                    {{ $bulletins->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Section d'export massif --}}
    <div class="mt-8 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-amber-50 rounded-2xl">
                <i class="fas fa-download text-amber-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800">Export massif</h3>
                <p class="text-xs text-slate-400">Générer les bulletins pour toute une classe</p>
            </div>
        </div>
        
        <form action="{{ route('secretariat.bulletins.export-pdf') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                    <option value="">Sélectionner une classe</option>
                    @php
                        $classes = \App\Models\Classe::all();
                    @endphp
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Type de bulletin</label>
                <select name="type" class="w-full px-4 py-3 rounded-2xl border border-slate-200">
                    <option value="S5">Semestre 5</option>
                    <option value="S6">Semestre 6</option>
                    <option value="ANNUEL">Annuel</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-2xl font-black uppercase text-sm hover:bg-amber-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-file-export"></i> Exporter tous les PDF
                </button>
            </div>
        </form>
    </div>
</x-layouts.secretariat>