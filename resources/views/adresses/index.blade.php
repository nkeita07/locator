@extends('dashboard.main')
@section('title', 'Adresser un article')

@section('content')

@php
    // Autorisés à faire des actions de stock/adressage
    $canAdress = auth()->user()?->hasAnyRole(['admin','logisticien']) ?? false;
@endphp

<style>
:root {
    --blue: #2B3DB8;
    --green: #00B388;
    --gray-light: #F7F7F9;
    --text: #1E1E2D;
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

/* Bandeau accès */
#accessBanner {
    position: fixed;
    top: 18px;
    left: 50%;
    transform: translateX(-50%);
    width: min(720px, calc(100% - 24px));
    background: #FFF3CD;
    color: #856404;
    border: 1px solid #FFEEBA;
    border-radius: 12px;
    padding: 12px 14px;
    z-index: 99999;
    display: none;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
    font-weight: 600;
    font-size: .95rem;
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

.input.disabled {
    background: #F1F3F6;
    cursor: not-allowed;
    color: #777;
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
    align-items: flex-start;
}

@media (max-width: 768px) {
    .article-infos { flex-direction: column; align-items: stretch; }
    #articleImage { align-self: center; }
}

#articleImage {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    background: #f3f4f6;
    border: 1px solid #eef0f3;
}

.article-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text);
    margin: .2rem 0 .35rem 0;
}

/*** ZONES EXISTANTES ***/
.zones-section { margin-top: .35rem; width: 100%; }

.zones-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: .7rem;
    width: 100%;
}

@media (min-width: 768px) {
    .zones-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

.zone-row {
    background: var(--gray-light);
    padding: .85rem 1rem;
    border-radius: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.zone-left { display:flex; flex-direction:column; gap:2px; }
.zone-name { font-weight: 800; font-size: 1.05rem; color:#111; }
.zone-stock { font-size: .9rem; color:#4b5563; }

.zone-actions { display:flex; gap:.4rem; }

.zone-btn {
    background: var(--blue);
    color: white;
    font-size: 20px;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
}
.zone-btn:active { transform: scale(.98); }

.zone-btn.is-disabled {
    background: #AEB5E6;
    cursor: not-allowed;
}

/*** AUTOCOMPLÉTION (articles + zones) ***/
.suggestions {
    position: absolute;
    background: white;
    width: 100%;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    margin-top: .35rem;
    display: none;
    z-index: 9999; /* important */
    box-shadow: 0 12px 28px rgba(0,0,0,.08);
    overflow: hidden;
}

.suggestion-item {
    padding: .85rem .9rem;
    cursor: pointer;
    display: flex;
    gap: 12px;
    align-items: center;
}
.suggestion-item:hover { background: #EEF1FF; }

.suggestion-thumb {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    background: #f3f4f6;
    border: 1px solid #eef0f3;
    flex: 0 0 auto;
}

.suggestion-main { line-height: 1.2; }
.suggestion-ref { font-weight: 800; color:#111; }
.suggestion-name { font-size: .9rem; color:#4b5563; margin-top: 2px; }

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
    width: 180px;
    cursor: pointer;
}
.btn-confirm:hover { background:#009e78; }

.btn-confirm.is-disabled {
    background: #9ddccf;
    cursor: not-allowed;
}

/*** MODAL ***/
.modal-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999;
}
.modal-box {
    background: white;
    width: 420px;
    max-width: 95%;
    padding: 1.8rem;
    border-radius: 14px;
}
.modal-title { font-size: 1.25rem; font-weight: 800; }
.modal-sub { font-size:.92rem; color:#6b7280; margin: .35rem 0 1rem 0; }

.modal-footer { display:flex; justify-content:space-between; margin-top:1.2rem; gap:.7rem; }
.modal-btn {
    padding:.75rem 1.1rem;
    border-radius:10px;
    font-weight:700;
    border:none;
    cursor:pointer;
    width: 100%;
}
.btn-cancel { background:#E5E7EB; }
.btn-ok { background:var(--green); color:white; }

/*** LOADER ***/
#loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99998;
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

<div id="accessBanner">Vous n’avez pas les accès. Merci de contacter le gestionnaire de l’application.</div>

{{-- LOADER --}}
<div id="loader"><div class="loader-spin"></div></div>

<div class="wrapper-adresser">

    {{-- 1) RECHERCHE --}}
    <div class="card">

        <label class="label">Référence ou nom de l’article</label>
        <div class="relative" style="position:relative;">
            <input type="text"
                   id="refInput"
                   class="input"
                   placeholder="Ex : 608629 ou chaussure"
                   autocomplete="off">
            <div id="refSuggestions" class="suggestions"></div>
        </div>

        <div id="articleStatus"></div>

        <div id="articleBlock" style="display:none;" class="article-infos mt-4">
            <img id="articleImage" src="{{ asset('images/default.jpg') }}" alt="Image article">

            <div style="flex:1">

                <div class="text-sm text-gray-600">
                    Référence : <b id="articleRef"></b>
                </div>

                <div id="articleName" class="article-title"></div>

                <div class="text-sm text-gray-600">Stock total : <b id="articleStock"></b></div>
                <div class="text-sm text-gray-600">Stock adressé : <b id="articleStockAdr"></b></div>

                <div class="text-xs text-gray-500 mt-3 mb-1">Zones existantes :</div>

                <div class="zones-section">
                    <div id="zonesList" class="zones-grid"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- 2) ZONE --}}
    <div class="card" id="zoneCard" style="display:none;">
        <label class="label">Zone</label>
        <div class="relative" style="position:relative;">
            <input type="text"
                   id="zoneInput"
                   class="input"
                   placeholder="Ex : A1-2"
                   autocomplete="off">
            <div class="suggestions" id="zoneSuggestions"></div>
        </div>

        <div id="zoneStatus"></div>
    </div>

    {{-- 3) DEPOT --}}
    <div class="card" id="depositCard" style="display:none;">
        <label class="label">Zone sélectionnée</label>
        <div id="selectedZone" style="font-weight:800;color:var(--blue);margin-top:6px;"></div>

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
function showLoader(){ document.getElementById("loader").style.display="flex"; }
function hideLoader(){ document.getElementById("loader").style.display="none"; }

document.addEventListener("DOMContentLoaded", () => {

    const API  = "{{ url('/api') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // droits
    const CAN_ADRESS = @json($canAdress);

    // bandeau accès
    const banner = document.getElementById('accessBanner');
    function showAccessDenied(){
        banner.style.display = 'block';
        clearTimeout(window.__bannerTimer);
        window.__bannerTimer = setTimeout(() => banner.style.display = 'none', 3500);
    }

    let currentArticle = null;

    /* DOM refs */
    const refInput        = document.getElementById("refInput");
    const refSuggestions  = document.getElementById("refSuggestions");

    const articleBlock    = document.getElementById("articleBlock");
    const articleStatus   = document.getElementById("articleStatus");
    const articleStockAdr = document.getElementById("articleStockAdr");

    const zonesList       = document.getElementById("zonesList");

    const articleImage    = document.getElementById("articleImage");
    const articleName     = document.getElementById("articleName");
    const articleRef      = document.getElementById("articleRef");
    const articleStock    = document.getElementById("articleStock");

    const zoneCard        = document.getElementById("zoneCard");
    const zoneInput       = document.getElementById("zoneInput");
    const zoneStatus      = document.getElementById("zoneStatus");
    const zoneSuggestions = document.getElementById("zoneSuggestions");

    const depositCard     = document.getElementById("depositCard");
    const selectedZone    = document.getElementById("selectedZone");
    const qtyInput        = document.getElementById("qtyInput");
    const depositBtn      = document.getElementById("depositBtn");
    const depositStatus   = document.getElementById("depositStatus");

    /* Modal */
    const modal           = document.getElementById("stockModal");
    const modalTitle      = document.getElementById("modalTitle");
    const modalProduct    = document.getElementById("modalProduct");
    const modalQty        = document.getElementById("modalQty");
    const modalMessage    = document.getElementById("modalMessage");
    const modalCancel     = document.getElementById("modalCancel");
    const modalConfirm    = document.getElementById("modalConfirm");

    let modalZone = null;
    let modalMode = null;

    /* =======================================================
       A) UI droits : griser si pas de droit
    ======================================================= */
    function applyRightsUI(){
        if (!CAN_ADRESS) {
            // griser zoneInput + empêcher focus
            zoneInput.classList.add('disabled');
            zoneInput.setAttribute('readonly', 'readonly');

            // griser bouton valider dépôt
            depositBtn.classList.add('is-disabled');
        } else {
            zoneInput.classList.remove('disabled');
            zoneInput.removeAttribute('readonly');

            depositBtn.classList.remove('is-disabled');
        }
    }
    applyRightsUI();

    // Clic sur zone input => message si pas droit
    zoneInput.addEventListener('mousedown', (e) => {
        if (!CAN_ADRESS) { e.preventDefault(); showAccessDenied(); }
    });
    zoneInput.addEventListener('keydown', (e) => {
        if (!CAN_ADRESS) { e.preventDefault(); showAccessDenied(); }
    });

    /* =======================================================
       1) AUTOCOMPLETE ARTICLES (liste uniquement)
    ======================================================= */
    let acTimer;
    refInput.addEventListener("input", () => {
        clearTimeout(acTimer);

        const q = refInput.value.trim();
        if (q.length < 2) {
            refSuggestions.style.display = "none";
            return;
        }

        acTimer = setTimeout(loadArticleSuggestions, 250);
    });

    async function loadArticleSuggestions(){
    const q = refInput.value.trim().toLowerCase();
    if (q.length < 2) return;

    try {
        const res = await fetch(`${API}/article/autocomplete/${encodeURIComponent(q)}`);
        const data = await res.json();

        if (!Array.isArray(data)) {
            refSuggestions.style.display = "none";
            return;
        }

        // ✅ FILTRAGE FRONT : COMMENCE PAR
        const filtered = data.filter(a => {
            const ref  = (a.reference || '').toLowerCase();
            const name = (a.designation || '').toLowerCase();
            return ref.startsWith(q) || name.startsWith(q);
        });

        if (filtered.length === 0) {
            refSuggestions.style.display = "none";
            return;
        }

        refSuggestions.innerHTML = filtered.map(a => `
            <div class="suggestion-item" data-ref="${a.reference}">
                <img class="suggestion-thumb"
                     src="${a.image || '{{ asset('images/default.jpg') }}'}"
                     alt="">
                <div class="suggestion-main">
                    <div class="suggestion-ref">${a.reference}</div>
                    <div class="suggestion-name">${a.designation || ''}</div>
                </div>
            </div>
        `).join('');

        refSuggestions.style.display = "block";
    } catch (e) {
        refSuggestions.style.display = "none";
    }
}


    // clic suggestion article => recherche réelle (search)
    refSuggestions.addEventListener('click', (e) => {
        const item = e.target.closest('.suggestion-item');
        if (!item) return;

        const ref = item.dataset.ref;
        refInput.value = ref;
        refSuggestions.style.display = "none";

        loadArticle(ref);
    });

    // click outside => hide suggestions
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#refSuggestions') && !e.target.closest('#refInput')) {
            refSuggestions.style.display = "none";
        }
        if (!e.target.closest('#zoneSuggestions') && !e.target.closest('#zoneInput')) {
            zoneSuggestions.style.display = "none";
        }
    });

    /* =======================================================
       2) RECHERCHE ARTICLE (appel API search)
    ======================================================= */
    async function loadArticle(query){

        const q = (query ?? refInput.value).trim();
        if (q.length < 2) return;

        articleStatus.innerHTML = `<div class="status">Recherche en cours...</div>`;
        articleBlock.style.display = "none";
        zoneCard.style.display     = "none";
        depositCard.style.display  = "none";

        showLoader();

        try {
            const res  = await fetch(`${API}/article/search/${encodeURIComponent(q)}`);
            const data = await res.json();

            hideLoader();

            if (!res.ok) {
                articleStatus.innerHTML =
                    `<div class="status status-error">${data.error ?? "Article introuvable."}</div>`;
                return;
            }

            currentArticle = data;

            articleName.textContent  = data.designation;
            articleRef.textContent   = data.reference;
            articleStock.textContent = data.stock_total ?? data.stock ?? "N/A";
            articleImage.src         = data.image ?? "{{ asset('images/default.jpg') }}";

            const totalAdr = (data.zones ?? [])
                .reduce((sum, z) => sum + (z.stock || 0), 0);

            articleStockAdr.textContent = totalAdr;

            renderZones(data.zones ?? []);

            articleBlock.style.display = "flex";
            zoneCard.style.display     = "block";
            articleStatus.innerHTML    = "";

            // appliquer UI droits (boutons grisés ou non)
            applyRightsUI();

        } catch (e) {
            hideLoader();
            articleStatus.innerHTML = `<div class='status status-error'>Erreur réseau.</div>`;
        }
    }

    // Entrée => recherche directe (au lieu de sélectionner automatiquement)
    refInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            refSuggestions.style.display = "none";
            loadArticle();
        }
    });

    /* =======================================================
       3) ZONES EXISTANTES (SANS stock 0)
    ======================================================= */
    function renderZones(zones){

        const filtered = zones.filter(z => (z.stock || 0) > 0);

        if (filtered.length === 0){
            zonesList.innerHTML =
                `<div class='text-gray-400 text-xs'>Aucune zone avec stock.</div>`;
            return;
        }

        zonesList.innerHTML = filtered.map(z => `
            <div class="zone-row">
                <div class="zone-left">
                    <span class="zone-name">${z.zone}</span>
                    <span class="zone-stock">${z.stock} unités</span>
                </div>

                <div class="zone-actions">
                    <button class="zone-btn ${CAN_ADRESS ? '' : 'is-disabled'}"
                            data-zone="${z.zone}"
                            data-mode="remove"
                            type="button">–</button>

                    <button class="zone-btn ${CAN_ADRESS ? '' : 'is-disabled'}"
                            data-zone="${z.zone}"
                            data-mode="add"
                            type="button">+</button>
                </div>
            </div>
        `).join('');
    }

    /* =======================================================
       4) AUTOCOMPLÉTION ZONES
    ======================================================= */
    zoneInput.addEventListener("input", async () => {
        if (!CAN_ADRESS) { showAccessDenied(); zoneSuggestions.style.display="none"; return; }

        const q = zoneInput.value.trim();
        if (!q) return zoneSuggestions.style.display = "none";

        try {
            const res  = await fetch(`${API}/adresse/search/${encodeURIComponent(q)}`);
            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0){
                zoneSuggestions.style.display = "none";
                return;
            }

            // IMPORTANT : item cliquable
            zoneSuggestions.innerHTML = data
                .map(z => `<div class="suggestion-item" data-zone="${z.zone}">${z.zone}</div>`)
                .join("");

            zoneSuggestions.style.display = "block";
        } catch (_) {}
    });

    // clic suggestion zone
    zoneSuggestions.addEventListener("click", async (e) => {

        const item = e.target.closest('[data-zone]');
        if (!item) return;

        if (!CAN_ADRESS) { showAccessDenied(); zoneSuggestions.style.display="none"; return; }

        const zone = item.dataset.zone;
        zoneInput.value = zone;
        zoneSuggestions.style.display = "none";

        if (!currentArticle) return;

        zoneStatus.innerHTML = `<div class='status'>Validation en cours...</div>`;
        showLoader();

        try {
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
            hideLoader();

            if (!res.ok){
                // si ton backend renvoie 403 pour vendeur
                if (res.status === 403) {
                    zoneStatus.innerHTML = "";
                    showAccessDenied();
                    return;
                }
                zoneStatus.innerHTML =
                    `<div class='status status-error'>${data.error ?? "Zone invalide."}</div>`;
                return;
            }

            zoneStatus.innerHTML =
                `<div class='status status-success'>Zone validée.</div>`;

            selectedZone.textContent = zone;
            depositCard.style.display = "block";
            applyRightsUI();

        } catch (e) {
            hideLoader();
            zoneStatus.innerHTML = `<div class='status status-error'>Erreur réseau.</div>`;
        }
    });

    /* =======================================================
       5) Dépôt classique (ADD)
    ======================================================= */
    depositBtn.addEventListener("click", async () => {

        if (!CAN_ADRESS) { showAccessDenied(); return; }

        depositStatus.innerHTML = "";
        const zone = selectedZone.textContent.trim();
        const qty  = parseInt(qtyInput.value);

        if (!qty || qty < 1){
            depositStatus.innerHTML =
                `<div class='status status-error'>Quantité invalide.</div>`;
            return;
        }

        depositStatus.innerHTML = `<div class='status'>Mise à jour en cours...</div>`;
        showLoader();

        try {
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
            hideLoader();

            if (!res.ok){
                if (res.status === 403) { depositStatus.innerHTML=""; showAccessDenied(); return; }

                depositStatus.innerHTML =
                    `<div class='status status-error'>${data.error ?? "Erreur lors du dépôt."}</div>`;
                return;
            }

            depositStatus.innerHTML =
                `<div class='status status-success'>+${qty} unités ajoutées dans <b>${zone}</b>.</div>`;

            qtyInput.value = 1;
            loadArticle(currentArticle.reference);

        } catch (e) {
            hideLoader();
            depositStatus.innerHTML = `<div class='status status-error'>Erreur réseau.</div>`;
        }
    });

    /* =======================================================
       6) MODAL + / -
    ======================================================= */
    zonesList.addEventListener("click", (e) => {
        const btn = e.target.closest(".zone-btn");
        if (!btn) return;

        // si pas droits => message (même si bouton grisé)
        if (!CAN_ADRESS) { showAccessDenied(); return; }

        modalZone = btn.dataset.zone;
        modalMode = btn.dataset.mode;

        modalQty.value = 1;
        modalMessage.innerHTML = "";

        modalTitle.textContent =
            modalMode === "add" ? "Ajouter du stock" : "Retirer du stock";

        modalProduct.textContent = `Réf. ${currentArticle.reference} · Zone ${modalZone}`;

        modal.style.display = "flex";
    });

    modalCancel.addEventListener("click", () => modal.style.display = "none");

    modalConfirm.addEventListener("click", async () => {

        if (!CAN_ADRESS) { showAccessDenied(); return; }

        const qty = parseInt(modalQty.value);
        if (!qty || qty < 1) return;

        const quantite = (modalMode === "add") ? qty : -qty;

        modalMessage.innerHTML = `<div class='status'>Mise à jour en cours...</div>`;
        showLoader();

        try {
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
            hideLoader();

            if (!res.ok){
                if (res.status === 403) { modalMessage.innerHTML=""; modal.style.display="none"; showAccessDenied(); return; }

                modalMessage.innerHTML =
                    `<div class='status status-error'>${data.error ?? "Erreur lors de la mise à jour."}</div>`;
                return;
            }

            modal.style.display = "none";
            loadArticle(currentArticle.reference);

        } catch (e) {
            hideLoader();
            modalMessage.innerHTML = `<div class='status status-error'>Erreur réseau.</div>`;
        }
    });

});
</script>
@endpush
