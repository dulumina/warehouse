<aside id="application-sidebar-brand"
    class="fixed inset-y-0 left-0 z-50 w-[270px] bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out transform -translate-x-full xl:translate-x-0 xl:static xl:block shrink-0 shadow-sm"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'">
    
    <!-- Logo -->
    <div class="px-6 py-5 flex items-center justify-between h-[70px]">
        <a href="{{ route('dashboard') }}" class="text-nowrap flex items-center gap-3">
            <div class="bg-blue-50 text-blue-600 p-1.5 rounded-lg">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
                <span class="text-xl font-extrabold text-gray-800 tracking-tight">Laravel</span>
        </a>
        <button @click="sidebarOpen = false" class="xl:hidden block text-gray-500 hover:text-blue-600 transition-colors">
            <i class="ti ti-x text-2xl"></i>
        </button>
    </div>

    <div class="h-[calc(100vh-70px)] overflow-y-auto overflow-x-hidden px-6 pb-6" data-simplebar>
        <nav class="mt-6 w-full flex flex-col sidebar-nav">
            <ul id="sidebarnav" class="text-gray-600 text-sm list-none p-0 m-0 space-y-1">
                
                <!-- HOME Section -->
                <li class="text-xs font-bold pb-2 uppercase text-gray-400 mt-2 px-4">
                    <span>HOME</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-3 px-4 rounded-lg w-full flex items-center font-medium {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="ti ti-layout-dashboard text-xl"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Dynamic & Settings Section -->
                    @php
                    $settings = $navItems->where('dropdown', 'Settings');
                    $others = $navItems->where('dropdown', '!=', 'Settings')->where('route', '!=', 'dashboard');
                @endphp

                @if($others->count() > 0)
                    <li class="text-xs font-bold pb-2 pt-6 uppercase text-gray-400 px-4">
                        <span>APPS</span>
                    </li>
                    @foreach($others as $item)
                        <li class="sidebar-item">
                            <a class="sidebar-link gap-3 py-3 px-4 rounded-lg w-full flex items-center font-medium {{ $item['active'] ? 'active' : '' }}"
                                href="{{ route($item['route']) }}">
                                <i class="ti {{ $item['icon'] ?? 'ti-circle' }} text-xl"></i>
                                <span>{{ $item['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif

                @if($settings->count() > 0)
                    <li class="text-xs font-bold pb-2 pt-6 uppercase text-gray-400 px-4">
                        <span>SETTINGS</span>
                    </li>

                    <li class="sidebar-item" x-data="{ open: {{ request()->is('settings*') || request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'true' : 'false' }} }">
                        <a href="javascript:void(0)" @click="open = !open" 
                            class="sidebar-link gap-3 py-3 px-4 rounded-lg w-full flex items-center justify-between font-medium hover:bg-transparent">
                            <span class="flex gap-3 items-center">
                                <i class="ti ti-settings text-xl"></i>
                                <span class="hide-menu">Management</span>
                            </span>
                            <i class="ti ti-chevron-down text-lg transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </a>
                        
                        <ul x-show="open" x-collapse class="pl-0 mt-1 space-y-1">
                            @foreach($settings as $item)
                                <li class="sidebar-item">
                                    <a href="{{ route($item['route']) }}" 
                                        class="sidebar-link gap-2 py-2.5 px-4 pl-12 rounded-lg w-full flex items-center font-medium {{ $item['active'] ? 'text-blue-600 bg-transparent' : '' }}">
                                        <i class="ti {{ $item['active'] ? 'ti-circle-filled' : 'ti-circle' }} text-[10px]"></i>
                                        <span class="{{ $item['active'] ? 'font-semibold' : '' }}">{{ $item['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
