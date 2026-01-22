<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .sidebar-nav ul .sidebar-item.selected > .sidebar-link, 
        .sidebar-nav ul .sidebar-item.selected > .sidebar-link.active, 
        .sidebar-nav ul .sidebar-item > .sidebar-link.active {
            background-color: #5D87FF;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-nav ul .sidebar-item.selected > .sidebar-link i,
        .sidebar-nav ul .sidebar-item.selected > .sidebar-link.active i,
        .sidebar-nav ul .sidebar-item > .sidebar-link.active i {
            color: #ffffff;
        }

        .sidebar-link {
            color: #2A3547;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover:not(.active) {
            background-color: #ECF2FF;
            color: #5D87FF;
        }
        
        .sidebar-link:hover:not(.active) i {
            color: #5D87FF;
        }

        /* Simplebar scrollbar styling if we were using it, but standard scrollbar for now */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>

<body class="bg-blue-50/20" x-data="{ sidebarOpen: false }">
    <main>
        <div id="main-wrapper" class="flex h-screen overflow-hidden">

            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Overlay for Mobile -->
            @include('layouts.partials.mobile-overlay')

            <!-- Page Content -->
            <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden bg-transparent">
                
                <!-- Header -->
                @include('layouts.partials.header')

                <!-- Scrollable Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6">
                    <div class="container mx-auto max-w-7xl">
                        @if (isset($header))
                             <div class="card bg-white rounded-xl shadow-sm border border-gray-200/60 p-5 mb-8 relative overflow-hidden">
                                <div class="relative z-10">
                                    {{ $header }}
                                </div>
                                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-50/50 to-transparent pointer-events-none"></div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                    
                     <!-- Footer -->
                    @include('layouts.partials.footer')
                </main>

            </div>
        </div>
    </main>
</body>
</html>
