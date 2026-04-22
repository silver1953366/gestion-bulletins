<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 transition shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div class="flex flex-col">
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                    Modifier le Profil
                </h2>
                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">{{ $user->full_name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mt-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10 space-y-8">
            @csrf 
            @method('PUT')
            
            {{-- Section : Informations Identité --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                    @error('first_name') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                    @error('last_name') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Section : Accès & Rôle --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                    @error('email') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle Système</label>
                    <select name="role_id" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm text-slate-600">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Zone Sécurité (Mise en avant) --}}
            <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-shield-alt text-indigo-500 text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter">Sécurité du compte</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Laissez vide pour conserver le mot de passe actuel</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <input type="password" name="password" placeholder="Nouveau mot de passe" 
                            class="w-full px-6 py-4 bg-white border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm shadow-sm">
                        @error('password') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <input type="password" name="password_confirmation" placeholder="Confirmer le nouveau mot de passe" 
                            class="w-full px-6 py-4 bg-white border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="flex justify-end items-center gap-6 pt-4 border-t border-slate-50">
                <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition">
                    Annuler les modifications
                </a>
                <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg shadow-slate-200">
                    Mettre à jour le profil
                </button>
            </div>
        </form>
    </div>
</x-app-layout>