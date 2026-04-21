<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Validation des UEs</h1>
                <p class="text-slate-500 text-sm font-medium italic">Gestion des crédits ECTS et compensations</p>
            </div>
            <form action="{{ route('admin.resultats.ues.calculer-classe') }}" method="POST" class="flex gap-2">
                @csrf
                <select name="classe_id" class="px-4 py-2 bg-white border border-slate-200 rounded-xl font-bold text-xs">
                    <option value="">Sélectionner une classe</option>
                    @foreach(\App\Models\Classe::all() as $c)
                        <option value="{{ $c->id }}">{{ $c->nom }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    Lancer Délibération
                </button>
            </form>
        </div>
    </x-slot>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <th class="px-8 py-5">Étudiant</th>
                    <th class="px-8 py-5">Unité d'Enseignement</th>
                    <th class="px-8 py-5 text-center">Moyenne UE</th>
                    <th class="px-8 py-5 text-center">Crédits Acquis</th>
                    <th class="px-8 py-5 text-right">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @foreach($resultats as $res)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-5">
                        <span class="font-black text-slate-900 uppercase italic">{{ $res->etudiant->nom }}</span>
                    </td>
                    <td class="px-8 py-5 text-slate-500 font-bold uppercase text-xs">
                        {{ $res->ue->nom }} ({{ $res->ue->code }})
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-lg font-black {{ $res->moyenne >= 10 ? 'text-indigo-600' : ($res->compense ? 'text-amber-500' : 'text-rose-600') }}">
                            {{ number_format($res->moyenne, 2) }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center font-black text-slate-700 uppercase">
                        {{ $res->credits_acquis }}
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($res->isValide())
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg font-black text-[10px] uppercase">Validé</span>
                        @elseif($res->compense)
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg font-black text-[10px] uppercase">Compensé</span>
                        @else
                            <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-lg font-black text-[10px] uppercase">Ajourné</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
            {{ $resultats->links() }}
        </div>
    </div>
</x-app-layout>