@extends('dashboard.main')
@section('title', 'Adresser un article')

@section('content')

<style>
:root {
    --blue: #2B3DB8;
    --green: #00B388;
    --gray-light: #F7F7F9;
}

/* Layout */
.wrapper-adresser {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1rem;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem 1.7rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
}

.card h2 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1E1E2D;
    margin-bottom: 1rem;
}

/* Inputs */
.input {
    width: 100%;
    padding: .9rem .8rem;
    border-radius: 10px;
    border: 2px solid #E1E5EA;
    background: white;
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

/*** ARTICLE BLOCK ***/
.article-infos {
    display: flex;
    gap: 15px;
}

#articleImage {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
}

/*** ZONES EXISTANTES (grille + centrage mobile) ***/
#zonesList {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .6rem;
}

@media(max-width: 640px){
    #zonesList {
        grid-template-columns: 1fr;
    }
}

.zone-row {
    background: var(--gray-light);
    padding: .7rem .9rem;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.zone-name {
    font-weight: 700;
}

.zone-actions {
    display: flex;
    gap: .3rem;
}

.zone-btn {
    background: var(--blue);
    color: white;
    font-size: 20px;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

/*** AUTOCOMPLÉTION ***/
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

/*** STATUS MESSAGE ***/
.status {
    margin-top: .8rem;
    padding: .9rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: .9rem;
}
.status-success { background: #D1FAE5; color:#065F46; }
.status-error   { background: #FEE2E2; color:#9B1C1C; }

/*** BOUTON VALIDER ***/
.btn-confirm {
    background: var(--green);
    color: white;
    padding: .75rem;
    border-radius: 10px;
    font-weight: bold;
    border: none;
    width: 160px;
    cursor: pointer;
}
.btn-confirm:hover { background:#009e78; }

/*** MODAL ***/
.modal-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 500;
}
.modal-box {
    background: white;
    width: 380px;
    max-width: 95%;
    padding: 1.8rem;
    border-radius: 14px;
}
.modal-title {
    font-weight: 700;
    font-size: 1.25rem;
}
.modal-sub {
    font-size: .9rem;
    color: #777;
    margin-bottom: 1rem;
}
.modal-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
}
.modal-btn {
    padding: .7rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    border: none;
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
        <input type="text" id="refInput" class="input" placeholder="Ex : 608629">

        <div id="articleStatus"></div>

        <div id="articleBlock" style="display:none;" class="article-infos mt-4">
            <img id="articleImage" src="">

            <div style="flex:1">
                <p class="text-sm text-gray-600">
                    Référence : <b id="articleRef"></b>
                </p>
                <p id="articleName" class="font-bold text-lg"></p>

                <p class="text-sm text-gray-600">
                    Stock total : <b id="articleStock"></b>
                </p>

                <p class="text-sm text-gray-600">
                    Stock adressé : <b id="articleStockAdr"></b>
                </p>

                <p class="text-xs text-gray-500 mt-3 mb-1">Zones existantes :</p>
                <div id="zonesList"></div>
            </div>
        </div>
    </div>


    {{-- 2 — ZONE --}}
    <div class="card" id="zoneCard" style="display:none;">
        <h2>2. Zone de destination</h2>

        <label class="label">Zone</label>
        <div class="relative">
            <input type="text" id="zoneInput" class="input" placeholder="Ex : A1-2" autocomplete="off">
            <div class="suggestions" id="zoneSuggestions"></div>
        </div>

        <div id="zoneStatus"></div>
    </div>


    {{-- 3 — DÉPÔT --}}
    <div class="card" id="depositCard" style="display:none;">
        <h2>3. Ajouter dans la zone</h2>

        <label class="label">Zone sélectionnée</label>
        <div class="zone-badge" id="selectedZone"></div>

        <label class="label mt-3">Quantité</label>
        <input type="number" id="qtyInput" class="input" value="1" min="1">

        <div class="mt-4">
            <button class="btn-confirm" id="depositBtn">Valider</button>
        </div>

        <div id="depositStatus"></div>
    </div>

</div>


{{-- MODAL + / - --}}
<div id="stockModal" class="modal-bg">
    <div class="modal-box">

        <div class="modal-title" id="modalTitle"></div>
        <div class="modal-sub" id="modalProduct"></div>

        <label class="label">Quantité</label>
        <input type="number" class="input" id="modalQty" value="1" min="1">

        <div id="modalMessage"></div>

        <div class="modal-footer">
            <button id="modalCancel" class="modal-btn btn-cancel">Annuler</button>
            <button id="modalConfirm" class="modal-btn btn-ok">Valider</button>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const API  = "{{ url('/api') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let currentArticle = null;

    /* DOM refs */
    const refInput       = document.getElementById("refInput");
    const articleBlock   = document.getElementById("articleBlock");
    const articleStatus  = document.getElementById("articleStatus");
    const articleStockAdr= document.getElementById("articleStockAdr");

    const zonesList      = document.getElementById("zonesList");

    const articleImage   = document.getElementById("articleImage");
    const articleName    = document.getElementById("articleName");
    const articleRef     = document.getElementById("articleRef");
    const articleStock   = document.getElementById("articleStock");

    const zoneCard       = document.getElementById("zoneCard");
    const zoneInput      = document.getElementById("zoneInput");
    const zoneStatus     = document.getElementById("zoneStatus");
    const zoneSuggestions= document.getElementById("zoneSuggestions");

    const depositCard    = document.getElementById("depositCard");
    const selectedZone   = document.getElementById("selectedZone");
    const qtyInput       = document.getElementById("qtyInput");
    const depositBtn     = document.getElementById("depositBtn");
    const depositStatus  = document.getElementById("depositStatus");

    /* Modal */
    const modal          = document.getElementById("stockModal");
    const modalTitle     = document.getElementById("modalTitle");
    const modalProduct   = document.getElementById("modalProduct");
    const modalQty       = document.getElementById("modalQty");
    const modalMessage   = document.getElementById("modalMessage");
    const modalCancel    = document.getElementById("modalCancel");
    const modalConfirm   = document.getElementById("modalConfirm");

    let modalZone = null;
    let modalMode = null;


    /* =======================================================
       1) Recherche Article
    ======================================================= */
    let timer;
    refInput.addEventListener("input", () => {
        clearTimeout(timer);
        timer = setTimeout(loadArticle, 400);
    });

    async function loadArticle() {
        const ref = refInput.value.trim();
        if (ref.length < 3) return;

        articleStatus.innerHTML = "";
        articleBlock.style.display = "none";
        zoneCard.style.display     = "none";
        depositCard.style.display  = "none";

        try {
            const res  = await fetch(`${API}/article/search/${ref}`);
            const data = await res.json();

            if (!res.ok) {
                articleStatus.innerHTML =
                    `<div class="status status-error">${data.error ?? "Article introuvable."}</div>`;
                return;
            }

            currentArticle = data;

            articleName.textContent   = data.designation;
            articleRef.textContent    = data.reference;
            articleStock.textContent  = data.stock_total ?? data.stock ?? "N/A";
            articleImage.src          = data.image ?? "{{ asset('images/default.jpg') }}";

            const totalAdr = (data.zones ?? [])
                .reduce((sum, z) => sum + (z.stock || 0), 0);

            articleStockAdr.textContent = totalAdr;

            renderZones(data.zones ?? []);

            articleBlock.style.display = "flex";
            zoneCard.style.display     = "block";

        } catch (_) {
            articleStatus.innerHTML = `<div class='status status-error'>Erreur réseau.</div>`;
        }
    }


    /* =======================================================
       2) Affichage Zones existantes
    ======================================================= */
    function renderZones(zones){
        const filtered = zones.filter(z => z.stock > 0);

        if (filtered.length === 0){
            zonesList.innerHTML = `<div class='text-gray-400 text-xs'>Aucune zone avec stock.</div>`;
            return;
        }

        zonesList.innerHTML = filtered.map(z => `
            <div class="zone-row">
                <div>
                    <span class="zone-name">${z.zone}</span>
                    <span class="text-xs text-gray-600"> · ${z.stock} unités</span>
                </div>

                <div class="zone-actions">
                    <button class="zone-btn" data-zone="${z.zone}" data-mode="remove">–</button>
                    <button class="zone-btn" data-zone="${z.zone}" data-mode="add">+</button>
                </div>
            </div>
        `).join('');
    }


    /* =======================================================
       3) Auto-complétion Zone + Validation auto
    ======================================================= */
    zoneInput.addEventListener("input", async () => {
        const q = zoneInput.value.trim();
        if (!q) return zoneSuggestions.style.display = "none";

        try {
            const res  = await fetch(`${API}/adresse/search/${q}`);
            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0){
                zoneSuggestions.style.display = "none";
                return;
            }

            zoneSuggestions.innerHTML = data.map(z =>
                `<div>${z.zone}</div>`
            ).join("");

            zoneSuggestions.style.display = "block";

        } catch (_) {}
    });

    zoneSuggestions.addEventListener("click", async (e) => {
        const zone = e.target.textContent;
        zoneInput.value = zone;
        zoneSuggestions.style.display = "none";

        if (!currentArticle) return;

        const res = await fetch(`${API}/stockage/adresser`, {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":csrf
            },
            body:JSON.stringify({
                reference: currentArticle.reference,
                zone: zone
            })
        });

        const data = await res.json();

        if (!res.ok){
            zoneStatus.innerHTML =
                `<div class='status status-error'>${data.error ?? "Zone invalide."}</div>`;
            return;
        }

        zoneStatus.innerHTML =
            `<div class='status status-success'>Zone validée.</div>`;

        selectedZone.textContent = zone;
        depositCard.style.display = "block";
    });



    /* =======================================================
       4) Dépôt classique (ADD)
    ======================================================= */
    depositBtn.addEventListener("click", async () => {
        depositStatus.innerHTML = "";

        const zone = selectedZone.textContent.trim();
        const qty  = parseInt(qtyInput.value);

        if (!qty || qty < 1){
            depositStatus.innerHTML =
                `<div class='status status-error'>Quantité invalide.</div>`;
            return;
        }

        const res = await fetch(`${API}/stockage/miseAJourStock`, {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":csrf
            },
            body:JSON.stringify({
                reference: currentArticle.reference,
                zone: zone,
                quantite: qty
            })
        });

        const data = await res.json();

        if (!res.ok){
            depositStatus.innerHTML =
                `<div class='status status-error'>${data.error ?? "Erreur lors du dépôt."}</div>`;
            return;
        }

        depositStatus.innerHTML =
            `<div class='status status-success'>+${qty} unités ajoutées dans <b>${zone}</b>.</div>`;

        qtyInput.value = 1;

        loadArticle();
    });



    /* =======================================================
       5) MODAL + / -
    ======================================================= */
    zonesList.addEventListener("click", (e) => {
        const btn = e.target.closest(".zone-btn");
        if (!btn) return;

        modalZone = btn.dataset.zone;
        modalMode = btn.dataset.mode;
        modalQty.value = 1;
        modalMessage.innerHTML = "";

        modalTitle.textContent =
            modalMode === "add" ? "Ajouter du stock" : "Retirer du stock";

        modalProduct.textContent =
            `Réf. ${currentArticle.reference} · Zone ${modalZone}`;

        modal.style.display = "flex";
    });

    modalCancel.addEventListener("click", () => {
        modal.style.display = "none";
    });


    modalConfirm.addEventListener("click", async () => {
        const qty = parseInt(modalQty.value);
        if (!qty || qty < 1) return;

        const quantite = (modalMode === "add") ? qty : -qty;

        const res = await fetch(`${API}/stockage/miseAJourStock`, {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":csrf
            },
            body:JSON.stringify({
                reference: currentArticle.reference,
                zone: modalZone,
                quantite: quantite
            })
        });

        const data = await res.json();

        if (!res.ok){
            modalMessage.innerHTML =
                `<div class='status status-error'>${data.error ?? "Erreur lors de la mise à jour."}</div>`;
            return;
        }

        modal.style.display = "none";
        loadArticle();
    });

});
</script>
@endpush
