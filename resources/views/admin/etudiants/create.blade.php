<<<<<<< HEAD
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter étudiant</title>
</head>
<body>

<h2>Ajouter un étudiant</h2>

<form action="{{ route('admin.etudiants.store') }}" method="POST">
    @csrf

    <input type="text" name="nom" placeholder="Nom"><br>
    <input type="text" name="prenom" placeholder="Prénom"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Mot de passe"><br>

    <button type="submit">Enregistrer</button>
</form>

</body>
</html>
=======
 @extends('admin.layouts.master')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in pb-12">
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.etudiants.index') }}" class="group inline-flex items-center gap-2 text-indigo-600 font-black text-xs uppercase tracking-widest mb-3 hover:text-indigo-800 transition">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter italic uppercase">Nouvelle Inscription</h1>
            <p class="text-slate-500 text-sm font-bold italic mt-1">Enregistrement d'un nouvel étudiant dans la base de données</p>
        </div>

        <div class="hidden md:block">
            <div class="w-16 h-16 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <form action="{{ route('admin.etudiants.store') }}" method="POST" class="p-8 md:p-12 space-y-10">
            @csrf

            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                    </div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-tighter italic">Informations d'identité</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 text-indigo-600/50">Nom de famille <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="ex: MINKO ESSONE"
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition uppercase placeholder:normal-case placeholder:font-medium">
                        @error('nom') <p class="text-rose-500 text-[10px] font-bold ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Prénoms <span class="text-rose-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="ex: Marc"
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition">
                        @error('prenom') <p class="text-rose-500 text-[10px] font-bold ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Date de Naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" 
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Lieu de Naissance</label>
                        <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance') }}" placeholder="ex: Libreville"
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition">
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" stroke-width="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke-width="2"/></svg>
                    </div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-tighter italic">Origine & Diplômes</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Série du Baccalauréat</label>
                        <input type="text" name="bac" value="{{ old('bac') }}" placeholder="ex: Série TI" 
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Lycée / Établissement d'origine</label>
                        <input type="text" name="provenance" value="{{ old('provenance') }}" placeholder="ex: Lycée Paul Indjendjet Gondjout" 
                            class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-0 rounded-[1.5rem] font-bold text-slate-900 transition">
                    </div>
                </div>
            </div>

            <div class="pt-10 flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-1 py-5 bg-indigo-600 text-white rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-900 hover:shadow-2xl hover:shadow-indigo-200 transition-all duration-300">
                    Valider l'inscription de l'étudiant
                </button>
                <a href="{{ route('admin.etudiants.index') }}" class="flex-none px-10 py-5 bg-slate-100 text-slate-500 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] text-center hover:bg-slate-200 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <div class="mt-8 px-8 py-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 flex items-center gap-4">
        <div class="text-indigo-500">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        </div>
        <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest leading-relaxed">
            Une fois inscrit, vous pourrez générer son matricule et l'affecter à une classe depuis sa fiche profil.
        </p>
    </div>
</div>
@endsection
>>>>>>> 6f3d284 (Initialisation ERP INPTIC : Sidebar et Layout fonctionnels)
