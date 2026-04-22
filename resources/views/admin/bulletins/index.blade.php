<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter italic uppercase">Archives <span class="text-cyan-600">Bulletins</span></h1>
            <div class="flex gap-4 italic text-xs font-bold text-slate-400">
                <span>INPTIC</span> • <span>ÉDITION OFFICIELLE</span>
            </div>
        </div>
    </x-slot>

    {{-- Moteur de Génération --}}
    <div class="bg-slate-900 rounded-[3rem] p-10 mb-10 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-cyan-400 mb-2">Génération de documents</h2>
                <p class="text-3xl font-light leading-tight">Édition des bulletins S5, S6 ou Annuels avec statistiques de promotion.</p>
            </div>

            <div class="bg-white/5 p-6 rounded-[2.5rem] border border-white/10 backdrop-blur-md">
                <form action="{{ route('admin.bulletins.generate') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Étudiant</label>
                            <select name="etudiant_id" required class="w-full bg-slate-800 border-none rounded-xl text-xs font-bold text-white focus:ring-cyan-500">
                                @foreach(App\Models\Etudiant::with('user')->get() as $etudiant)
                                    <option value="{{ $etudiant->id }}">{{ $etudiant->user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Type</label>
                            <select name="type" required class="w-full bg-slate-800 border-none rounded-xl text-xs font-bold text-white focus:ring-cyan-500">
                                <option value="S5">Semestre 5</option>
                                <option value="S6">Semestre 6</option>
                                <option value="ANNUEL">Bilan Annuel</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Année Académique</label>
                        <select name="annee_academique_id" required class="w-full bg-slate-800 border-none rounded-xl text-xs font-bold text-white focus:ring-cyan-500">
                            @foreach(App\Models\AnneeAcademique::all() as $annee)
                                <option value="{{ $annee->id }}">{{ $annee->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-4 bg-cyan-600 hover:bg-white text-white hover:text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all duration-500">
                        Générer le PDF sécurisé
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Liste des bulletins générés --}}
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-10 py-7">Étudiant</th>
                    <th class="px-10 py-7">Type</th>
                    <th class="px-10 py-7">Année</th>
                    <th class="px-10 py-7">Date Génération</th>
                    <th class="px-10 py-7 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm italic">
                @forelse($bulletins as $bulletin)
                    <tr class="hover:bg-slate-50/40 transition-all group">
                        <td class="px-10 py-6 font-black text-slate-900 uppercase italic">
                            {{ $bulletin->etudiant->user->full_name }}
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-black text-[9px] uppercase">
                                {{ $bulletin->type }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-slate-500 font-bold uppercase text-xs">
                            {{ $bulletin->anneeAcademique->nom }}
                        </td>
                        <td class="px-10 py-6 text-slate-400 text-xs">
                            {{ $bulletin->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-10 py-6 text-right space-x-2">
                            <a href="{{ route('admin.bulletins.download', $bulletin) }}" class="inline-flex p-3 bg-cyan-50 text-cyan-600 rounded-xl hover:bg-cyan-600 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5"/></svg>
                            </a>
                            <form action="{{ route('admin.bulletins.destroy', $bulletin) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Supprimer cette archive ?')" class="p-3 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center text-slate-300 font-black uppercase text-xs tracking-widest">
                            Aucun bulletin archivé pour le moment
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-10 py-8 bg-slate-50/50">
            {{ $bulletins->links() }}
        </div>
    </div>
</x-app-layout>