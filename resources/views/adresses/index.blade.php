@extends('dashboard.main')

@section('title', 'Adresser un article')

@section('content')

<style>
:root {
    --blue: #2B3DB8;
    --green: #00B388;
    --gray-light: #F7F7F9;
}

/* Layout général */
.wrapper-adresser {
    max-width: 980px;
    margin: 0 auto;
    padding: 1rem;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 1.6rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    margin-bottom: 1.5rem;
}

.card h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1E1E2D;
    margin-bottom: 1rem;
}

/* Inputs */
.input {
    width: 100%;
    padding: .9rem .8rem;
    border-radius: 10px;
    border: 2px solid #E5E7EB;
    font-size: 1rem;
}
.input:focus {
    outline: none;
    border-color: var(--blue);
}

.label {
    font-size: .85rem;
    font-weight: 600;
    color: #555;
}

/* Image produit */
#articleImage {
    width: 110px;
    height: 110px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

/* Badges & status */
.zone-badge {
    background: var(--blue);
    padding: .35rem .7rem;
    border-radius: 12px;
    color: white;
    font-size: .85rem;
    font-weight: bold;
}

.status {
    margin-top: .7rem;
    padding: .8rem;
    border-radius: 10px;
    font-weight: 600;
}
.status-success { background: #D1FAE5; color:#065F46; }
.status-error   { background: #FEE2E2; color:#9B1C1C; }

/* Autocomplétion zone */
.suggestions {
    position: absolute;
    background: white;
    width: 100%;
    border-radius: 8px;
    border: 1px solid #DDD;
    margin-top: .2rem;
    display: none;
    z-index: 20;
}
.suggestions div {
    padding: .7rem .8rem;
    cursor: pointer;
}
.suggestions div:hover {
    background: #EEF1FF;
}

/* Bouton confirmation dépôt */
.btn-confirm {
    background: var(--green);
    color: white;
    padding: 1rem;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    width: 100%;
    cursor: pointer;
}
.btn-confirm:hover { background:#00A476; }

/* Lignes des zones existantes */
.zone-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gray-light);
    padding: .6rem .9rem;
    border-radius: 10px;
    margin-bottom: .4rem;
}
.zone-info {
    font-size: .9rem;
}
.zone-info span.zone-name {
    font-weight: 700;
}
.zone-actions {
    display: flex;
    gap: .3rem;
}
.zone-actions .btn-zone {
    background: var(--blue);
    color: white;
    font-size: 20px;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

/* Modal (+ / -) */
.modal-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 60;
}
.modal-box {
    background: white;
    width: 380px;
    max-width: 95%;
    padding: 1.8rem;
    border-radius: 12px;
}
.modal-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: .4rem;
}
.modal-sub {
    font-size: .9rem;
    color: #6B7280;
    margin-bottom: 1rem;
}
.modal-btn {
    padding: .7rem 1.2rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
}
.btn-cancel { background:#E5E7EB; }
.btn-ok     { background:var(--green); color:white; }
</style>

<div class="wrapper-adresser">

    {{-- 1 — RECHERCHE ARTICLE --}}
    <div class="card">
        <h2>1. Recherche de l'article</h2>

        <label class="label">Référence article</label>
        <input type="text" id="refInput" class="input" placeholder="Saisir la référence...">

        <div id="articleStatus"></div>

        <div id="articleBlock" style="display:none;" class="flex gap-4 mt-4">
            <img id="articleImage" src="">

            <div>
                <p class="text-sm text-gray-600">
                    Référence : <b id="articleRef"></b>
                </p>
                <p class="font-bold text-lg" id="articleName"></p>
                <p class="text-sm text-gray-600">
                    Stock total : <b id="articleStock"></b>
                </p>

                <p class="text-xs text-gray-500 mt-3 mb-1">Zones existantes :</p>
                {{-- ICI on affiche les zones en liste avec + / - --}}
                <div id="zonesList"></div>
            </div>
        </div>
    </div>

    {{-- 2 — CHOIX ZONE --}}
    <div class="card" id="zoneCard" style="display:none;">
        <h2>2. Zone de destination</h2>

        <div class="relative">
            <label class="label">Zone</label>
            <input type="text" id="zoneInput" class="input" placeholder="Ex : A1-2" autocomplete="off">
            <div class="suggestions" id="zoneSuggestions"></div>
        </div>

        <div id="zoneStatus"></div>
    </div>

    {{-- 3 — DEPOT --}}
    <div class="card" id="depositCard" style="display:none;">
        <h2>3. Dépôt de marchandise</h2>

        <p>Zone sélectionnée : <span id="selectedZone" class="zone-badge"></span></p>

        <label class="label mt-3">Quantité</label>
        <input type="number" id="qtyInput" value="1" min="1" class="input">

        <button class="btn-confirm mt-4" id="depositBtn">Confirmer le dépôt</button>

        <div id="depositStatus"></div>
    </div>

</div>

{{-- MODAL POUR + / - SUR UNE ZONE --}}
<div id="stockModal" class="modal-bg">
    <div class="modal-box">
        <div class="modal-title" id="modalTitle"></div>
        <div class="modal-sub" id="modalProduct"></div>

        <label class="label">Quantité</label>
        <input type="number" id="modalQty" value="1" min="1" class="input mb-4">

        <div class="flex justify-between mt-2">
            <button type="button" id="modalCancel" class="modal-btn btn-cancel">Annuler</button>
            <button type="button" id="modalConfirm" class="modal-btn btn-ok">Valider</button>
        </div>
    </div>
</div>

{{-- =============================== --}}
{{-- SCRIPTS --}}
{{-- =============================== --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const API  = "{{ url('/api') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let currentArticle = null;

    // DOM
    const refInput       = document.getElementById("refInput");
    const articleStatus  = document.getElementById("articleStatus");
    const articleBlock   = document.getElementById("articleBlock");
    const articleName    = document.getElementById("articleName");
    const articleRef     = document.getElementById("articleRef");
    const articleStock   = document.getElementById("articleStock");
    const articleImage   = document.getElementById("articleImage");
    const zonesList      = document.getElementById("zonesList");

    const zoneCard       = document.getElementById("zoneCard");
    const zoneInput      = document.getElementById("zoneInput");
    const zoneSuggestions= document.getElementById("zoneSuggestions");
    const zoneStatus     = document.getElementById("zoneStatus");

    const depositCard    = document.getElementById("depositCard");
    const selectedZone   = document.getElementById("selectedZone");
    const qtyInput       = document.getElementById("qtyInput");
    const depositBtn     = document.getElementById("depositBtn");
    const depositStatus  = document.getElementById("depositStatus");

    // Modal
    const modal          = document.getElementById("stockModal");
    const modalTitle     = document.getElementById("modalTitle");
    const modalProduct   = document.getElementById("modalProduct");
    const modalQty       = document.getElementById("modalQty");
    const modalCancel    = document.getElementById("modalCancel");
    const modalConfirm   = document.getElementById("modalConfirm");
    let modalZone = null;
    let modalMode = null; // "add" ou "remove"

    function clearStatuses() {
        articleStatus.innerHTML = "";
        zoneStatus.innerHTML = "";
        depositStatus.innerHTML = "";
    }

    /* =======================
       1) RECHERCHE ARTICLE
       ======================= */
    let timer;
    refInput.addEventListener("input", () => {
        clearTimeout(timer);
        timer = setTimeout(fetchArticle, 400);
    });

    async function fetchArticle() {
        clearStatuses();
        const ref = refInput.value.trim();
        if (ref.length < 3) return;

        articleBlock.style.display = "none";
        zoneCard.style.display = "none";
        depositCard.style.display = "none";
        currentArticle = null;

        try {
            const res  = await fetch(`${API}/article/search/${encodeURIComponent(ref)}`);
            const data = await res.json();

            if (!res.ok) {
                const msg = data.error || "Erreur lors de la recherche.";
                articleStatus.innerHTML = `<div class="status status-error">${msg}</div>`;
                return;
            }

            currentArticle = data;

            articleName.textContent  = data.designation;
            articleRef.textContent   = data.reference;
            articleStock.textContent = data.stock_total ?? data.stock ?? 'N/A';
            articleImage.src         = data.image || "{{ asset('images/default.jpg') }}";

            // Affichage des zones existantes avec boutons + / -
            const zones = data.zones || [];
            if (zones.length === 0) {
                zonesList.innerHTML = `<span class="text-xs text-gray-400">Aucune zone encore adressée.</span>`;
            } else {
                zonesList.innerHTML = zones.map(z => `
                    <div class="zone-row">
                        <div class="zone-info">
                            <span class="zone-name">${z.zone}</span>
                            <span class="text-xs text-gray-600"> · ${z.stock} unités</span>
                        </div>
                        <div class="zone-actions">
                            <button type="button" class="btn-zone" data-mode="remove" data-zone="${z.zone}">–</button>
                            <button type="button" class="btn-zone" data-mode="add" data-zone="${z.zone}">+</button>
                        </div>
                    </div>
                `).join('');
            }

            articleBlock.style.display = "flex";
            zoneCard.style.display    = "block";

        } catch (e) {
            articleStatus.innerHTML = `<div class="status status-error">Erreur réseau.</div>`;
        }
    }

    /* =======================
       2) AUTO-COMPLÉTION ZONE
       ======================= */
    zoneInput.addEventListener("input", async () => {
        const q = zoneInput.value.trim().toUpperCase();
        zoneSuggestions.style.display = "none";
        zoneSuggestions.innerHTML = "";

        if (q.length < 1) return;

        try {
            const res  = await fetch(`${API}/adresse/search/${encodeURIComponent(q)}`);
            const data = await res.json();
            if (!res.ok || !Array.isArray(data) || data.length === 0) return;

            zoneSuggestions.innerHTML = data.map(z =>
                `<div>${z.zone}</div>`
            ).join("");
            zoneSuggestions.style.display = "block";
        } catch (e) {
            // on ignore
        }
    });

    zoneSuggestions.addEventListener("click", async (e) => {
        const zone = e.target.textContent;
        if (!zone) return;

        zoneInput.value = zone;
        zoneSuggestions.style.display = "none";
        zoneStatus.innerHTML = "";

        if (!currentArticle) {
            zoneStatus.innerHTML = `<div class="status status-error">Commence par rechercher un article.</div>`;
            return;
        }

        try {
            const res  = await fetch(`${API}/stockage/adresser`, {
                method: "POST",
                headers: {
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": csrf
                },
                body: JSON.stringify({
                    reference: currentArticle.reference,
                    zone: zone
                })
            });

            const data = await res.json();
            if (!res.ok) {
                const msg = data.error || 'Erreur lors de la validation de la zone.';
                zoneStatus.innerHTML = `<div class="status status-error">${msg}</div>`;
                return;
            }

            zoneStatus.innerHTML = `<div class="status status-success">${data.message || 'Zone validée.'}</div>`;
            selectedZone.textContent = zone;
            depositCard.style.display = "block";
        } catch (e) {
            zoneStatus.innerHTML = `<div class="status status-error">Erreur réseau.</div>`;
        }
    });

    /* =======================
       3) DÉPÔT CLASSIQUE
       ======================= */
    depositBtn.addEventListener("click", async () => {
        clearStatuses();

        if (!currentArticle) {
            depositStatus.innerHTML = `<div class="status status-error">Aucun article chargé.</div>`;
            return;
        }

        const zone = selectedZone.textContent.trim();
        if (!zone) {
            depositStatus.innerHTML = `<div class="status status-error">Aucune zone sélectionnée.</div>`;
            return;
        }

        const qty = parseInt(qtyInput.value, 10);
        if (isNaN(qty) || qty < 1) {
            depositStatus.innerHTML = `<div class="status status-error">Quantité invalide.</div>`;
            return;
        }

        try {
            const res  = await fetch(`${API}/stockage/miseAJourStock`, {
                method: "POST",
                headers: {
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": csrf
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
                depositStatus.innerHTML = `<div class="status status-error">${msg}</div>`;
                return;
            }

            depositStatus.innerHTML = `
                <div class="status status-success">
                    ${qty} unité(s) déposées dans <strong>${zone}</strong>.<br>
                    Nouveau stock total article : <strong>${data.stock_total_article ?? 'N/A'}</strong>
                </div>
            `;

            qtyInput.value = 1;
            // on recharge l'article pour mettre à jour les zones
            fetchArticle();

        } catch (e) {
            depositStatus.innerHTML = `<div class="status status-error">Erreur réseau.</div>`;
        }
    });

    /* =======================
       4) MODAL + / -
       ======================= */

    // Ouverture du modal quand on clique sur un bouton de zone
    zonesList.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-zone");
        if (!btn || !currentArticle) return;

        modalZone = btn.dataset.zone;
        modalMode = btn.dataset.mode; // "add" ou "remove"
        modalQty.value = 1;

        modalTitle.textContent = modalMode === "add"
            ? `Ajouter du stock dans ${modalZone}`
            : `Retirer du stock de ${modalZone}`;

        modalProduct.textContent = `${currentArticle.reference} — ${currentArticle.designation}`;
        modal.style.display = "flex";
    });

    modalCancel.addEventListener("click", () => {
        modal.style.display = "none";
    });

    modalConfirm.addEventListener("click", async () => {
        if (!currentArticle || !modalZone || !modalMode) return;

        const q = parseInt(modalQty.value, 10);
        if (isNaN(q) || q < 1) return;

        // NOTE : ici on envoie une quantité positive ou négative selon le mode.
        // Il faut que ton contrôleur accepte les quantités négatives pour le retrait.
        const quantite = (modalMode === "add") ? q : -q;

        try {
            const res  = await fetch(`${API}/stockage/miseAJourStock`, {
                method: "POST",
                headers: {
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": csrf
                },
                body: JSON.stringify({
                    reference: currentArticle.reference,
                    zone: modalZone,
                    quantite: quantite
                })
            });

            const data = await res.json();
            if (!res.ok) {
                const msg = data.error
                    || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Erreur lors de la mise à jour.');
                alert(msg);
                return;
            }

            modal.style.display = "none";
            // On rafraîchit les infos de l'article et des zones
            fetchArticle();

        } catch (e) {
            alert("Erreur réseau lors de la mise à jour du stock.");
        }
    });

});
</script>
@endpush

@endsection
