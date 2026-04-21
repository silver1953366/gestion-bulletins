<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Journal d'Audit</h1>
                <p class="text-slate-500 text-sm font-medium italic">Traçabilité complète des actions administratives</p>
            </div>
            <div class="flex gap-2">
                <span class="px-4 py-2 bg-slate-100 rounded-xl text-[10px] font-black uppercase text-slate-500 border border-slate-200">
                    {{ \App\Models\AuditLog::count() }} Entrées
                </span>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <th class="px-8 py-5">Date & Heure</th>
                    <th class="px-8 py-5">Utilisateur</th>
                    <th class="px-8 py-5">Action</th>
                    <th class="px-8 py-5">Modèle / ID</th>
                    <th class="px-8 py-5">IP</th>
                    <th class="px-8 py-5 text-right">Détails</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-xs">
                @foreach($logs as $log)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-5 font-bold text-slate-500 uppercase">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="font-black text-slate-900 uppercase italic">{{ $log->user->full_name ?? 'Système' }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $color = match($log->action) {
                                'CREATE' => 'bg-emerald-100 text-emerald-700',
                                'UPDATE' => 'bg-amber-100 text-amber-700',
                                'DELETE' => 'bg-rose-100 text-rose-700',
                                default  => 'bg-slate-100 text-slate-700'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-lg font-black {{ $color }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700">{{ $log->model }}</span>
                            <span class="text-slate-400 font-medium italic">ID: {{ $log->model_id }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 font-mono text-slate-400">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button @click="window.location='{{ route('admin.audit.show', $log) }}'" class="p-2 text-slate-400 hover:text-slate-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>