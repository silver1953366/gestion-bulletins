{{-- resources/views/secretariat/etudiants/show.blade.php --}}
<x-layouts.secretariat>
    <x-slot name="title">Détails étudiant</x-slot>

    <div class="mb-8">
        <a href="{{ route('secretariat.etudiants.index') }}" class="text-teal-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center">
                        <span class="text-white text-2xl font-black">{{ substr($etudiant->prenom ?? '', 0, 1) }}{{ substr($etudiant->nom ?? '', 0, 1) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">{{ $etudiant->prenom }} {{ $etudiant->nom }}</h2>
                        <p class="text-slate-500">ID: #{{ $etudiant->id }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase">Date de naissance</p>
                        <p class="text-sm font-bold text-slate-700">{{ $etudiant->date_naissance ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase">Lieu de naissance</p>
                        <p class="text-sm font-bold text-slate-700">{{ $etudiant->lieu_naissance ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase">Baccalauréat</p>
                        <p class="text-sm font-bold text-slate-700">{{ $etudiant->bac ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase">Provenance</p>
                        <p class="text-sm font-bold text-slate-700">{{ $etudiant->provenance ?? 'Non renseigné' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-gradient-to-br from-teal-600 to-teal-800 rounded-3xl p-6 text-white">
                <h3 class="text-xs font-black uppercase tracking-wider mb-4">Inscriptions</h3>
                @foreach($etudiant->inscriptions as $inscription)
                    <div class="bg-white/10 rounded-xl p-4 mb-3">
                        <p class="font-bold">{{ $inscription->classe->nom ?? 'N/A' }}</p>
                        <p class="text-xs opacity-80">{{ $inscription->anneeAcademique->libelle ?? 'Année inconnue' }}</p>
                        <p class="text-xs opacity-80">Statut: {{ $inscription->statut }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.secretariat>