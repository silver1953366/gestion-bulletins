<x-app-layout>
    <div x-data="{ 
        currentParam: { id: '', cle: '', valeur: '', description: '' },
        editParam(param) {
            this.currentParam = {
                id: param.id,
                cle: param.cle,
                valeur: typeof param.valeur === 'object' ? JSON.stringify(param.valeur) : param.valeur,
                description: param.description || ''
            };
            $dispatch('open-modal', 'form-parametre');
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase underline decoration-fuchsia-100 decoration-8 underline-offset-4">
                        Paramètres Système
                    </h1>
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-1 italic">
                        Configuration globale du système
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="currentParam = { id: '', cle: '', valeur: '', description: '' }; $dispatch('open-modal', 'form-parametre')" 
                        class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-fuchsia-600 transition-all shadow-xl shadow-slate-200 group">
                        <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform"></i> Nouveau Réglage
                    </button>
                </div>
            </div>
        </x-slot>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-tighter italic animate-bounce">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-6 animate-fade-in">
            @forelse($parametres as $param)
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="flex justify-between items-start mb-6 relative">
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-fuchsia-400 shadow-lg shadow-slate-200">
                        <i class="fas fa-sliders-h text-sm"></i>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editParam({{ json_encode($param) }})" class="w-8 h-8 flex items-center justify-center bg-fuchsia-50 text-fuchsia-600 rounded-lg hover:bg-fuchsia-600 hover:text-white transition">
                            <i class="fas fa-pen text-[10px]"></i>
                        </button>
                        <form action="{{ route('admin.parametres.destroy', $param) }}" method="POST" onsubmit="return confirm('Supprimer ce réglage ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="relative">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-1 font-mono">CLÉ_ID</span>
                    <h3 class="text-slate-900 font-black text-xl italic tracking-tighter mb-4 uppercase truncate">
                        {{ $param->cle }}
                    </h3>
                    
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-4 shadow-inner">
                        <span class="text-[9px] font-black text-fuchsia-500 uppercase tracking-widest block mb-1 italic text-right">Contenu</span>
                        <p class="text-slate-900 font-black text-lg font-mono">
                            {{ is_array($param->valeur) ? 'DATA_OBJECT' : $param->valeur }}
                        </p>
                    </div>

                    <p class="text-slate-500 text-[10px] leading-relaxed font-bold italic border-l-2 border-fuchsia-200 pl-3 uppercase tracking-tighter">
                        {{ $param->description ?? 'Aucune documentation' }}
                    </p>
                </div>
            </div>
            @empty
            <div class="col-span-full py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100 text-slate-300">
                <i class="fas fa-tools text-5xl mb-4"></i>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] italic">Aucune configuration active</p>
            </div>
            @endforelse
        </div>

        <x-modal name="form-parametre" focusable>
            <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 italic tracking-tighter uppercase">
                        <span x-text="currentParam.id ? 'Modifier' : 'Nouveau'"></span> <span class="text-fuchsia-600">Réglage</span>
                    </h2>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Accès direct à la base de configuration</p>
                </div>
                <button @click="$dispatch('close')" class="w-10 h-10 flex items-center justify-center text-slate-300 hover:bg-rose-50 hover:text-rose-500 rounded-full transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.parametres.store') }}" method="POST" class="p-10 space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 italic text-left block italic">Identifiant de la clé</label>
                    <input type="text" name="cle" x-model="currentParam.cle" :readonly="currentParam.id !== ''" required 
                           class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent focus:border-fuchsia-500 focus:bg-white focus:ring-0 rounded-2xl font-black text-xs uppercase tracking-widest transition-all" 
                           placeholder="EX: NOM_ETABLISSEMENT">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 italic text-left block italic">Valeur du paramètre</label>
                    <input type="text" name="valeur" x-model="currentParam.valeur" required 
                           class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent focus:border-fuchsia-500 focus:bg-white focus:ring-0 rounded-2xl font-black text-sm transition-all" 
                           placeholder="Texte, Chiffre ou Code">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 italic text-left block italic">Description (Usage interne)</label>
                    <textarea name="description" x-model="currentParam.description" rows="3" 
                              class="w-full px-6 py-5 bg-slate-50 border-2 border-transparent focus:border-fuchsia-500 focus:bg-white focus:ring-0 rounded-2xl font-bold text-xs italic transition-all" 
                              placeholder="Expliquez ici l'utilité de ce réglage..."></textarea>
                </div>
                
                <div class="flex flex-col gap-3 pt-4">
                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-fuchsia-600 transition shadow-2xl shadow-slate-200">
                        <i class="fas fa-save mr-2"></i> Enregistrer le paramètre
                    </button>
                    <button type="button" @click="$dispatch('close')" class="w-full py-3 font-black text-[9px] uppercase text-slate-300 hover:text-slate-500 tracking-widest transition">
                        Fermer sans enregistrer
                    </button>
                </div>
            </form>
        </x-modal>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.4s ease-out forwards;
        }
    </style>
</x-app-layout>