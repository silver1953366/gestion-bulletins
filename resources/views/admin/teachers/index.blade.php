@extends('components.layouts.master')

@section('content')
<div x-data="teacherManager()" 
     x-init="checkFlashData()"
     class="max-w-7xl mx-auto animate-fade-in pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase leading-none">
                Corps <span class="text-cyan-600">Enseignant</span>
            </h1>
            <p class="text-slate-500 font-bold uppercase text-[9px] tracking-[0.3em] mt-3 ml-1 flex items-center gap-2">
                <span class="w-6 h-[2px] bg-cyan-600"></span>
                Gestion des profils & grades académiques
            </p>
        </div>

        <button @click="openCreate()" 
           class="inline-flex items-center justify-center px-6 py-3.5 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-xl shadow-cyan-100/50 gap-3 group">
            <svg class="w-4 h-4 transform group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Initialiser un profil
        </button>
    </div>

    {{-- Alertes --}}
    @if(session('success') && !session('open_edit_modal'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl animate-slide-up">
            <p class="text-emerald-700 text-[10px] font-black uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tableau des enseignants --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-8 py-5">Identité</th>
                    <th class="px-8 py-5">Spécialité</th>
                    <th class="px-8 py-5">Statut</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @forelse($teachers as $teacher)
                <tr class="hover:bg-slate-50/30 transition-all group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            {{-- Photo ou Initiales --}}
                            @if($teacher->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $teacher->user->profile_photo_path) }}" 
                                     class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center font-black text-[10px] text-slate-400 uppercase border border-slate-200">
                                    {{ substr($teacher->user->first_name, 0, 1) }}{{ substr($teacher->user->last_name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-black text-slate-900 uppercase leading-none tracking-tight text-xs">
                                    {{ $teacher->user->full_name }}
                                </p>
                                <span class="text-[9px] font-bold {{ $teacher->grade ? 'text-cyan-600' : 'text-slate-300' }} uppercase tracking-tighter italic">
                                    {{ $teacher->grade ?? 'Grade non défini' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @if($teacher->specialite === 'À définir')
                            <button @click="openEdit({{ json_encode($teacher->load('user')) }})" 
                                    class="flex items-center gap-2 group/alert">
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[9px] font-black uppercase tracking-tighter border border-amber-100 group-hover/alert:bg-amber-500 group-hover/alert:text-white transition-all">
                                    ⚠️ Profil incomplet
                                </span>
                            </button>
                        @else
                            <span class="text-[11px] font-bold text-slate-500 uppercase italic">{{ $teacher->specialite }}</span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $teacher->matieres->count() > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            <span class="text-[10px] font-black text-slate-600 uppercase tracking-tighter">
                                {{ $teacher->matieres->count() }} matière(s)
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            {{-- Voir Plus --}}
                            <button @click="openShow({{ json_encode($teacher->load('user', 'matieres')) }})" 
                                    class="p-2.5 text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <button @click="openEdit({{ json_encode($teacher->load('user')) }})" 
                                    class="p-2.5 text-slate-400 hover:bg-cyan-50 hover:text-cyan-600 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/>
                                </svg>
                            </button>
                            <button @click="confirmDelete({{ $teacher->id }}, '{{ $teacher->user->full_name }}')" 
                                    class="p-2.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center text-slate-400 font-bold uppercase text-[10px] tracking-widest">
                        Aucun profil enseignant trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $teachers->links() }}
        </div>
    </div>

    {{-- MODAL : VOIR PLUS (SHOW) --}}
    <div x-show="showModal" 
         class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
         x-cloak>
        <div @click.away="showModal = false" class="bg-white w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl animate-scale-up">
            <div class="relative h-32 bg-slate-900">
                <div class="absolute -bottom-12 left-8">
                    <template x-if="showData.photo">
                        <img :src="showData.photo" class="w-24 h-24 rounded-[2rem] border-4 border-white object-cover shadow-lg">
                    </template>
                    <template x-if="!showData.photo">
                        <div class="w-24 h-24 rounded-[2rem] border-4 border-white bg-cyan-600 flex items-center justify-center text-white text-2xl font-black" x-text="showData.initials"></div>
                    </template>
                </div>
            </div>
            <div class="pt-16 pb-10 px-8">
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tighter" x-text="showData.name"></h3>
                <p class="text-cyan-600 font-bold text-[10px] uppercase tracking-[0.2em] mb-6" x-text="showData.grade || 'Sans Grade'"></p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-400 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.215.77a2 2 0 01-2.44 1.398 2 2 0 01-1.398-2.44l.77-.215a2 2 0 001.414-1.96l-.477-2.387a2 2 0 00-.547-1.022L9.29 6.606a2 2 0 00-2.828 0l-1.414 1.414a2 2 0 000 2.828l1.414 1.414a2 2 0 002.828 0l1.414-1.414z" stroke-width="2"/></svg>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Spécialité</p>
                            <p class="text-xs font-bold text-slate-700" x-text="showData.specialite"></p>
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-400 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2"/></svg>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Email professionnel</p>
                            <p class="text-xs font-bold text-slate-700" x-text="showData.email"></p>
                        </div>
                    </div>
                </div>

                <button @click="showModal = false" class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-cyan-600 transition-all">
                    Fermer la fiche
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL : CRÉATION / ÉDITION --}}
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-cloak>
        
        <div @click.away="modalOpen = false" class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden relative">
            <form :action="isEdit ? `/admin/teachers/${currentId}` : '{{ route('admin.teachers.store') }}'" method="POST" class="p-8">
                @csrf
                <template x-if="isEdit">@method('PUT')</template>

                <h2 class="text-xl font-black text-slate-900 uppercase italic mb-8 tracking-tighter leading-tight">
                    <span x-text="isEdit ? 'Configurer le profil de ' : 'Initialiser un profil'"></span>
                    <span class="text-cyan-600 block" x-show="isEdit" x-text="currentUserName"></span>
                </h2>

                <div class="space-y-6">
                    <div x-show="!isEdit" class="animate-fade-in">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Compte Enseignant</label>
                        <select name="user_id" :required="!isEdit" class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold focus:ring-2 focus:ring-cyan-500 transition-all">
                            <option value="">Choisir un compte...</option>
                            @foreach($availableUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Domaine d'expertise</label>
                        <input type="text" name="specialite" x-model="formData.specialite" required placeholder="ex: Réseaux & Sécurité" 
                               class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Grade académique</label>
                        <input type="text" name="grade" x-model="formData.grade" placeholder="ex: Master, Docteur, Ingénieur..." 
                               class="w-full bg-slate-50 border-none rounded-xl p-4 text-xs font-bold focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-10">
                    <button type="button" @click="modalOpen = false" class="text-[10px] font-black uppercase text-slate-400 hover:text-rose-500 transition-colors">
                        {{ session('open_edit_modal') ? 'Compléter plus tard' : 'Annuler' }}
                    </button>
                    <button type="submit" class="px-8 py-3.5 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-lg">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL : SUPPRESSION --}}
    <div x-show="deleteModal" class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" x-cloak>
        <div class="bg-white p-8 rounded-[2.5rem] max-w-sm w-full text-center shadow-2xl">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
            </div>
            <h3 class="text-lg font-black text-slate-900 uppercase italic">Supprimer le profil ?</h3>
            <p class="text-slate-500 text-[11px] font-bold mt-3" x-text="`Le compte de ${deleteName} restera actif.`"></p>
            <div class="mt-8 flex flex-col gap-2">
                <form :action="`/admin/teachers/${deleteId}`" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-4 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition-all">Confirmer</button>
                </form>
                <button @click="deleteModal = false" class="w-full py-4 text-slate-400 font-black text-[10px] uppercase tracking-widest">Annuler</button>
            </div>
        </div>
    </div>
</div>

<script>
function teacherManager() {
    return {
        modalOpen: false,
        deleteModal: false,
        showModal: false,
        isEdit: false,
        currentId: null,
        currentUserName: '',
        deleteId: null,
        deleteName: '',
        showData: {},
        formData: { specialite: '', grade: '' },

        checkFlashData() {
            @if(session('open_edit_modal') && session('teacher_to_edit'))
                const teacher = @json(session('teacher_to_edit'));
                setTimeout(() => { this.openEdit(teacher); }, 300);
            @endif
        },

        openCreate() {
            this.isEdit = false;
            this.formData = { specialite: '', grade: '' };
            this.modalOpen = true;
        },

        openEdit(teacher) {
            this.isEdit = true;
            this.currentId = teacher.id;
            this.currentUserName = teacher.user ? `${teacher.user.first_name} ${teacher.user.last_name}` : '';
            this.formData = {
                specialite: (teacher.specialite === 'À définir') ? '' : teacher.specialite,
                grade: teacher.grade || ''
            };
            this.modalOpen = true;
        },

        openShow(teacher) {
            this.showData = {
                name: teacher.user.full_name,
                email: teacher.user.email,
                grade: teacher.grade,
                specialite: teacher.specialite,
                initials: (teacher.user.first_name[0] + teacher.user.last_name[0]).toUpperCase(),
                photo: teacher.user.profile_photo_path ? `/storage/${teacher.user.profile_photo_path}` : null
            };
            this.showModal = true;
        },

        confirmDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteModal = true;
        }
    }
}
</script>
@endsection