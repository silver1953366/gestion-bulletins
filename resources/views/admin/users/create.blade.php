<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
            Nouvel Utilisateur
        </h2>
    </x-slot>

    <div class="max-w-4xl mt-8">
        <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                    @error('first_name') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle Système</label>
                    <select name="role_id" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mot de passe</label>
                    <input type="password" name="password" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirmation</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-600 transition">Annuler</a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    Créer le compte
                </button>
            </div>
        </form>
    </div>
</x-app-layout>