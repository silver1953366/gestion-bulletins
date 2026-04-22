<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 transition shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                Nouvel Utilisateur
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mt-8">
        <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10 space-y-8">
            @csrf
            
            {{-- Section : Informations Personnelles --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm placeholder:text-slate-300" placeholder="Prénom de l'utilisateur">
                    @error('first_name') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm placeholder:text-slate-300" placeholder="Nom de l'utilisateur">
                    @error('last_name') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Section : Identifiants & Rôle --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adresse Email Professionnelle</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm placeholder:text-slate-300" placeholder="email@exemple.com">
                    @error('email') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle Système</label>
                    <select name="role_id" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm text-slate-600">
                        <option value="" disabled selected>Choisir un rôle...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Section : Sécurité --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mot de passe</label>
                    <input type="password" name="password" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                    @error('password') <p class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirmation du mot de passe</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end items-center gap-6 pt-4 border-t border-slate-50">
                <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition">
                    Annuler l'opération
                </a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">
                    Enregistrer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</x-app-layout>