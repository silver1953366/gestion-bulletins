<x-app-layout>
    <div x-data="{ showCreateModal: false }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Attributions des Cours</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Liaison entre le corps enseignant et les matières</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-violet-600 text-white rounded-2xl font-bold text-sm hover:bg-violet-700 transition shadow-lg shadow-violet-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke-width="2" stroke-linecap="round"/></svg>
                    Nouvelle Attribution
                </button>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-8 py-5">Enseignant</th>
                            <th class="px-8 py-5">Matière Enseignée</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @foreach($affectations as $aff)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-5 font-black text-slate-900 italic uppercase">
                                {{ $aff->teacherProfile->user->name }}
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-violet-50 text-violet-600 rounded-lg font-black text-[10px] uppercase">
                                    {{ $aff->matiere->libelle }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form action="{{ route('admin.enseignant-matiere.destroy', $aff) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-300 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-6">
                    {{ $affectations->links() }}
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-violet-600 to-indigo-700 p-8 rounded-[2.5rem] text-white shadow-xl shadow-violet-100">
                    <h3 class="font-black text-xl italic mb-2">Total Charges</h3>
                    <p class="text-4xl font-black italic">{{ $affectations->total() }}</p>
                    <p class="text-violet-100 text-xs mt-4 font-medium italic opacity-80 italic">Volume d'affectations actives pour le semestre en cours.</p>
                </div>
            </div>
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic text-center">Nouvelle Attribution</h2>
                    </div>
                    <form action="{{ route('admin.enseignant-matiere.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Enseignant</label>
                            <select name="teacher_profile_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-violet-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                <option value="">Sélectionner le professeur...</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->user->name }} ({{ $t->specialite }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Matière à assigner</label>
                            <select name="matiere_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-violet-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                <option value="">Sélectionner la matière...</option>
                                @foreach($matieres as $m)
                                    <option value="{{ $m->id }}">[{{ $m->code }}] {{ $m->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-4 bg-violet-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-violet-700 transition shadow-lg mt-4">
                            Confirmer l'attribution
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>