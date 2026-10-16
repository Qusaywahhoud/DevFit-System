<nav x-data="{ open: false }" class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" dir="rtl">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-gray-200">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown & Language Switcher (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">

                <!-- زر تبديل اللغة فقط -->
                <div class="flex items-center">
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-xl transition shadow-sm">
                            English 🇬🇧
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-xl transition shadow-sm">
                            العربية 🇸🇾
                        </a>
                    @endif
                </div>

                <!-- قائمة اسم المستخدم المنسدلة (تحتوي على زر الثيم الوحيد) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-900 hover:text-gray-700 dark:hover:text-gray-100 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <!-- زر تبديل الثيم المباشر داخل القائمة المنسدلة -->
                        @php 
                            $currentTheme = session('app_theme') ?? request()->cookie('app_theme', 'light'); 
                        @endphp
                        <x-dropdown-link :href="route('theme.switch', $currentTheme === 'dark' ? 'light' : 'dark')">
                            {{ $currentTheme === 'dark' ? '☀️ ' . (app()->getLocale() == 'ar' ? 'الوضع الصباحي' : 'Light Mode') : '🌙 ' . (app()->getLocale() == 'ar' ? 'الوضع الليلي' : 'Dark Mode') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Log Out' }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-gray-200">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-800">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>

                <!-- Language Switcher (Mobile) -->
                <div class="flex items-center gap-2">
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg shadow-sm">
                            English 🇬🇧
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg shadow-sm">
                            العربية 🇸🇾
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @php 
                    $currentTheme = session('app_theme') ?? request()->cookie('app_theme', 'light'); 
                @endphp
                <x-responsive-nav-link :href="route('theme.switch', $currentTheme === 'dark' ? 'light' : 'dark')">
                    {{ $currentTheme === 'dark' ? '☀️ ' . (app()->getLocale() == 'ar' ? 'الوضع الصباحي' : 'Light Mode') : '🌙 ' . (app()->getLocale() == 'ar' ? 'الوضع الليلي' : 'Dark Mode') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-gray-800 dark:text-gray-200">
                        {{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Log Out' }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
