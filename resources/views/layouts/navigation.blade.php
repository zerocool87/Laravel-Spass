<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(Auth::user()->isElu() || Auth::user()->isAdmin())
                            <x-nav-link :href="route('elus.dashboard')" :active="request()->routeIs('elus.*')">
                                {{ __('Espace Élus') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
