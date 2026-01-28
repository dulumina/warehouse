<header class="sticky top-0 z-30 w-full border-b shadow-sm bg-white/80 backdrop-blur-md xl:static border-gray-100/50">
    <nav class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="block text-gray-500 transition-colors xl:hidden hover:text-blue-600">
                <i class="text-2xl ti ti-menu-2"></i>
            </button>

            <!-- Search (Mockup) -->
            {{-- <div class="items-center hidden gap-2 text-gray-400 bg-transparent md:flex">
                <i class="text-xl ti ti-search"></i>
                <span class="text-sm">Search...</span>
            </div> --}}
        </div>

        <div class="flex items-center gap-4">
            <!-- Notifications -->
            <button
                class="relative p-2 text-gray-500 transition-colors rounded-full hover:text-blue-600 hover:bg-blue-50 group">
                <i class="text-xl ti ti-bell"></i>
                <span class="absolute flex w-2 h-2 top-2 right-2">
                    <span
                        class="absolute inline-flex w-full h-full bg-blue-400 rounded-full opacity-75 animate-ping"></span>
                    <span class="relative inline-flex w-2 h-2 bg-blue-500 rounded-full"></span>
                </span>
            </button>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-3 focus:outline-none hover:bg-blue-50 p-1.5 rounded-full transition-colors border border-transparent hover:border-blue-100">
                    {{-- <img class="object-cover rounded-full shadow-sm w-9 h-9 ring-2 ring-white"
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=007bff&color=fff"
                        alt="{{ Auth::user()->name }}"> --}}
                    <div class="flex flex-col items-start">
                        <span class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-500">{{ Auth::user()->role }}</span>
                    </div>
                    <i class="text-lg ti ti-chevron-down"></i>
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 z-50 w-64 py-0 mt-3 overflow-hidden duration-200 origin-top-right bg-white border border-gray-100 shadow-lg rounded-xl animate-in fade-in zoom-in">
                    <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50">
                        <h5 class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</h5>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="text-lg ti ti-user"></i> My Profile
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="text-lg ti ti-mail"></i> My Account
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="text-lg ti ti-list-check"></i> My Tasks
                        </a>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-center px-4 py-2.5 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
