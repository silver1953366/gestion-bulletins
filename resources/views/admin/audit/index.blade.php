<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                    Journal <span class="text-violet-600">d'Audit</span>
                </h1>
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1 italic">Traçabilité complète des actions système</p>
            </div>
            <div class="flex gap-4">
                <span class="px-6 py-3 bg-white border border-slate-100 shadow-sm rounded-2xl text-[10px] font-black uppercase italic text-slate-500">
                    <span class="text-violet-600">{{ \App\Models\AuditLog::count() }}</span> Entrées totales
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        currentLog: null,
        openShow(log) {
            this.currentLog = log;
            $dispatch('open-modal', 'show-audit');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/30 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-8 py-6">Horodatage</th>
                            <th class="px-8 py-6">Utilisateur</th>
                            <th class="px-8 py-6">Action</th>
                            <th class="px-8 py-6">Modèle / Ressource</th>
                            <th class="px-8 py-6 text-right">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-5">
                                <span class="block font-black text-slate-900 text-xs tracking-tighter">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-8 py-5 text-xs font-black text-slate-700 uppercase italic">
                                {{ $log->user->name ?? 'Système' }}
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $color = match($log->action) {
                                        'CREATE' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'UPDATE' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'DELETE' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        default  => 'bg-slate-50 text-slate-600 border-slate-100'
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-lg font-black text-[10px] border {{ $color }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 text-[11px] uppercase italic tracking-tight">{{ str_replace('App\\Models\\', '', $log->model) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">ID: #{{ $log->model_id }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button @click="openShow({{ $log->toJson() }})" class="p-2.5 bg-slate-50 text-slate-400 rounded-xl hover:text-violet-600 hover:bg-violet-50 transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2.5"/></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>

        <x-modal name="show-audit" focusable>
            <div class="p-10" x-show="currentLog">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Détails de <span class="text-violet-500">l'Action</span></h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Inspection des données modifiées</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-sm font-black text-slate-900" x-text="currentLog ? new Date(currentLog.created_at).toLocaleString() : ''"></span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic" x-text="'IP: ' + currentLog?.ip_address"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-widest ml-1 italic">Anciennes Valeurs</label>
                        <div class="bg-rose-50/30 border border-rose-100 rounded-[2rem] p-6 min-h-[200px] overflow-auto">
                            <template x-if="currentLog?.old_value">
                                <div class="space-y-2">
                                    <template x-for="(value, key) in currentLog.old_value">
                                        <div class="flex flex-col border-b border-rose-100/50 pb-2">
                                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-tighter" x-text="key"></span>
                                            <span class="text-xs font-bold text-slate-700" x-text="value"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!currentLog?.old_value">
                                <div class="h-full flex items-center justify-center italic text-slate-300 text-xs font-bold uppercase tracking-widest">Aucune donnée</div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-emerald-500 uppercase tracking-widest ml-1 italic">Nouvelles Valeurs</label>
                        <div class="bg-emerald-50/30 border border-emerald-100 rounded-[2rem] p-6 min-h-[200px] overflow-auto">
                            <template x-if="currentLog?.new_value">
                                <div class="space-y-2">
                                    <template x-for="(value, key) in currentLog.new_value">
                                        <div class="flex flex-col border-b border-emerald-100/50 pb-2">
                                            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-tighter" x-text="key"></span>
                                            <span class="text-xs font-bold text-slate-700" x-text="value"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!currentLog?.new_value">
                                <div class="h-full flex items-center justify-center italic text-slate-300 text-xs font-bold uppercase tracking-widest text-center">Donnée supprimée ou inexistante</div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-end">
                    <button @click="$dispatch('close')" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-violet-600 transition-all shadow-xl shadow-slate-200">
                        Fermer le journal
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.4s ease-out forwards;
        }
    </style>
</x-app-layout>