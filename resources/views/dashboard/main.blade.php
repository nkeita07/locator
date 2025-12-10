{{-- Fichier : resources/views/dashboard/main.blade.php (FINAL) --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Locator | @yield('title', 'Dashboard')</title> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Styles nécessaires pour l'animation */
        .sidebar-bg { background-color: #3e51b5; } 
        .transition-width { transition: width 0.3s ease-in-out; } 
        /* Style pour les petits drapeaux */
        .flag-icon { 
            height: 1.75rem; /* Légèrement plus grand */
            width: 2.625rem; 
            border: 1px solid rgba(0,0,0,0.1); 
            border-radius: 4px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden; 
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    
    <div class="flex h-screen overflow-hidden">
        
        {{-- BLOC 1: Barre Latérale --}}
        <div id="sidebar-container" 
             class="w-64 sidebar-bg text-white flex flex-col shadow-lg flex-shrink-0 transition-width duration-300 ease-in-out p-4">
            @include('dashboard._sidebar')
        </div>

        {{-- BLOC 2: Contenu Principal --}}
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300"> 
            
            {{-- Entête avec le bouton Hamburger et les infos --}}
            <header class="flex justify-between items-center p-4 bg-white border-b shadow-sm text-sm">
                
                {{-- 1. Groupe de gauche (Menu + Infos Magasin) --}}
                <div class="flex items-center space-x-4 flex-shrink-0">
                    {{-- Bouton Menu Hamburger --}}
                    <button id="menu-toggle" class="p-2 rounded hover:bg-gray-100">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    {{-- Informations Magasin/FEID --}}
                    @include('dashboard._header')
                </div>


               
                
                {{-- 3. Logo Decathlon (Aligné à droite) --}}
                <div class="flex items-center flex-shrink-0">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Decathlon Logo" class="h-8">
                </div>
            </header>

            {{-- Corps de la Page (Zone de Recherche/Gestion) --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6"> 
                
                {{-- Conteneur centré --}}
                <div class="max-w-7xl mx-auto"> 
                    
                    {{-- ESPACE RÉSERVÉ AU CONTENU DYNAMIQUE --}}
                    @yield('content') 
                </div>

            </main>
        </div>
    </div>
    
    @include('dashboard._scripts') 
    @stack('scripts') 

</body>
</html>