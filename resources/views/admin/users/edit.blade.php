<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
            Modifier : {{ $user->full_name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mt-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10 space-y-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prénom</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rôle Système</label>
                    <select name="role_id" required class="w-full px-6 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100 space-y-6">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase">Sécurité</h3>
                    <p class="text-xs text-slate-400 font-medium">Laissez vide pour conserver le mot de passe actuel.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <input type="password" name="password" placeholder="Nouveau mot de passe" class="w-full px-6 py-4 bg-white border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm shadow-sm">
                    <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" class="w-full px-6 py-4 bg-white border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm shadow-sm">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-slate-600 transition">Annuler</a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</x-app-layout>