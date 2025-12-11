<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Product Locator')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root {
            --blue: #2b3db8;
            --blue-dark: #1f2e8f;
            --sidebar-width: 15rem;
        }

        body {
            background: #f5f6fa;
            overflow-x: hidden;
        }

        /* ----- SIDEBAR ----- */
        #sidebar {
            width: var(--sidebar-width);
            background: var(--blue);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            z-index: 50;
            transform: translateX(0);
            transition: transform .3s ease;
        }

        #sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem .75rem;
            background: rgba(255,255,255,0.10);
            border-radius: 8px;
            font-weight: 500;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.18);
        }

        /* HEADER */
        #header {
            height: 64px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 40;
        }

        /* CONTENT */
        #main-content {
            margin-left: var(--sidebar-width);
            padding: 1rem;
            transition: margin-left .3s ease;
        }

        @media (max-width: 768px) {
            #main-content {
                margin-left: 0;
            }
        }

        /* HAMBURGER */
        .menu-btn {
            font-size: 1.7rem;
            cursor: pointer;
            padding: .5rem;
            border-radius: 6px;
        }

        .menu-btn:hover {
            background: #eee;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div id="sidebar" class="hidden md:block">
        <h1 class="font-bold text-xl mb-4">{{ Auth::user()->matricule ?? 'USER' }}</h1>

        <a href="{{ route('article.location') }}" class="sidebar-link">📦 Adresser un article</a>
        <a href="{{ route('zones.index') }}" class="sidebar-link">📍 Zones</a>
        <a href="{{ route('historique.index') }}" class="sidebar-link">📜 Historique</a>
    </div>

    <!-- HEADER -->
    <div id="header">
        <span id="menu-toggle" class="menu-btn md:hidden">☰</span>
        <img src="{{ asset('images/logo.jpg') }}" class="h-8">
    </div>

    <!-- CONTENT -->
    <div id="main-content">
        @yield('content')
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('menu-toggle');

        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
