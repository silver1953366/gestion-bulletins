<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentTeacher: { id: '', user_id: '', specialite: '', grade: '', user: { name: '' } },
        openEditModal(teacher) {
            this.currentTeacher = teacher;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Corps Enseignant</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Gestion des spécialités et grades académiques</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-cyan-600 text-white rounded-2xl font-bold text-sm hover:bg-cyan-700 transition shadow-lg shadow-cyan-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round"/></svg>
                    Nouveau Profil
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Enseignant</th>
                        <th class="px-8 py-5">Spécialité</th>
                        <th class="px-8 py-5 text-center">Grade</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($teachers as $teacher)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-black text-xs uppercase">
                                    {{ substr($teacher->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 uppercase italic leading-none">{{ $teacher->user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $teacher->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-cyan-50 text-cyan-600 rounded-lg font-black text-[10px] uppercase">
                                {{ $teacher->specialite }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center font-bold text-slate-500 italic">
                            {{ $teacher->grade ?? 'N/A' }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ json_encode($teacher) }})" class="p-2 text-slate-400 hover:text-cyan-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </button>
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Supprimer ce profil enseignant ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-400 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
                {{ $teachers->links() }}
            </div>
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Assigner un rôle Enseignant</h2>
                    </div>
                    <form action="{{ route('admin.teachers.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Utilisateur</label>
                            <select name="user_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-cyan-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                <option value="">Choisir un utilisateur...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Spécialité</label>
                            <input type="text" name="specialite" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-cyan-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: Réseaux & Télécoms">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Grade</label>
                            <input type="text" name="grade" class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-cyan-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: Docteur, Ingénieur...">
                        </div>
                        <button type="submit" class="w-full py-4 bg-cyan-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-cyan-700 transition shadow-lg mt-4">
                            Créer le profil
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>