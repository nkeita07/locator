<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Locator Contraintes Stock & Dynamique</title>
    <style>
        /* --- Styles Généraux --- */
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f4f4f4; 
        }
        .app-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background-color: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }
        .article-location-title { 
            font-size: 16px; 
            font-weight: bold; 
            margin-bottom: 15px; 
            color: #333; 
        }

        /* --- Barre de Recherche (Similaire aux images) --- */
        #articleSearchForm { 
            display: flex; 
            margin-bottom: 20px; 
        }
        #article_search_input { 
            flex-grow: 1; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            font-size: 16px; 
        }
        /* Cacher les boutons de recherche pour un look épuré */
        .search-area button { display: none; }

        /* --- Structure des Résultats (Tableau Grid) --- */
        .locations-grid { 
            display: grid; 
            grid-template-columns: 2fr 1fr 50px; /* Adresse | Stock | Panier */
            padding: 10px 0; 
            font-size: 14px; 
        }
        .locations-grid.header { 
            font-weight: bold; 
            color: #333; 
            border-bottom: 2px solid #333; /* Ligne de séparation sous les titres */
        }
        .location-row { 
            display: grid; 
            grid-template-columns: 2fr 1fr 50px; 
            align-items: center; 
            padding: 15px 0; 
            border-bottom: 1px solid #eee;
        }
        .zone-cell { font-weight: 500; }
        .stock-cell { justify-self: end; padding-right: 50px; } /* Pour aligner le titre STOCK */

        /* --- Contrôles de Stock (+/-) --- */
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
        .cart-cell { text-align: center; }
        .cart-cell button { 
            background: none; 
            border: none; 
            cursor: pointer; 
            font-size: 20px; 
            color: #333; 
        }
        
        /* Messages et visibilité */
        .hidden { display: none !important; }
        .alert-message {
            color: orange;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    

        <div id="search-status"></div>
        <div id="alert-message" class="alert-message hidden"></div>

        <div id="article-details-container" class="hidden">
            
            <div class="locations-grid header">
                <div class="zone-cell">ADRESSE</div>
                <div class="stock-cell">STOCK</div>
                <div class="cart-cell"></div>
            </div>

            <div id="locations-list">
                </div>

        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Déclarations des constantes du DOM
            const searchForm = document.getElementById('articleSearchForm');
            const searchInput = document.getElementById('article_search_input');
            const detailsContainer = document.getElementById('article-details-container');
            const locationsList = document.getElementById('locations-list');
            const alertMessageDiv = document.getElementById('alert-message');
            const searchStatus = document.getElementById('search-status'); 

            let articleData = {};
            
            // --- Logique de la Recherche et des Données Simuléss ---

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const reference = searchInput.value.trim();
                    if (!reference) return;

                    // 1. Masquer les messages précédents et les résultats
                    detailsContainer.classList.add('hidden');
                    alertMessageDiv.classList.add('hidden');
                    searchStatus.textContent = 'Recherche en cours...';

                    // 2. Simuler la recherche avec la référence
                    simulateArticleSearch(reference); 
                });
            }

            function simulateArticleSearch(reference) {
                let article = null;
                let emplacements = [];

                // Références et données basées sur les images fournies
                if (reference === '99') {
                    article = { reference: reference, designation: "EKIDEN ONE MAN GRIS 45" };
                    emplacements = [
                        { zone: 'C4-3', stock: 15, initialStock: 15 },
                        { zone: 'C4-4', stock: 36, initialStock: 36 },
                        { zone: 'C4-5', stock: 36, initialStock: 36 }
                    ];
                } else if (reference === '2360000') {
                    article = { reference: reference, designation: "SAC À DOS DE RANDONNÉE 50L VERT" };
                    emplacements = [
                        { zone: 'Z9-10', stock: 2, initialStock: 2 },
                        { zone: 'Z9-11', stock: 10, initialStock: 10 },
                        { zone: 'Z9-12', stock: 3, initialStock: 3 }
                    ];
                } else {
                    // Référence non trouvée
                    searchStatus.textContent = '';
                    alertMessageDiv.textContent = `Article "${reference}" non trouvé.`;
                    alertMessageDiv.classList.remove('hidden');
                    return;
                }
                
                // Calcul du stock total
                article.stock = emplacements.reduce((total, item) => total + item.stock, 0);
                articleData = article;

                // 3. Afficher les résultats
                searchStatus.textContent = '';
                renderEmplacements(emplacements);
                makeStockButtonsInteractive(); 

                // 4. Rendre le conteneur des résultats visible
                detailsContainer.classList.remove('hidden');
            }


            // --- Logique du Rendu et des Contrôles ---

            function renderEmplacements(emplacements) {
                locationsList.innerHTML = '';
                emplacements.forEach(function(item) {
                    const row = document.createElement('div');
                    row.classList.add('location-row');
                    
                    // Utilisation de data-attributes pour stocker le stock initial max
                    row.innerHTML = `
                        <div class="zone-cell">${item.zone}</div>
                        <div class="stock-control-cell" data-initial-stock="${item.initialStock}">
                            <button class="stock-btn minus-btn" data-action="decrease" data-zone="${item.zone}">-</button>
                            <span class="stock-value" data-zone-stock="${item.zone}">${item.stock}</span>
                            <button class="stock-btn plus-btn" data-action="increase" data-zone="${item.zone}">+</button>
                        </div>
                        <div class="cart-cell">
                            <button class="icon">🛒</button>
                        </div>
                    `;
                    locationsList.appendChild(row);
                    
                    // Initialiser l'état des boutons au chargement
                    updateButtonState(row);
                });
            }

            /**
             * Met à jour l'état (disabled/enabled) des boutons + et -
             * en fonction du stock actuel et du stock initial (maximal).
             */
            function updateButtonState(rowElement) {
                const controlCell = rowElement.querySelector('.stock-control-cell');
                const stockElement = rowElement.querySelector('.stock-value');
                const minusBtn = rowElement.querySelector('.minus-btn');
                const plusBtn = rowElement.querySelector('.plus-btn');

                if (!controlCell || !stockElement || !minusBtn || !plusBtn) return;
                
                const currentStock = parseInt(stockElement.textContent);
                const initialStock = parseInt(controlCell.getAttribute('data-initial-stock'));

                // Bouton MOINS (-) : Désactiver si le stock est 0 (impossible de retirer plus)
                minusBtn.disabled = (currentStock === 0);

                // Bouton PLUS (+) : Désactiver si le stock est égal au stock initial (impossible d'ajouter plus)
                plusBtn.disabled = (currentStock === initialStock);
            }


            // --- Logique de l'Interactivité et des Contraintes ---

            function makeStockButtonsInteractive() {
                // Utilisation de la délégation d'événements pour une meilleure performance
                locationsList.addEventListener('click', function(event) {
                    const button = event.target.closest('.stock-btn');
                    if (!button || button.disabled) return; // Sortir si le bouton est désactivé

                    const action = button.getAttribute('data-action');
                    const rowElement = button.closest('.location-row');
                    const stockElement = rowElement.querySelector('.stock-value');
                    const controlCell = rowElement.querySelector('.stock-control-cell');
                    
                    const initialStock = parseInt(controlCell.getAttribute('data-initial-stock'));
                    let currentStock = parseInt(stockElement.textContent);

                    alertMessageDiv.classList.add('hidden'); // Masquer l'alerte à chaque nouvelle action

                    if (action === 'increase') {
                        if (currentStock < initialStock) {
                            currentStock++;
                            articleData.stock++; 
                        } else {
                            // Alerte de dépassement de stock initial (ne devrait pas arriver si le bouton est disabled)
                            alertMessageDiv.textContent = `Alerte : Le stock de ${rowElement.querySelector('.zone-cell').textContent} a atteint son maximum initial (${initialStock}).`;
                            alertMessageDiv.classList.remove('hidden');
                            return;
                        }
                    } else if (action === 'decrease' && currentStock > 0) {
                        currentStock--;
                        articleData.stock--; 
                    } else {
                        return;
                    }

                    // 1. Mise à jour du stock affiché
                    stockElement.textContent = currentStock; 
                    
                    // 2. Mettre à jour l'état des boutons de la ligne concernée
                    updateButtonState(rowElement);
                });
            }
        });
    </script>

</body>
</html>