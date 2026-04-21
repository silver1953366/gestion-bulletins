<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.audit.index') }}" class="p-2 bg-white rounded-xl shadow-sm text-slate-400 hover:text-slate-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Détail de l'Action #{{ $auditLog->id }}</h2>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">{{ $log->action }} sur {{ $log->model }} (ID: {{ $log->model_id }})</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Auteur</p>
                <p class="font-black text-slate-900 italic uppercase">{{ $auditLog->user->full_name ?? 'Système' }}</p>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date & Heure</p>
                <p class="font-black text-slate-900 italic uppercase">{{ $auditLog->created_at->format('d/m/Y à H:i:s') }}</p>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Adresse IP</p>
                <p class="font-mono font-bold text-indigo-600">{{ $auditLog->ip_address }}</p>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-white/10">
                <div class="p-10 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">État Précédent</h3>
                    </div>
                    <div class="bg-black/30 rounded-2xl p-6 font-mono text-[11px] text-rose-300 leading-relaxed overflow-x-auto">
                        @if($auditLog->old_value)
                            <pre>{{ json_encode($auditLog->old_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="italic opacity-50 text-white text-center py-4">Aucune donnée (Action de création)</p>
                        @endif
                    </div>
                </div>

                <div class="p-10 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-xs font-black text-white uppercase tracking-[0.2em]">Nouvel État</h3>
                    </div>
                    <div class="bg-black/30 rounded-2xl p-6 font-mono text-[11px] text-emerald-300 leading-relaxed overflow-x-auto">
                        @if($auditLog->new_value)
                            <pre>{{ json_encode($auditLog->new_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="italic text-rose-500 font-black text-center py-4 uppercase tracking-widest">Donnée supprimée</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>