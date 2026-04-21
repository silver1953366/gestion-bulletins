<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
                Gestion des Utilisateurs
            </h2>
            <a href="{{ route('admin.users.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                Nouvel Utilisateur
            </a>
        </div>
    </x-slot>

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
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-xs italic">
                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                            </div>
                            <span class="font-black text-slate-700 uppercase italic text-sm">
                                {{ $user->full_name }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-slate-500 font-medium">{{ $user->email }}</td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-black text-[10px] uppercase">
                            {{ $user->role->name ?? 'Aucun' }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right flex justify-end gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>