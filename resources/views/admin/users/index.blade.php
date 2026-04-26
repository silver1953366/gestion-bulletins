<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                Gestion des <span class="text-indigo-600">Utilisateurs</span>
            </h2>
            <button @click="$dispatch('open-modal', 'create-user')" 
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
    x-init="
        @if($errors->any() && !old('_method'))
            $nextTick(() => $dispatch('open-modal', 'create-user'));
        @endif
    "
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

        {{-- Table --}}
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
        {{-- MODAL CRÉATION --}}
        {{-- ========================================== --}}
        <x-modal name="create-user" focusable>
            <div class="p-0 max-h-[90vh] overflow-y-auto custom-scrollbar">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-8 sticky top-0 bg-white z-10 pb-4 border-b border-slate-50">
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Nouveau <span class="text-indigo-600">Profil</span></h2>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1 italic">Remplissez les informations de base</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                            <i class="fas fa-user-plus text-xl"></i>
                        </div>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        @if ($errors->any() && !old('_method'))
                            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-xl mb-6">
                                <ul class="text-[11px] font-bold text-rose-500 uppercase tracking-tight">
                                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de famille</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Attribution du Rôle</label>
                                <select name="role_id" @change="handleRoleChange" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    <option value="" disabled selected>Sélectionner...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Notice de Redirection Dynamique --}}
                        <div x-show="roleSelected.includes('étudiant')" x-transition class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-2xl flex items-start gap-4">
                            <i class="fas fa-user-graduate text-amber-500 mt-1"></i>
                            <p class="text-[10px] font-black text-amber-800 leading-relaxed uppercase tracking-tight">
                                <span class="block text-xs mb-1">Redirection Étudiant</span>
                                Vous devrez compléter : Série du Bac, Matricule, Classe et Niveau.
                            </p>
                        </div>

                        <div x-show="roleSelected.includes('enseignant')" x-transition class="p-4 bg-indigo-50 border-l-4 border-indigo-400 rounded-r-2xl flex items-start gap-4">
                            <i class="fas fa-chalkboard-teacher text-indigo-500 mt-1"></i>
                            <p class="text-[10px] font-black text-indigo-800 leading-relaxed uppercase tracking-tight">
                                <span class="block text-xs mb-1">Redirection Enseignant</span>
                                Vous devrez compléter : Grade, Spécialité et Matières enseignées.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Photo (Avatar)</label>
                            <input type="file" name="photo" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl font-bold text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-600 file:text-white shadow-inner">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-900 rounded-[2rem] shadow-xl">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Mot de passe</label>
                                <input type="password" name="password" required class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl font-bold text-xs text-white focus:ring-2 focus:ring-indigo-400 shadow-inner">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-[0.2em] ml-1">Confirmation</label>
                                <input type="password" name="password_confirmation" required class="w-full px-5 py-4 bg-white/10 border-none rounded-2xl font-bold text-xs text-white focus:ring-2 focus:ring-indigo-400 shadow-inner">
                            </div>
                        </div>

                        <div class="flex justify-end gap-6 pt-6 sticky bottom-0 bg-white pb-4">
                            <button type="button" x-on:click="$dispatch('close')" class="text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-600">Annuler</button>
                            <button type="submit" class="px-10 py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-200">
                                Créer l'accès <i class="fas fa-chevron-right ml-2"></i>
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
            <div class="p-0 max-h-[90vh] overflow-y-auto">
                <div class="p-10">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">
                        Édition du <span class="text-indigo-600">Profil</span>
                    </h2>
                    
                    <form :action="'/admin/users/' + currentUser.id" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                                <input type="text" name="first_name" x-model="currentUser.first_name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                                <input type="text" name="last_name" x-model="currentUser.last_name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                <input type="email" name="email" x-model="currentUser.email" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle</label>
                                <select name="role_id" x-model="currentUser.role_id" @change="handleRoleChange" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 text-indigo-600">Actualiser la Photo (Optionnel)</label>
                            <input type="file" name="photo" class="w-full px-5 py-3 bg-slate-50 border-none rounded-2xl font-bold text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-slate-900 file:text-white">
                        </div>

                        {{-- Section Mot de passe Facultative --}}
                        <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 shadow-inner">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-shield-alt text-indigo-500 text-xs"></i>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Sécurité (Laissez vide pour conserver l'actuel)</p>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <input type="password" name="password" placeholder="Nouveau" class="w-full px-5 py-4 bg-white border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-sm">
                                <input type="password" name="password_confirmation" placeholder="Confirmer" class="w-full px-5 py-4 bg-white border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-indigo-500 shadow-sm">
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

                <div class="p-6 bg-rose-50/30 rounded-[2rem] border border-rose-100 mb-8">
                    <p class="text-[10px] font-black text-rose-800 leading-relaxed uppercase tracking-tight italic">
                        <i class="fas fa-info-circle mr-2"></i>
                        Attention : La suppression de cet utilisateur (Rôle: <span x-text="roleNom"></span>) entraînera la perte définitive de toutes ses données liées au système INPTIC.
                    </p>
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
    /* Pour une scrollbar plus discrète dans la modale */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6366f1;
    }
</style>