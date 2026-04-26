<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                Gestion des <span class="text-indigo-600">Utilisateurs</span>
            </h2>
            <button @click="roleSelected = ''; $dispatch('open-modal', 'create-user')" 
                    class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i> Nouvel Utilisateur
            </button>
        </div>
    </x-slot>

    <div x-data="{ 
        currentUser: {}, 
        deleteUrl: '',
        roleNom: '',
        roleSelected: '',
        
        openEdit(user) {
            this.currentUser = JSON.parse(JSON.stringify(user));
            this.roleSelected = user.role ? user.role.nom.toLowerCase() : '';
            $dispatch('open-modal', 'edit-user');
        },
        openDelete(user) {
            this.currentUser = user;
            this.roleNom = user.role ? user.role.nom : 'Aucun';
            this.deleteUrl = '/admin/users/' + user.id;
            $dispatch('open-modal', 'confirm-user-deletion');
        },
        handleRoleChange(e) {
            this.roleSelected = e.target.options[e.target.selectedIndex].text.toLowerCase();
        }
    }" 
    class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Notifications Flash --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100 font-bold uppercase tracking-tight shadow-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 font-bold uppercase tracking-tight shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Table des Utilisateurs --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mt-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-8 py-5 w-20">Photo</th>
                            <th class="px-4 py-5">Identité</th>
                            <th class="px-8 py-5">Email</th>
                            <th class="px-8 py-5 text-center">Rôle</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-5">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black text-xs italic overflow-hidden shadow-md border-2 border-white ring-1 ring-slate-100">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <span class="font-black text-slate-700 uppercase italic text-sm tracking-tight">
                                    {{ $user->first_name }} <span class="text-indigo-600">{{ $user->last_name }}</span>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-500 font-bold text-xs">{{ $user->email }}</td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-4 py-1.5 rounded-xl font-black text-[9px] uppercase tracking-widest shadow-sm 
                                    {{ $user->role && strtolower($user->role->nom) === 'admin' ? 'bg-rose-600 text-white' : 'bg-indigo-50 text-indigo-600' }}">
                                    {{ $user->role->nom ?? 'Aucun' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-3">
                                    <button @click="openEdit({{ $user->toJson() }})" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button @click="openDelete({{ $user->toJson() }})" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-600 hover:text-white transition shadow-sm">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-slate-300 font-black uppercase text-xs tracking-[0.2em]">
                                <i class="fas fa-user-slash block text-4xl mb-4 opacity-20"></i>
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $users->links() }}
        </div>

       {{-- ========================================== --}}
{{-- MODAL CRÉATION (V.2 OPTIMISÉE) --}}
{{-- ========================================== --}}
<x-modal name="create-user" focusable>
    <div class="p-0 max-h-[95vh] overflow-y-auto no-scrollbar bg-white">
        <div class="p-8">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-8 sticky top-0 bg-white/90 backdrop-blur-sm z-10 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase italic tracking-tighter">
                        Nouvel <span class="text-indigo-600">Accès</span>
                    </h2>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mt-1">Enregistrement INPTIC 2026</p>
                </div>
                <button @click="$dispatch('close')" class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 hover:text-rose-500 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Section Identité --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Prénom</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Nom de famille</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition shadow-inner" style="text-transform: uppercase;">
                    </div>
                </div>

                {{-- Email et Rôle --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Email Professionnel</label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Rôle Système</label>
                        <select name="role_id" id="role_selector" required 
                            @change="roleSelected = $el.options[$el.selectedIndex].text.toLowerCase()"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-[1.5rem] font-bold text-sm focus:border-indigo-500 focus:bg-white focus:ring-0 transition shadow-inner appearance-none">
                            <option value="" disabled selected>Choisir un rôle...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Alertes Dynamiques (Correction du bug d'affichage) --}}
                <div x-show="roleSelected !== ''" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
                    <template x-if="roleSelected.includes('etudiant') || roleSelected.includes('étudiant')">
                        <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-2xl flex items-center gap-4 shadow-sm">
                            <div class="w-8 h-8 bg-amber-400 rounded-full flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-graduation-cap text-xs"></i>
                            </div>
                            <p class="text-[10px] font-black text-amber-900 uppercase tracking-tight">
                                <span class="opacity-50">Note :</span> Redirection vers le profil académique après validation.
                            </p>
                        </div>
                    </template>

                    <template x-if="roleSelected.includes('enseignant')">
                        <div class="p-4 bg-indigo-50 border-l-4 border-indigo-400 rounded-r-2xl flex items-center gap-4 shadow-sm">
                            <div class="w-8 h-8 bg-indigo-400 rounded-full flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-chalkboard-teacher text-xs"></i>
                            </div>
                            <p class="text-[10px] font-black text-indigo-900 uppercase tracking-tight">
                                <span class="opacity-50">Note :</span> Redirection vers la gestion des spécialités après validation.
                            </p>
                        </div>
                    </template>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Photo de profil</label>
                    <div class="relative group">
                        <input type="file" name="photo" 
                            class="w-full px-5 py-3 bg-slate-100 border-2 border-dashed border-slate-200 rounded-2xl font-bold text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-slate-900 file:text-white group-hover:border-indigo-300 transition cursor-pointer">
                    </div>
                </div>

                {{-- Sécurité --}}
                <div class="p-6 bg-slate-900 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fas fa-shield-alt text-6xl text-white"></i>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 relative z-10">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.2em] ml-1">Mot de passe</label>
                            <input type="password" name="password" required 
                                class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl font-bold text-xs text-white focus:ring-2 focus:ring-indigo-400 shadow-inner placeholder:text-white/20" placeholder="••••••••">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.2em] ml-1">Confirmation</label>
                            <input type="password" name="password_confirmation" required 
                                class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl font-bold text-xs text-white focus:ring-2 focus:ring-indigo-400 shadow-inner placeholder:text-white/20" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close')" class="text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-rose-500 transition">
                        Abandonner
                    </button>
                    <button type="submit" class="px-12 py-5 bg-indigo-600 text-white rounded-[1.8rem] font-black text-[11px] uppercase tracking-[0.15em] hover:bg-slate-900 transition-all shadow-xl hover:-translate-y-1">
                        Finaliser la création <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-modal>

        {{-- ========================================== --}}
        {{-- MODAL MODIFICATION --}}
        {{-- ========================================== --}}
        <x-modal name="edit-user" focusable>
            <div class="p-0 max-h-[90vh] overflow-y-auto no-scrollbar">
                <div class="p-10">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Édition du <span class="text-indigo-600">Profil</span></h2>
                    
                    <form :action="'/admin/users/' + currentUser.id" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                                <input type="text" name="first_name" x-model="currentUser.first_name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                                <input type="text" name="last_name" x-model="currentUser.last_name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner uppercase">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                <input type="email" name="email" x-model="currentUser.email" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle</label>
                                <select name="role_id" x-model="currentUser.role_id" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nouvelle Photo</label>
                            <input type="file" name="photo" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl font-bold text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-900 file:text-white shadow-inner">
                        </div>

                        <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-4">Laissez vide pour conserver le mot de passe actuel</p>
                            <div class="grid grid-cols-2 gap-6">
                                <input type="password" name="password" placeholder="Nouveau" class="w-full px-5 py-4 bg-white border-none rounded-2xl font-bold text-xs shadow-sm focus:ring-indigo-500">
                                <input type="password" name="password_confirmation" placeholder="Confirmer" class="w-full px-5 py-4 bg-white border-none rounded-2xl font-bold text-xs shadow-sm focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="flex justify-end gap-6 pt-4">
                            <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-600">Annuler</button>
                            <button type="submit" class="px-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-xl">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- ========================================== --}}
        {{-- MODAL SUPPRESSION --}}
        {{-- ========================================== --}}
        <x-modal name="confirm-user-deletion" focusable>
            <form method="post" :action="deleteUrl" class="p-10">
                @csrf @method('delete')
                <div class="flex items-center gap-8 mb-8 text-center sm:text-left flex-col sm:flex-row">
                    <div class="w-20 h-20 bg-rose-50 rounded-[2rem] flex items-center justify-center text-rose-500 text-3xl shadow-sm border border-rose-100 shrink-0">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Suppression <span class="text-rose-600">Confirmée ?</span></h2>
                        <p class="text-rose-600 text-lg font-black uppercase italic mt-2" x-text="currentUser.first_name + ' ' + currentUser.last_name"></p>
                    </div>
                </div>

                <div class="p-6 bg-rose-50/30 rounded-[2rem] border border-rose-100 mb-8 font-black text-rose-800 text-[10px] uppercase italic leading-relaxed">
                    Attention : La suppression de cet utilisateur entraînera la perte définitive de toutes ses données liées au système (Profils, absences, notes).
                </div>

                <div class="flex justify-end gap-6">
                    <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 font-black text-[10px] uppercase tracking-widest">Annuler</button>
                    <button type="submit" class="px-10 py-5 bg-rose-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition shadow-xl">
                        Confirmer
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>

<style>
    /* Design sans barre de défilement visible */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>