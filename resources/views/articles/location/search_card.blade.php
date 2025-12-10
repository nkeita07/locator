{{-- Fichier : resources/views/articles/location/search_card.blade.php --}}

<style>
    /* VARIABLES GLOBALES (Pour la cohérence avec votre design) */
    :root {
        --decathlon-blue: #3643ba;
        --gradient-blue: linear-gradient(135deg, #3643ba 0%, #3643ba 100%);
    }

    /* Style de la carte principale (Similaire au glass-card précédent) */
    .modern-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    /* Titre */
    .card-title {
        font-size: 1.75rem; /* Légèrement plus grand que 2xl */
        font-weight: 800; /* Plus de gras */
        color: #1f2937;
        margin-bottom: 2rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid #f3f4f6;
    }

    /* Champ de Saisie Stylisé */
    .styled-input {
        width: 100%;
        padding: 1.5rem; /* Rendre l'input plus grand */
        font-size: 1.5rem; /* Grosse police pour la lecture */
        font-weight: 700;
        text-align: center;
        border: 3px solid #e5e7eb;
        border-radius: 16px;
        background: white;
        transition: all 0.3s ease;
        color: #1f2937;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .styled-input:focus {
        outline: none;
        /* Ajout de l'effet de focus bleu de Decathlon */
        border-color: var(--decathlon-blue);
        box-shadow: 0 0 0 4px rgba(0, 130, 195, 0.1), 0 8px 24px rgba(0, 130, 195, 0.15);
        transform: translateY(-1px);
    }
    
    .styled-input::placeholder {
        color: #9ca3af;
        font-weight: 500;
        font-size: 1.125rem; /* Taille de placeholder plus discrète */
        letter-spacing: 0.05em;
    }

    /* Conteneur pour centrer et limiter la largeur du champ */
    .search-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Centrage du texte d'instruction */
    .instruction-text {
        text-align: center;
        color: #6b7280;
        font-size: 1rem;
        margin-top: 1.5rem;
    }
</style>

{{-- L'utilisation de max-w-4xl permet d'adapter cette carte à une taille raisonnable --}}
<div class="max-w-4xl mx-auto py-6 px-4"> 

    <div class="modern-card">
        {{-- Nouveau Titre Stylisé --}}
        <h1 class="card-title">📍Article location</h1> 

        {{-- 1. BLOC DE RECHERCHE --}}
        <form id="articleSearchForm" class="relative mb-8 search-container">
            
            {{-- Champ de Saisie --}}
            <input type="text" 
                placeholder="SAISIR LE CODE ARTICLE..."
                id="article_search_input"
                class="styled-input" {{-- Application de la nouvelle classe CSS --}}
                required>
            
        </form>
        

    </div>
    
</div>