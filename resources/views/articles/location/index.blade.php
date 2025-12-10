@extends('dashboard.main') 

@section('title', 'Article Location')

@section('content')

    {{-- ************************************************** --}}
    {{-- INJECTION DES STYLES SPÉCIFIQUES POUR LA PAGE ARTICLE --}}
    {{-- ************************************************** --}}
    {{-- Normalement, ces styles devraient être dans votre app.css ou Tailwind, mais nous les injectons ici pour la simulation des contraintes de stock et des messages --}}
    <style>
        /* --- Styles du Script.blade.php Fusionnés --- */

        /* --- CONTROLES DE STOCK ET COULEURS --- */
        .stock-control-cell { 
            display: flex; 
            justify-content: flex-start; 
            align-items: center; 
            gap: 5px; 
        }
        .stock-control-cell button { 
            background-color: white; 
            border: 1px solid #007bff; /* Bleu Decathlon */
            color: #007bff; 
            width: 30px; 
            height: 30px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-weight: bold; 
            line-height: 1; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            transition: background-color 0.1s; 
        }
        .stock-control-cell button:active:not(:disabled) { 
            background-color: #007bff; 
            color: white; 
        }
        /* Style pour désactiver les boutons */
        .stock-control-cell button:disabled {
            border-color: #ccc;
            color: #ccc;
            cursor: not-allowed;
            background-color: #f8f8f8;
        }
        
        .stock-value { 
            min-width: 25px; 
            text-align: center; 
        }

        /* Messages et visibilité */
        .hidden { display: none !important; }
        .alert-message {
            color: orange;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>

    {{-- 1. CARTE DE RECHERCHE PRINCIPALE --}}
    <div class="mb-6">
        @include('articles.location.search_card') 
    </div>
    
    {{-- 2. BLOC DES RÉSULTATS (Masqué au chargement) --}}
    <div class="mt-8 hidden" id="article-details-container"> 
        @include('articles.location.results_card') 
        
        {{-- Conteneurs pour les messages d'état/alertes --}}
        <div id="search-status"></div>
        {{-- Conteneur pour l'alerte de dépassement de stock --}}
        <div id="alert-message" class="alert-message hidden p-4 bg-orange-100 text-orange-700 rounded-md"></div>
    </div>
    
    {{-- Message d'attente initial (Visible au chargement) --}}
   
    
    {{-- ************************************************** --}}
    {{-- LOGIQUE JAVASCRIPT --}}
    {{-- ************************************************** --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- DÉCLARATIONS DES ÉLÉMENTS DU DOM ---
            const articleSearchForm = document.getElementById('articleSearchForm');
            const articleSearchInput = document.getElementById('article_search_input');
            const locationsList = document.getElementById('locations-list');
            const resultsContainer = document.getElementById('article-details-container'); 
            const alertMessageDiv = document.getElementById('alert-message'); 
            const initialMessage = document.getElementById('initial-message'); 
            
            let currentArticleData = {};

            // --- FONCTION PRINCIPALE DE RECHERCHE (SIMULÉE) ---
            function simulateArticleSearch(reference) {
                let emplacements = [];
                
                // Masquer les messages d'état et vider le conteneur des résultats
                initialMessage.classList.add('hidden');
                alertMessageDiv.classList.add('hidden');
                locationsList.innerHTML = ''; 
                resultsContainer.classList.add('hidden');
                
                // --- LOGIQUE DE SIMULATION DES DONNÉES ---
                if (reference === '99' || reference === 'EKIDEN ONE MAN GRIS 45') {
                    emplacements = [
                        { zone: 'C4-3', stock: 15, initialStock: 15 },
                        { zone: 'C4-4', stock: 36, initialStock: 36 },
                        { zone: 'C4-5', stock: 36, initialStock: 36 }
                    ];
                } else if (reference === '2360000') {
                    emplacements = [
                        { zone: 'Z9-10', stock: 2, initialStock: 50 },
                        { zone: 'Z9-11', stock: 10, initialStock: 30 },
                        { zone: 'Z9-12', stock: 3, initialStock: 90 }
                    ];
                } else {
                    locationsList.innerHTML = '<p class="p-4 text-center text-red-500">Article non trouvé. Veuillez scanner ou entrer une référence valide.</p>';
                    resultsContainer.classList.remove('hidden'); 
                    return;
                }
                
                currentArticleData.stock = emplacements.reduce((total, item) => total + item.stock, 0);

                renderEmplacements(emplacements);
                makeStockButtonsInteractive();
                
                // Afficher le conteneur des résultats
                resultsContainer.classList.remove('hidden');
            }

            // --- FONCTIONS DE RENDU ET D'INTERACTIVITÉ ---
            
            // 1. Rendu du stock par emplacement
            function renderEmplacements(emplacements) {
                locationsList.innerHTML = '';
                emplacements.forEach(item => {
                    const row = document.createElement('div');
                    // Utilisation des classes Tailwind et de la classe 'stock-control-cell'
                    row.className = 'location-row grid grid-cols-3 divide-x divide-gray-100 items-center border-b border-gray-100'; 
                    
                    row.innerHTML = `
                        <div class="px-6 py-4 text-sm font-medium text-gray-900">${item.zone}</div>
                        <div class="flex justify-start items-center space-x-2 px-6 py-4 stock-control-cell" data-initial-stock="${item.initialStock}">
                            <button class="stock-btn minus-btn" data-action="decrease" data-zone="${item.zone}">-</button>
                            <span class="stock-value w-8 text-center" data-zone-stock="${item.zone}">${item.stock}</span>
                            <button class="stock-btn plus-btn" data-action="increase" data-zone="${item.zone}">+</button>
                        </div>
                        <div class="px-6 py-4 text-center">
                            <button class="text-gray-500 hover:text-blue-600">🛒</button>
                        </div>
                    `;
                    locationsList.appendChild(row);
                    updateButtonState(row); 
                });
            }

            // 2. Gestion des clics sur les boutons de stock (avec contraintes et alerte)
            function makeStockButtonsInteractive() {
                // S'assure que l'écouteur est ajouté une seule fois (via délégation)
                locationsList.removeEventListener('click', handleStockButtonClick);
                locationsList.addEventListener('click', handleStockButtonClick);
            }
            
            function handleStockButtonClick(event) {
                const button = event.target.closest('.stock-btn');
                if (!button) return;

                const action = button.getAttribute('data-action');
                const rowElement = button.closest('.location-row');
                const stockElement = rowElement.querySelector('.stock-value');
                // Ciblage de la cellule de contrôle pour récupérer l'initial stock
                const controlCell = rowElement.querySelector('.stock-control-cell'); 
                const zone = rowElement.querySelector('.zone-cell').textContent;
                
                const initialStock = parseInt(controlCell.getAttribute('data-initial-stock'));
                let currentStock = parseInt(stockElement.textContent);
                
                alertMessageDiv.classList.add('hidden');
                
                if (action === 'increase') {
                    if (currentStock < initialStock) {
                        currentStock++;
                    } else {
                        // ALERTE MAX ATTEINT
                        alertMessageDiv.textContent = `Attention : Le stock de l'emplacement ${zone} est déjà au maximum initial (${initialStock}).`;
                        alertMessageDiv.classList.remove('hidden');
                        return; 
                    }
                } else if (action === 'decrease') {
                    if (currentStock > 0) {
                        currentStock--;
                    } else {
                        // ALERTE MIN ATTEINT
                        alertMessageDiv.textContent = `Attention : Le stock de l'emplacement ${zone} est déjà à zéro (0).`;
                        alertMessageDiv.classList.remove('hidden');
                        return; 
                    }
                }

                stockElement.textContent = currentStock; 
                updateButtonState(rowElement);
            }

            // 3. Mise à jour de l'état (disabled) des boutons
            function updateButtonState(rowElement) {
                // Ciblage de la cellule de contrôle
                const controlCell = rowElement.querySelector('.stock-control-cell');
                if (!controlCell) return;

                const stockElement = controlCell.querySelector('.stock-value');
                const minusBtn = controlCell.querySelector('.minus-btn');
                const plusBtn = controlCell.querySelector('.plus-btn');

                const initialStock = parseInt(controlCell.getAttribute('data-initial-stock'));
                const currentStock = parseInt(stockElement.textContent);

                minusBtn.disabled = (currentStock === 0);
                plusBtn.disabled = (currentStock === initialStock);
            }

            // --- GESTION DE LA RECHERCHE ET DÉCLENCHEURS ---
            
            // 1. Déclencheur de recherche lors de la soumission du formulaire
            if (articleSearchForm) {
                articleSearchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const reference = articleSearchInput.value.trim();
                    if (reference) {
                        simulateArticleSearch(reference);
                    }
                });
            }
            
            // 2. Déclencheur sur la touche Entrée dans le champ de saisie (pour le scan)
            if (articleSearchInput) {
                articleSearchInput.addEventListener('keyup', function(e) {
                    const reference = articleSearchInput.value.trim();
                    
                    if (e.key === 'Enter' && reference.length > 0) {
                        simulateArticleSearch(reference);
                        articleSearchInput.blur(); 
                        e.preventDefault();
                    }
                });
            }

            // 3. Afficher le message d'attente initial (Visible au chargement)
            initialMessage.classList.remove('hidden');
        });
    </script>
    @endpush
@endsection