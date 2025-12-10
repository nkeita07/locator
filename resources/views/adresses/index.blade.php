@extends('dashboard.main')

@section('title', 'Adresser un article')

@section('content')

<style>
:root {
    --decathlon-blue: #3643ba;
    --decathlon-green: #00B388;
    --error-red: #E31B23;
}

.glass-card {
    background: white;
    padding: 1.8rem;
    border-radius: 14px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}

.status-message {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    margin-top: 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-success { background: #d1fae5; color: #065f46; }
.status-error   { background: #fee2e2; color: #991b1b; }
.status-info    { background: #dbeafe; color: #1e40af; }

.btn {
    padding: 0.9rem 1rem;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    width: 100%;
    text-align: center;
}

.btn-blue  { background: var(--decathlon-blue); color: white; }
.btn-green { background: var(--decathlon-green); color: white; }

.input-primary, .input-secondary {
    width: 100%;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.8rem 0.9rem;
    font-size: 0.95rem;
    font-weight: 500;
}

.input-primary:focus, .input-secondary:focus {
    outline: none;
    border-color: var(--decathlon-blue);
}

.label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
    display: block;
}

.suggestions-dropdown {
    background: white;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-top: 0.3rem;
    display: none;
    max-height: 180px;
    overflow-y: auto;
    z-index: 30;
    position: absolute;
    width: 100%;
}

.suggestion-item {
    padding: 0.6rem 0.9rem;
    cursor: pointer;
    font-size: 0.9rem;
}
.suggestion-item:hover { background: #eef2ff; }

.zone-badge {
    background: var(--decathlon-blue);
    color: white;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-zone-existing {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    background: #eef2ff;
    color: #1f2937;
    font-size: 0.8rem;
    margin-right: 0.35rem;
    margin-bottom: 0.3rem;
}
</style>

<div class="max-w-5xl mx-auto space-y-6">

    {{-- 1️⃣ RECHERCHE ARTICLE --}}
    <div class="glass-card">
        <h2 class="font-bold text-xl mb-4">1️⃣ Recherche de l'article</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            {{-- Colonne gauche : formulaire recherche --}}
            <div class="col-span-1 space-y-3">
                <div>
                    <label class="label">Référence article</label>
                    <input id="refInput"
                           type="text"
                           class="input-primary"
                           placeholder="Ex : 608629">
                </div>

                <button id="searchBtn" class="btn btn-blue">
                    Chercher
                </button>

                <div id="articleStatus"></div>
            </div>

            {{-- Colonne droite : info produit --}}
            <div class="col-span-2 flex gap-4 items-start" id="articleCard" style="display:none;">
                <img id="articleImage"
                     class="w-28 h-28 rounded shadow object-cover flex-shrink-0"
                     src=""
                     alt="Image article">

                <div class="space-y-1">
                    <p class="text-sm text-gray-500">Référence : <span id="articleRef" class="font-semibold"></span></p>
                    <p class="text-lg font-bold text-gray-900" id="articleName"></p>
                    <p class="text-sm text-gray-600">
                        Stock total : <span id="articleStock" class="font-semibold"></span>
                    </p>

                    <div class="mt-2">
                        <p class="text-xs text-gray-500 mb-1">Zones déjà adressées :</p>
                        <div id="zonesList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2️⃣ CHOIX DE LA ZONE --}}
    <div class="glass-card" id="zoneCard" style="display:none;">
        <h2 class="font-bold text-xl mb-4">2️⃣ Zone de destination</h2>

        <div class="relative">
            <label class="label">Zone</label>
            <input id="zoneInput"
                   type="text"
                   class="input-secondary"
                   placeholder="Ex : A1-2"
                   autocomplete="off">

            <div id="zoneSuggestions" class="suggestions-dropdown"></div>
        </div>

        <button id="validateZoneBtn" class="btn btn-blue mt-3">
            Valider la zone
        </button>

        <div id="zoneStatus"></div>
    </div>

    {{-- 3️⃣ DÉPÔT --}}
    <div class="glass-card" id="depositCard" style="display:none;">
        <h2 class="font-bold text-xl mb-4">3️⃣ Dépôt de marchandise</h2>

        <div class="status-info mb-3">
            Zone sélectionnée :
            <span id="selectedZone" class="zone-badge"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-1">
                <label class="label">Quantité à déposer</label>
                <input id="qtyInput"
                       type="number"
                       value="1"
                       min="1"
                       class="input-secondary">
            </div>

            <div class="md:col-span-2">
                <button id="depositBtn" class="btn btn-green">
                    Confirmer le dépôt
                </button>
            </div>
        </div>

        <div id="depositStatus"></div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const API = "{{ url('/api') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token']")?.content || '';

    // DOM
    const refInput       = document.getElementById("refInput");
    const searchBtn      = document.getElementById("searchBtn");
    const articleStatus  = document.getElementById("articleStatus");
    const articleCard    = document.getElementById("articleCard");
    const articleName    = document.getElementById("articleName");
    const articleRef     = document.getElementById("articleRef");
    const articleStock   = document.getElementById("articleStock");
    const articleImage   = document.getElementById("articleImage");
    const zonesList      = document.getElementById("zonesList");

    const zoneCard       = document.getElementById("zoneCard");
    const zoneInput      = document.getElementById("zoneInput");
    const zoneSuggestions= document.getElementById("zoneSuggestions");
    const validateZoneBtn= document.getElementById("validateZoneBtn");
    const zoneStatus     = document.getElementById("zoneStatus");

    const depositCard    = document.getElementById("depositCard");
    const selectedZone   = document.getElementById("selectedZone");
    const qtyInput       = document.getElementById("qtyInput");
    const depositBtn     = document.getElementById("depositBtn");
    const depositStatus  = document.getElementById("depositStatus");

    let currentArticle = null;

    function clearMessages() {
        articleStatus.innerHTML = "";
        zoneStatus.innerHTML = "";
        depositStatus.innerHTML = "";
    }

    /* ========================
       1️⃣ RECHERCHE ARTICLE
       ======================== */
    async function fetchArticle() {
        clearMessages();
        const ref = refInput.value.trim();

        if (ref.length < 3) {
            articleStatus.innerHTML = `<div class="status-error">Saisis au moins 3 caractères.</div>`;
            return;
        }

        articleCard.style.display  = "none";
        zoneCard.style.display     = "none";
        depositCard.style.display  = "none";
        currentArticle             = null;

        try {
            const res  = await fetch(`${API}/article/search/${encodeURIComponent(ref)}`);
            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.error || "Erreur lors de la recherche de l'article.");
            }

            currentArticle = data;

            // Infos produit
            articleName.textContent  = data.designation;
            articleRef.textContent   = data.reference;
            articleStock.textContent = data.stock_total ?? data.stock ?? 'N/A';
            articleImage.src         = data.image || "{{ asset('images/default.jpg') }}";

            // Zones existantes
            let html = "";
            if (!data.zones || data.zones.length === 0) {
                html = `<span class="text-xs text-gray-400">Aucune zone encore adressée.</span>`;
            } else {
                data.zones.forEach(z => {
                    html += `<span class="badge-zone-existing">${z.zone} · ${z.stock}</span>`;
                });
            }
            zonesList.innerHTML = html;

            articleCard.style.display = "flex";
            zoneCard.style.display    = "block";

        } catch (err) {
            articleStatus.innerHTML = `<div class="status-error">${err.message}</div>`;
        }
    }

    searchBtn.addEventListener("click", fetchArticle);
    refInput.addEventListener("keyup", (e) => {
        if (e.key === 'Enter') {
            fetchArticle();
        }
    });

    /* ========================
       2️⃣ AUTO-COMPLÉTION ZONE
       ======================== */
    zoneInput.addEventListener("input", async () => {
        const q = zoneInput.value.trim().toUpperCase();
        zoneSuggestions.style.display = "none";
        zoneSuggestions.innerHTML = "";

        if (q.length < 2) return;
        try {
            const res  = await fetch(`${API}/adresse/search/${encodeURIComponent(q)}`);
            const data = await res.json();
            if (!res.ok) return;

            if (!Array.isArray(data) || data.length === 0) {
                return;
            }

            zoneSuggestions.innerHTML = data.map(z =>
                `<div class="suggestion-item">${z.zone}</div>`
            ).join("");

            zoneSuggestions.style.display = "block";
        } catch (e) {
            // on ignore silencieusement
        }
    });

    zoneSuggestions.addEventListener("click", (e) => {
        if (e.target.classList.contains("suggestion-item")) {
            zoneInput.value = e.target.textContent;
            zoneSuggestions.style.display = "none";
        }
    });

    document.addEventListener("click", (e) => {
        if (!zoneSuggestions.contains(e.target) && e.target !== zoneInput) {
            zoneSuggestions.style.display = "none";
        }
    });

    /* ========================
       2️⃣ VALIDATION ZONE
       ======================== */
    validateZoneBtn.addEventListener("click", async () => {
        clearMessages();

        if (!currentArticle) {
            zoneStatus.innerHTML = `<div class="status-error">Commence par rechercher un article.</div>`;
            return;
        }

        const zone = zoneInput.value.trim().toUpperCase();
        if (!zone) {
            zoneStatus.innerHTML = `<div class="status-error">Saisis une zone.</div>`;
            return;
        }

        try {
            const res  = await fetch(`${API}/stockage/adresser`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {})
                },
                body: JSON.stringify({
                    reference: currentArticle.reference,
                    zone: zone
                })
            });

            const data = await res.json();
            if (!res.ok) {
                const msg = data.error || 'Erreur lors de la validation de la zone.';
                throw new Error(msg);
            }

            zoneStatus.innerHTML = `<div class="status-success">${data.message || 'Zone validée.'}</div>`;
            selectedZone.textContent = zone;
            depositCard.style.display = "block";

        } catch (err) {
            zoneStatus.innerHTML = `<div class="status-error">${err.message}</div>`;
        }
    });

    /* ========================
       3️⃣ DÉPÔT
       ======================== */
    depositBtn.addEventListener("click", async () => {
        clearMessages();

        if (!currentArticle) {
            depositStatus.innerHTML = `<div class="status-error">Aucun article chargé.</div>`;
            return;
        }

        const zone = selectedZone.textContent.trim();
        if (!zone) {
            depositStatus.innerHTML = `<div class="status-error">Aucune zone sélectionnée.</div>`;
            return;
        }

        const qty = parseInt(qtyInput.value, 10);
        if (isNaN(qty) || qty < 1) {
            depositStatus.innerHTML = `<div class="status-error">Quantité invalide.</div>`;
            return;
        }

        try {
            const res  = await fetch(`${API}/stockage/miseAJourStock`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {})
                },
                body: JSON.stringify({
                    reference: currentArticle.reference,
                    zone: zone,
                    quantite: qty
                })
            });

            const data = await res.json();
            if (!res.ok) {
                const msg = data.error || 'Erreur lors de la mise à jour du stock.';
                throw new Error(msg);
            }

            depositStatus.innerHTML = `
                <div class="status-success">
                    Déposé : <strong>${qty}</strong> unité(s) dans <strong>${zone}</strong>.<br>
                    Nouveau stock total article : <strong>${data.stock_total_article ?? 'N/A'}</strong>
                </div>
            `;

            // reset quantité mais on garde l'article chargé
            qtyInput.value = 1;

        } catch (err) {
            depositStatus.innerHTML = `<div class="status-error">${err.message}</div>`;
        }
    });
});
</script>
@endpush

@endsection
