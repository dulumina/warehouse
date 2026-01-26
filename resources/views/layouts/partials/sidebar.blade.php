<aside id="application-sidebar-brand"
    class="fixed inset-y-0 left-0 z-50 w-[270px] bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out transform -translate-x-full xl:translate-x-0 xl:static xl:block shrink-0 shadow-sm"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'">

    <!-- Logo -->
    <div class="px-6 py-5 flex items-center justify-between h-[70px]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-nowrap">
            <div class="bg-blue-50 text-blue-600 p-1.5 rounded-lg">
                <i class="text-2xl ti ti-building-skyscraper"></i>
            </div>
            <span
                class="text-xl font-extrabold tracking-tight text-gray-800">{{ config('app.name') ?? 'Laravel' }}</span>
        </a>
        <button @click="sidebarOpen = false" class="block text-gray-500 transition-colors xl:hidden hover:text-blue-600">
            <i class="text-2xl ti ti-x"></i>
        </button>
    </div>

    <div class="h-[calc(100vh-70px)] overflow-y-auto overflow-x-hidden px-6 pb-6" data-simplebar>
        <nav class="flex flex-col w-full mt-6 sidebar-nav">
            <ul id="sidebarnav" class="p-0 m-0 space-y-1 text-sm text-gray-600 list-none">

                <!-- HOME Section -->
                <li class="px-4 pb-2 mt-2 text-xs font-bold text-gray-400 uppercase">
                    <span>HOME</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link gap-3 py-3 px-4 rounded-lg w-full flex items-center font-medium {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="text-xl ti ti-layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Dynamic & Settings Section -->
                @php
                    $settings = $navItems->where('dropdown', 'Settings');
                    $others = $navItems->where('dropdown', '!=', 'Settings')->where('route', '!=', 'dashboard');
                @endphp

                @if ($others->count() > 0)
                    <li class="px-4 pt-6 pb-2 text-xs font-bold text-gray-400 uppercase">
                        <span>APPS</span>
                    </li>
                    @foreach ($others as $item)
                        <li class="sidebar-item">
                            <a class="sidebar-link gap-3 py-3 px-4 rounded-lg w-full flex items-center font-medium {{ $item['active'] ? 'active' : '' }}"
                                href="{{ route($item['route']) }}">
                                <i class="ti {{ $item['icon'] ?? 'ti-circle' }} text-xl"></i>
                                <span>{{ $item['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif

                @if ($settings->count() > 0)
                    <li class="px-4 pt-6 pb-2 text-xs font-bold text-gray-400 uppercase">
                        <span>SETTINGS</span>
                    </li>

                    <li class="sidebar-item" x-data="{ open: {{ request()->is('settings*') || request()->is('users*') || request()->is('roles*') || request()->is('permissions*') ? 'true' : 'false' }} }">
                        <a href="javascript:void(0)" @click="open = !open"
                            class="flex items-center justify-between w-full gap-3 px-4 py-3 font-medium rounded-lg sidebar-link hover:bg-transparent">
                            <span class="flex items-center gap-3">
                                <i class="text-xl ti ti-settings"></i>
                                <span class="hide-menu">Management</span>
                            </span>
                            <i class="text-lg transition-transform duration-200 ti ti-chevron-down"
                                :class="{ 'rotate-180': open }"></i>
                        </a>

                        <ul x-show="open" x-collapse class="pl-0 mt-1 space-y-1">
                            @foreach ($settings as $item)
                                <li class="sidebar-item">
                                    <a href="{{ route($item['route']) }}"
                                        class="sidebar-link gap-2 py-2.5 px-4 pl-12 rounded-lg w-full flex items-center font-medium {{ $item['active'] ? 'text-blue-600 bg-transparent' : '' }}">
                                        <i
                                            class="ti {{ $item['active'] ? 'ti-circle-filled' : 'ti-circle' }} text-[10px]"></i>
                                        <span
                                            class="{{ $item['active'] ? 'font-semibold' : '' }}">{{ $item['name'] }}</span>
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
