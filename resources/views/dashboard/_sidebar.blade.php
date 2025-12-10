{{-- Fichier : resources/views/dashboard/_sidebar.blade.php --}}

@php
    $collaborateur = Auth::user(); 
    $userFeid = $collaborateur->feid ?? 'NKEITA07'; 
@endphp

<div class="flex flex-col h-full text-white font-medium">

    {{-- HEADER DU MENU (FEID + Icône Déconnexion) --}}
    {{-- Augmentation du padding vertical pour l'en-tête (py-6) --}}
    <div class="px-6 py-6 border-b border-white/10 flex justify-between items-center">
        
        <div class="flex-1">
            <h1 class="text-xl font-semibold tracking-wide">{{ $userFeid }}</h1>
        </div>

        {{-- Section Déconnexion (Icône seule) --}}
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" class="p-2 hover:bg-white/20 rounded-full transition duration-150" title="Déconnexion">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>

    {{-- MENU LISTE (Navigation Aérée et Stylée) --}}
    {{-- Augmentation du py-4 sur la nav principale pour l'aération verticale --}}
    <nav class="flex-1 px-4 py-4 space-y-2"> 
        
        
        {{-- Article --}}
        <a href="{{ route('adresses.index') }}"
           class="flex items-center space-x-3 py-3 px-3 rounded-lg hover:bg-white/10 transition duration-150 w-full">
            <span>Adresser un article</span>
        </a>
        
        
        {{-- zonne --}}
        <a href="{{ route('paniers.index') }}"
           class="flex items-center space-x-3 py-3 px-3 rounded-lg hover:bg-white/10 transition duration-150 w-full">
            <span>Zone d'adressage</span>
        </a>

         {{-- historique --}}
        <a href="{{ route('paniers.index') }}"
           class="flex items-center space-x-3 py-3 px-3 rounded-lg hover:bg-white/10 transition duration-150 w-full">
            <span>Historique</span>
        </a>

        <hr class="border-white/20 my-4">

       

    </nav>
</div>