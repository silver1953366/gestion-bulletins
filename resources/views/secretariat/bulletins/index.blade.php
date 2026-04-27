{{-- resources/views/secretariat/bulletins/index.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Bulletins - Secrétariat</x-slot>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 italic">Bulletins de notes</h2>
            <p class="text-slate-500 text-sm mt-1">Génération des bulletins S5, S6 et annuel</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Formulaire de génération --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-800 mb-6">Générer un bulletin</h3>
            <form action="{{ route('secretariat.bulletins.generate') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2">Étudiant</label>
                        <select name="etudiant_id" class="w-full px-5 py-3 rounded-2xl border border-slate-200" required>
                            <option value="">Sélectionner</option>
                            @foreach($etudiants as $etudiant)
                                <option value="{{ $etudiant->id }}">{{ $etudiant->prenom }} {{ $etudiant->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2">Type de bulletin</label>
                        <select name="type" class="w-full px-5 py-3 rounded-2xl border border-slate-200" required>
                            <option value="S5">Semestre 5</option>
                            <option value="S6">Semestre 6</option>
                            <option value="ANNUEL">Annuel</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-2xl font-black uppercase text-sm hover:bg-teal-700">
                        <i class="fas fa-file-pdf mr-2"></i> Générer le bulletin
                    </button>
                </div>
            </form>
        </div>

        {{-- Liste des bulletins générés --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-teal-50 to-white border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">Bulletins générés</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($bulletins as $bulletin)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-bold">{{ $bulletin->etudiant->prenom }} {{ $bulletin->etudiant->nom }}</p>
                        <p class="text-xs text-slate-500">Bulletin {{ $bulletin->type }} - {{ $bulletin->created_at->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('secretariat.bulletins.download', $bulletin->id) }}" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 italic">
                    Aucun bulletin généré
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.secretariat>