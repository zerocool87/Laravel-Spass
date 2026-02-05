<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <!-- Logo and label removed as requested -->
                </div>

                <!-- Navigation Links -->
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

            <!-- Settings Dropdown - Removed completely, now only in Elus header -->
            <!-- Hamburger - Removed since responsive menu is no longer needed -->
        </div>
    </div>

    <!-- Responsive Navigation Menu - Removed completely, now only in Elus header -->
</nav>
