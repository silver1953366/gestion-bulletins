<aside 
    x-data="{ 
        saveScroll() { sessionStorage.setItem('sidebar-scroll', $el.querySelector('nav').scrollTop) },
        restoreScroll() { 
            const position = sessionStorage.getItem('sidebar-scroll');
            if (position) { $el.querySelector('nav').scrollTop = position; }
        }
    }"
    x-init="restoreScroll()"
    :class="sidebarOpen ? 'w-72' : 'w-20'" 
    class="bg-[#0f172a] text-slate-400 transition-all duration-300 flex flex-col shadow-2xl z-50 shrink-0 h-screen overflow-hidden sticky top-0">
    
    <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800 shrink-0 bg-[#0f172a]">
        <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332-4.5 1.253" />
            </svg>
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" class="flex flex-col overflow-hidden whitespace-nowrap">
            <span class="text-white font-black text-xl tracking-tight leading-none uppercase">INPTIC</span>
            <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-[0.2em]">ERP System</span>
        </div>
    </div>

    <nav @click.capture="saveScroll()" class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar bg-[#0f172a] scroll-smooth">
        
        <div class="pb-4">
            <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" icon="fas fa-th-large" label="Tableau de Bord" />
        </div>

        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-4 pb-2 tracking-[0.2em]">Scolarité & Identités</p>
        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-users-cog" label="Comptes Utilisateurs" />
        <x-sidebar-link :href="route('admin.etudiants.index')" :active="request()->routeIs('admin.etudiants.*')" icon="fas fa-user-graduate" label="Dossiers Étudiants" />
        <x-sidebar-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')" icon="fas fa-chalkboard-teacher" label="Corps Enseignant" />

        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-6 pb-2 tracking-[0.2em]">Organisation</p>
        <x-sidebar-link :href="route('admin.departements.index')" :active="request()->routeIs('admin.departements.*')" icon="fas fa-sitemap" label="Départements" />
        <x-sidebar-link :href="route('admin.filieres.index')" :active="request()->routeIs('admin.filieres.*')" icon="fas fa-stream" label="Filières" />
        <x-sidebar-link :href="route('admin.classes.index')" :active="request()->routeIs('admin.classes.*')" icon="fas fa-school" label="Classes" />
        <x-sidebar-link :href="route('admin.semestres.index')" :active="request()->routeIs('admin.semestres.*')" icon="fas fa-clock" label="Semestres" />

        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-6 pb-2 tracking-[0.2em]">Programme (LMD)</p>
        <x-sidebar-link :href="route('admin.ues.index')" :active="request()->routeIs('admin.ues.*')" icon="fas fa-layer-group" label="Unités d'Ens. (UE)" />
        <x-sidebar-link :href="route('admin.matieres.index')" :active="request()->routeIs('admin.matieres.*')" icon="fas fa-book" label="Matières & Crédits" />
        <x-sidebar-link :href="route('admin.enseignant-matiere.index')" :active="request()->routeIs('admin.enseignant-matiere.*')" icon="fas fa-link" label="Attributions" />

        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-6 pb-2 tracking-[0.2em]">Notes & Examens</p>
        <x-sidebar-link :href="route('admin.evaluations.index')" :active="request()->routeIs('admin.evaluations.*')" icon="fas fa-edit" label="Saisie des Notes" />
        <x-sidebar-link :href="route('admin.imports.index')" :active="request()->routeIs('admin.imports.*')" icon="fas fa-file-import" label="Imports Excel" />
        <x-sidebar-link :href="route('admin.resultats.matieres.index')" :active="request()->routeIs('admin.resultats.*')" icon="fas fa-calculator" label="Moteur de Calcul" />
        
        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-6 pb-2 tracking-[0.2em]">Délibérations</p>
        <x-sidebar-link :href="route('admin.jury.annuel.index')" :active="request()->routeIs('admin.jury.*')" icon="fas fa-gavel" label="Jury de Passage" />
        <x-sidebar-link :href="route('admin.bulletins.index')" :active="request()->routeIs('admin.bulletins.*')" icon="fas fa-print" label="Bulletins de Notes" />
        <x-sidebar-link :href="route('admin.absences.index')" :active="request()->routeIs('admin.absences.*')" icon="fas fa-user-clock" label="Suivi des Absences" />

        <p x-show="sidebarOpen" class="text-[10px] font-black text-slate-500 uppercase px-3 pt-6 pb-2 tracking-[0.2em]">Sécurité & Logs</p>
        <x-sidebar-link :href="route('admin.audit.index')" :active="request()->routeIs('admin.audit.*')" icon="fas fa-fingerprint" label="Journal d'Audit" />
        <x-sidebar-link :href="route('admin.annees.index')" :active="request()->routeIs('admin.annees.*')" icon="fas fa-calendar-alt" label="Années Académiques" />
        <x-sidebar-link :href="route('admin.parametres.index')" :active="request()->routeIs('admin.parametres.*')" icon="fas fa-cogs" label="Configuration" />

    </nav>

    <div class="p-4 bg-slate-900/50 border-t border-slate-800">
        <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center p-3 bg-slate-800 text-slate-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-200 group shadow-inner">
            <svg :class="sidebarOpen ? 'rotate-0' : 'rotate-180'" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>
</aside>