<x-app-layout>
    <div x-data="{ openCreate: false }">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                    Gestion des Utilisateurs
                </h2>
                {{-- Remplacer l'ancien bouton <button @click="..."> par ce lien --}}
                    <a href="{{ route('admin.users.create') }}" 
                         class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                          <i class="fas fa-plus"></i>
                                    Nouvel Utilisateur
                     </a>
            </div>
        </x-slot>

        {{-- Table --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mt-8">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Utilisateur</th>
                        <th class="px-8 py-5">Email</th>
                        <th class="px-8 py-5">Rôle</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs italic border-2 border-white shadow-sm">
                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                                <span class="font-black text-slate-700 uppercase italic text-sm italic underline-offset-4 decoration-indigo-500/30">
                                    {{ $user->full_name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-slate-500 font-bold text-xs">{{ $user->email }}</td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-lg font-black text-[9px] uppercase tracking-wider
                                {{ $user->role && $user->role->slug === 'admin' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->role->nom ?? 'Aucun' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right flex justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>

        {{-- MODAL DE CRÉATION --}}
        <div x-show="openCreate" 
             class="fixed inset-0 z-[100] overflow-y-auto" 
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop flou --}}
                <div x-show="openCreate" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" 
                     @click="openCreate = false"></div>

                <div x-show="openCreate" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-[2.5rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100">
                    
                    <div class="px-10 py-10">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Nouveau Profil</h3>
                            <button @click="openCreate = false" class="text-slate-300 hover:text-rose-500 transition"><i class="fas fa-times text-xl"></i></button>
                        </div>

                        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                                    <input type="text" name="first_name" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                                    <input type="text" name="last_name" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                    <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle</label>
                                    <select name="role_id" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mot de passe</label>
                                    <input type="password" name="password" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirmation</label>
                                    <input type="password" name="password_confirmation" required class="w-full px-6 py-4 bg-slate-50 border-none focus:ring-2 focus:ring-indigo-500 rounded-2xl font-bold text-sm">
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-6">
                                <button type="button" @click="openCreate = false" class="px-8 py-4 text-slate-400 font-black text-[10px] uppercase tracking-widest">Annuler</button>
                                <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-xl shadow-indigo-100">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>