<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        currentParam: { cle: '', valeur: '', description: '' },
        editParam(param) {
            this.currentParam = param;
            this.showCreateModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Paramètres Système</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Configuration globale de la plateforme</p>
                </div>
                <button @click="currentParam = { cle: '', valeur: '', description: '' }; showCreateModal = true" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-fuchsia-600 text-white rounded-2xl font-bold text-sm hover:bg-fuchsia-700 transition shadow-lg shadow-fuchsia-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" /><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" /></svg>
                    Nouveau Réglage
                </button>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
            @foreach($parametres as $param)
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition group relative">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[10px] font-black text-fuchsia-500 uppercase tracking-widest bg-fuchsia-50 px-3 py-1 rounded-full">
                        {{ $param->cle }}
                    </span>
                    <button @click="editParam({{ json_encode($param) }})" class="opacity-0 group-hover:opacity-100 transition p-2 text-slate-400 hover:text-fuchsia-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2"/></svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-slate-900 font-black text-lg italic truncate">
                        @if(is_array($param->valeur))
                            <span class="text-slate-400 font-medium text-xs font-mono">Structure JSON complexe</span>
                        @else
                            {{ $param->valeur }}
                        @endif
                    </h3>
                    <p class="text-slate-500 text-xs mt-2 leading-relaxed">
                        {{ $param->description ?? 'Aucune description fournie.' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Configuration</h2>
                    </div>
                    <form action="{{ route('admin.parametres.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Clé Unique</label>
                            <input type="text" name="cle" x-model="currentParam.cle" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-fuchsia-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: NOM_ECOLE">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Valeur</label>
                            <input type="text" name="valeur" x-model="currentParam.valeur" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-fuchsia-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: INPTIC Libreville">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description (Usage)</label>
                            <textarea name="description" x-model="currentParam.description" rows="3" class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-fuchsia-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="À quoi sert ce paramètre ?"></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-fuchsia-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-fuchsia-700 transition shadow-lg">
                            Enregistrer le réglage
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>