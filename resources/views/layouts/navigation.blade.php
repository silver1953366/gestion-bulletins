<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-fuchsia-600" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <span class="font-black text-xs uppercase tracking-widest">{{ __('Tableau de Bord') }}</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('admin.etudiants.index')" :active="request()->routeIs('admin.etudiants.*')">
                        <span class="font-bold text-sm">{{ __('Étudiants') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('admin.bulletins.index')" :active="request()->routeIs('admin.bulletins.*')">
                        <span class="font-bold text-sm">{{ __('Bulletins') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('admin.parametres.index')" :active="request()->routeIs('admin.parametres.*')">
                        <span class="font-bold text-sm">{{ __('Paramètres') }}</span>
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-bold rounded-xl text-gray-700 bg-gray-50 hover:bg-fuchsia-50 hover:text-fuchsia-600 transition ease-in-out duration-150 focus:outline-none">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="block px-4 py-2 text-xs text-gray-400 uppercase font-black tracking-widest">
                            Administration
                        </div>

                        <x-dropdown-link :href="route('admin.users.index')">
                            {{ __('Gestion du personnel') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('admin.parametres.index')">
                            {{ __('Configuration Système') }}
                        </x-dropdown-link>

                        <hr class="border-gray-100 my-1">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 font-bold hover:bg-red-50">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                {{ __('Tableau de Bord') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('admin.etudiants.index')" :active="request()->routeIs('admin.etudiants.*')">
                {{ __('Étudiants') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.bulletins.index')" :active="request()->routeIs('admin.bulletins.*')">
                {{ __('Bulletins') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('admin.parametres.index')" :active="request()->routeIs('admin.parametres.*')">
                {{ __('Paramètres Système') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-black text-base text-fuchsia-600">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Gestion du personnel') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-600 font-bold">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>