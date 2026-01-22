<header class="w-full bg-white/80 backdrop-blur-md shadow-sm xl:static z-30 border-b border-gray-100/50 sticky top-0">
    <nav class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="xl:hidden block text-gray-500 hover:text-blue-600 transition-colors">
                <i class="ti ti-menu-2 text-2xl"></i>
            </button>
            
            <!-- Search (Mockup) -->
            <div class="hidden md:flex items-center gap-2 text-gray-400 bg-transparent">
                <i class="ti ti-search text-xl"></i>
                <span class="text-sm">Search...</span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Notifications -->
            <button class="relative p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors group">
                <i class="ti ti-bell text-xl"></i>
                <span class="absolute top-2 right-2 flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
            </button>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-3 focus:outline-none hover:bg-blue-50 p-1.5 rounded-full transition-colors border border-transparent hover:border-blue-100">
                    <img class="w-9 h-9 rounded-full object-cover ring-2 ring-white shadow-sm" 
                         src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=007bff&color=fff" 
                         alt="{{ Auth::user()->name }}">
                </button>

                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-3 w-64 bg-white rounded-xl shadow-lg border border-gray-100 py-0 overflow-hidden origin-top-right z-50 animate-in fade-in zoom-in duration-200">
                    <div class="px-6 py-4 bg-blue-50/50 border-b border-gray-100">
                        <h5 class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</h5>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="ti ti-user text-lg"></i> My Profile
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="ti ti-mail text-lg"></i> My Account
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            <i class="ti ti-list-check text-lg"></i> My Tasks
                        </a>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center px-4 py-2.5 text-sm font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
