<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase underline decoration-indigo-100 decoration-8 underline-offset-4">
                    Configuration des Semestres
                </h1>
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-1 italic">
                    Paramétrage des cycles de formation
                </p>
            </div>
            <button @click="$dispatch('open-modal', 'add-semestre')" class="px-8 py-4 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-xl shadow-slate-200 group">
                <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> Nouveau Semestre
            </button>
        </div>
    </x-slot>

    <div class="py-6 space-y-6 animate-fade-in" x-data="{ 
        currentSemestre: { id: '', libelle: '', classe_id: '' },
        editSemestre(s) {
            this.currentSemestre = s;
            $dispatch('open-modal', 'edit-semestre');
        }
    }">
        
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-tighter italic animate-bounce">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-tighter italic">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Libellé (Période)</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Classe Rattachée</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($semestres as $s)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-[10px] font-black text-white italic">
                                    {{ substr($s->libelle, 0, 2) }}
                                </div>
                                <span class="font-black text-slate-900 uppercase italic tracking-tighter">{{ $s->libelle }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-black text-[9px] uppercase tracking-widest border border-indigo-100">
                                {{ $s->classe->nom }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right flex justify-end gap-3">
                            <button @click="editSemestre({ id: '{{ $s->id }}', libelle: '{{ $s->libelle }}', classe_id: '{{ $s->classe_id }}' })" 
                                    class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            
                            <form action="{{ route('admin.semestres.destroy', $s) }}" method="POST" onsubmit="return confirm('Supprimer ce semestre ? Cette action est irréversible.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-500 hover:text-white transition shadow-sm">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-times text-4xl text-slate-100 mb-4"></i>
                                <p class="text-slate-400 font-black uppercase text-[10px] tracking-widest italic">Aucun semestre configuré</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 px-4">
            {{ $semestres->links() }}
        </div>

        <x-modal name="add-semestre" focusable>
            <form action="{{ route('admin.semestres.store') }}" method="POST" class="p-10">
                @csrf
                <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Nouveau <span class="text-indigo-600">Semestre</span></h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-8 border-l-4 border-indigo-600 pl-4">Max 10 caractères (Ex: S1, Semestre 1)</p>
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" maxlength="10" required placeholder="Ex: S1" 
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Affecter à une classe</label>
                        <select name="classe_id" required 
                                class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all appearance-none">
                            <option value="">Sélectionner la classe...</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-6 mt-10">
                    <button type="button" x-on:click="$dispatch('close')" class="font-black text-[10px] uppercase text-slate-400 hover:text-slate-600 tracking-widest transition">Annuler</button>
                    <button type="submit" class="px-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition shadow-2xl shadow-slate-200">
                        Confirmer la création
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal name="edit-semestre" focusable>
            <form :action="'{{ route('admin.semestres.index') }}/' + currentSemestre.id" method="POST" class="p-10">
                @csrf @method('PUT')
                <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Modifier <span class="text-indigo-600">Semestre</span></h2>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-8 border-l-4 border-indigo-600 pl-4">Édition des paramètres temporels</p>

                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" x-model="currentSemestre.libelle" maxlength="10" required 
                               class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Classe</label>
                        <select name="classe_id" x-model="currentSemestre.classe_id" required 
                                class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all appearance-none">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-6 mt-10">
                    <button type="button" x-on:click="$dispatch('close')" class="font-black text-[10px] uppercase text-slate-400 hover:text-slate-600 tracking-widest transition">Annuler</button>
                    <button type="submit" class="px-10 py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition shadow-2xl shadow-indigo-100">
                        Sauvegarder les modifications
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>