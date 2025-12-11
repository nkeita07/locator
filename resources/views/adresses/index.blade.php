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

/* Inputs */
.input {
    width: 100%;
    padding: .9rem .8rem;
    border-radius: 10px;
    border: 2px solid #E1E5EA;
    background: white;
    font-size: 1rem;
}
.input:focus { border-color: var(--blue); outline: none; }

.label {
    font-size: .85rem;
    font-weight: 600;
    color: #555;
}

/*** ARTICLE BLOCK ***/
.article-infos {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

@media (max-width: 768px) {
    .article-infos {
        flex-direction: column;
        align-items: stretch;
    }
    #articleImage {
        align-self: center;
    }
}

#articleImage {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
}

/*** AUTOCOMPLÉTION – Suggestions Modernisées ***/
.suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
    border: 1px solid #E5E7EB;
    z-index: 40;
    overflow: hidden;
    max-height: 330px;
    display: none;
    overflow-y: auto;
}

.suggestion-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: .9rem;
    cursor: pointer;
    transition: background .15s;
}

.suggestion-item:hover {
    background: #EEF3FF;
}

.suggestion-img {
    width: 55px;
    height: 55px;
    border-radius: 8px;
    object-fit: cover;
    background: #F3F4F6;
}

.suggestion-text {
    display: flex;
    flex-direction: column;
}

.suggestion-ref {
    font-size: 1rem;
    font-weight: 700;
    color: #1E1E2D;
}

.suggestion-des {
    font-size: .88rem;
    color: #555;
}

/*** ZONES EXISTANTES ***/
.zones-section {
    margin-top: .35rem;
    width: 100%;
}

.zones-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: .6rem;
    width: 100%;
}

@media (min-width: 768px) {
    .zones-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.zone-row {
    background: var(--gray-light);
    padding: .7rem .9rem;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.zone-name { font-weight: 700; }

.zone-actions { display: flex; gap: .3rem; }

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

/*** BUTTON ***/
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

.modal-title { font-size: 1.25rem; font-weight: 700; }
.modal-sub { font-size:.9rem; color:#777; margin-bottom:1rem; }

.modal-footer { display:flex; justify-content:space-between; margin-top:1.5rem; }

.modal-btn {
    padding:.7rem 1.2rem;
    border-radius:8px;
    font-weight:600;
    border:none;
    cursor:pointer;
}

.btn-cancel { background:#E5E7EB; }
.btn-ok     { background:var(--green); color:white; }

/*** LOADER ***/
#loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.loader-spin {
    width: 45px;
    height: 45px;
    border: 4px solid #d1d5db;
    border-top-color: var(--blue);
    border-radius: 50%;
    animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>


<div id="loader"><div class="loader-spin"></div></div>

<div class="wrapper-adresser">

    {{-- 🔍 RECHERCHE --}}
    <div class="card">
        <label class="label">Référence ou nom de l'article</label>

        <div class="relative">
            <input type="text" id="refInput" class="input"
                placeholder="608629, chaussure, ballon..."
                autocomplete="off">
            
            <div id="refSuggestions" class="suggestions"></div>
        </div>

        <div id="articleStatus"></div>

        {{-- BLOC ARTICLE --}}
        <div id="articleBlock" style="display:none;" class="article-infos mt-4">

            <img id="articleImage">

            <div style="flex:1">

                <p class="text-sm text-gray-600">Référence : <b id="articleRef"></b></p>
                <p class="font-bold text-lg" id="articleName"></p>

                <p class="text-sm text-gray-600">Stock total : <b id="articleStock"></b></p>
                <p class="text-sm text-gray-600">Stock adressé : <b id="articleStockAdr"></b></p>

                <p class="text-xs text-gray-500 mt-3 mb-1">Zones existantes :</p>

                <div class="zones-section">
                    <div id="zonesList" class="zones-grid"></div>
                </div>

            </div>
        </div>
    </div>


    {{-- ZONE --}}
    <div class="card" id="zoneCard" style="display:none;">
        <label class="label">Zone</label>
        <div class="relative">
            <input type="text" id="zoneInput" class="input" placeholder="A1-2" autocomplete="off">
            <div id="zoneSuggestions" class="suggestions"></div>
        </div>

        <div id="zoneStatus"></div>
    </div>


    {{-- DÉPÔT --}}
    <div class="card" id="depositCard" style="display:none;">

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
        <input type="number" id="modalQty" class="input" value="1" min="1">

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

function showLoader(){ document.getElementById("loader").style.display="flex"; }
function hideLoader(){ document.getElementById("loader").style.display="none"; }

document.addEventListener("DOMContentLoaded", () => {

    const API = "{{ url('/api') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let currentArticle = null;

    /* DOM REFS */
    const refInput    = document.getElementById("refInput");
    const refSuggestions = document.getElementById("refSuggestions");

    const articleBlock  = document.getElementById("articleBlock");
    const articleStatus = document.getElementById("articleStatus");

    const articleImage  = document.getElementById("articleImage");
    const articleName   = document.getElementById("articleName");
    const articleRef    = document.getElementById("articleRef");
    const articleStock  = document.getElementById("articleStock");
    const articleStockAdr = document.getElementById("articleStockAdr");

    const zonesList   = document.getElementById("zonesList");

    const zoneCard    = document.getElementById("zoneCard");
    const zoneInput   = document.getElementById("zoneInput");
    const zoneSuggestions = document.getElementById("zoneSuggestions");
    const zoneStatus  = document.getElementById("zoneStatus");

    const depositCard = document.getElementById("depositCard");
    const selectedZone= document.getElementById("selectedZone");
    const qtyInput    = document.getElementById("qtyInput");
    const depositBtn  = document.getElementById("depositBtn");


    /* ========================================================
       AUTOCOMPLÉTION
    ======================================================== */

    let timer;

    refInput.addEventListener("input", () => {
        clearTimeout(timer);

        const q = refInput.value.trim();
        if (q.length < 2) {
            refSuggestions.style.display = "none";
            return;
        }

        timer = setTimeout(() => loadSuggestions(q), 250);
    });

    async function loadSuggestions(query) {

        try {
            const res = await fetch(`${API}/article/autocomplete/${query}`);
            const list = await res.json();

            if (!Array.isArray(list) || list.length === 0) {
                refSuggestions.style.display = "none";
                return;
            }

            refSuggestions.innerHTML = list.map(item => `
                <div class="suggestion-item" data-ref="${item.reference}">
                    <img src="${item.image}" class="suggestion-img">
                    <div class="suggestion-text">
                        <span class="suggestion-ref">${item.reference}</span>
                        <span class="suggestion-des">${item.designation}</span>
                    </div>
                </div>
            `).join('');

            refSuggestions.style.display = "block";

        } catch (e) {
            console.error(e);
        }
    }


    /* CLICK ON SUGGESTION */
    refSuggestions.addEventListener("click", (e) => {
        const row = e.target.closest(".suggestion-item");
        if (!row) return;

        const ref = row.dataset.ref;

        refInput.value = ref;
        refSuggestions.style.display = "none";

        loadArticle(ref);
    });


    /* ========================================================
       CHARGEMENT ARTICLE
    ======================================================== */

    async function loadArticle(ref) {

        const query = ref ?? refInput.value.trim();
        if (!query) return;

        articleStatus.innerHTML = `<div class="status">Recherche...</div>`;
        articleBlock.style.display = "none";
        zoneCard.style.display = "none";
        depositCard.style.display = "none";

        showLoader();

        try {
            const res = await fetch(`${API}/article/search/${query}`);
            const data = await res.json();

            hideLoader();

            if (!res.ok) {
                articleStatus.innerHTML =
                    `<div class="status status-error">${data.error ?? "Introuvable"}</div>`;
                return;
            }

            currentArticle = data;

            articleName.textContent  = data.designation;
            articleRef.textContent   = data.reference;
            articleStock.textContent = data.stock_total;
            articleImage.src         = data.image;

            const totalAdr = (data.zones ?? [])
                .reduce((sum, z) => sum + (z.stock || 0), 0);

            articleStockAdr.textContent = totalAdr;

            renderZones(data.zones ?? []);

            articleBlock.style.display = "flex";
            zoneCard.style.display     = "block";
            articleStatus.innerHTML = "";

        } catch (e) {
            hideLoader();
            articleStatus.innerHTML = `<div class="status status-error">Erreur réseau</div>`;
        }
    }


    /* ========================================================
       AFFICHAGE DES ZONES
    ======================================================== */

    function renderZones(zones) {

        const filtered = zones.filter(z => z.stock > 0);

        if (filtered.length === 0) {
            zonesList.innerHTML = `<div class='text-gray-400 text-xs'>Aucune zone.</div>`;
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


    /* ========================================================
       AUTOCOMPLÉTION ZONES
    ======================================================== */

    zoneInput.addEventListener("input", async () => {

        const q = zoneInput.value.trim();
        if (!q) return zoneSuggestions.style.display = "none";

        const res = await fetch(`${API}/adresse/search/${q}`);
        const list = await res.json();

        if (!Array.isArray(list) || list.length === 0) {
            zoneSuggestions.style.display = "none";
            return;
        }

        zoneSuggestions.innerHTML = list
            .map(z => `<div class="suggestion-item" data-zone="${z.zone}">${z.zone}</div>`)
            .join('');

        zoneSuggestions.style.display = "block";
    });

    zoneSuggestions.addEventListener("click", async (e) => {
        const div = e.target.closest(".suggestion-item");
        if (!div) return;

        const zone = div.dataset.zone;

        zoneInput.value = zone;
        zoneSuggestions.style.display = "none";

        zoneStatus.innerHTML = `<div class='status'>Validation...</div>`;
        showLoader();

        const res = await fetch(`${API}/stockage/adresser`, {
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

        hideLoader();
        const data = await res.json();

        if (!res.ok) {
            zoneStatus.innerHTML =
                `<div class='status status-error'>${data.error ?? "Zone invalide"}</div>`;
            return;
        }

        zoneStatus.innerHTML =
            `<div class='status status-success'>Zone validée.</div>`;

        selectedZone.textContent = zone;
        depositCard.style.display = "block";
    });


    /* ========================================================
       AJOUT STOCK
    ======================================================== */

    depositBtn.addEventListener("click", async () => {

        const qty = parseInt(qtyInput.value);
        if (!qty || qty < 1) {
            depositStatus.innerHTML = `<div class='status status-error'>Quantité invalide</div>`;
            return;
        }

        depositStatus.innerHTML = `<div class='status'>Mise à jour...</div>`;
        showLoader();

        const res = await fetch(`${API}/stockage/miseAJourStock`, {
            method: "POST",
            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({
                reference: currentArticle.reference,
                zone: selectedZone.textContent,
                quantite: qty
            })
        });

        hideLoader();

        const data = await res.json();
        if (!res.ok) {
            depositStatus.innerHTML = `<div class='status status-error'>${data.error}</div>`;
            return;
        }

        depositStatus.innerHTML =
            `<div class='status status-success'>+${qty} unités ajoutées.</div>`;

        loadArticle(currentArticle.reference);
    });


    /* ========================================================
       MODAL + / -
    ======================================================== */

    const modal = document.getElementById("stockModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalProduct = document.getElementById("modalProduct");
    const modalQty = document.getElementById("modalQty");
    const modalMessage = document.getElementById("modalMessage");

    const modalCancel = document.getElementById("modalCancel");
    const modalConfirm = document.getElementById("modalConfirm");

    let modalZone = null;
    let modalMode = null;

    zonesList.addEventListener("click", (e) => {
        const btn = e.target.closest(".zone-btn");
        if (!btn) return;

        modalZone = btn.dataset.zone;
        modalMode = btn.dataset.mode;

        modalQty.value = 1;
        modalMessage.innerHTML = "";

        modalTitle.textContent =
            modalMode === "add" ? "Ajouter du stock" : "Retirer du stock";

        modalProduct.textContent = `Article ${currentArticle.reference} – Zone ${modalZone}`;

        modal.style.display = "flex";
    });

    modalCancel.addEventListener("click", () => modal.style.display = "none");

    modalConfirm.addEventListener("click", async () => {

        const q = parseInt(modalQty.value);
        if (!q || q < 1) return;

        const quantite = modalMode === "add" ? q : -q;

        modalMessage.innerHTML = `<div class="status">Mise à jour...</div>`;
        showLoader();

        const res = await fetch(`${API}/stockage/miseAJourStock`, {
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

        hideLoader();

        const data = await res.json();

        if (!res.ok) {
            modalMessage.innerHTML =
                `<div class="status status-error">${data.error}</div>`;
            return;
        }

        modal.style.display = "none";
        loadArticle(currentArticle.reference);
    });

});
</script>
@endpush
