@extends('dashboard.main')

@section('title', 'Adresser un article')

@section('content')

<style>
:root {
    --blue: #2B3DB8;
    --green: #00B388;
    --gray-light: #F7F7F9;
}

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

#articleImage {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

.zone-badge {
    background: var(--blue);
    padding: .35rem .7rem;
    border-radius: 12px;
    color: white;
    font-size: .85rem;
    font-weight: bold;
}

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

.status {
    margin-top: .7rem;
    padding: .8rem;
    border-radius: 10px;
    font-weight: 600;
}
.status-success { background: #D1FAE5; color:#065F46; }
.status-error { background: #FEE2E2; color:#9B1C1C; }

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
                <div id="zonesList" class="flex flex-wrap gap-1"></div>
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



{{-- =============================== --}}
{{-- SCRIPTS --}}
{{-- =============================== --}}

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded",()=>{

    const API = "{{ url('/api') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let currentArticle = null;

    // DOM
    const refInput = document.getElementById("refInput");
    const articleBlock = document.getElementById("articleBlock");
    const articleStatus = document.getElementById("articleStatus");
    const articleName = document.getElementById("articleName");
    const articleRef = document.getElementById("articleRef");
    const articleStock = document.getElementById("articleStock");
    const articleImage = document.getElementById("articleImage");
    const zonesList = document.getElementById("zonesList");

    const zoneCard = document.getElementById("zoneCard");
    const zoneInput = document.getElementById("zoneInput");
    const zoneSuggestions = document.getElementById("zoneSuggestions");
    const zoneStatus = document.getElementById("zoneStatus");

    const depositCard = document.getElementById("depositCard");
    const selectedZone = document.getElementById("selectedZone");
    const qtyInput = document.getElementById("qtyInput");
    const depositBtn = document.getElementById("depositBtn");
    const depositStatus = document.getElementById("depositStatus");



    /* -------------------------
       1) Recherche automatique
       ------------------------- */
    let timer;
    refInput.addEventListener("input", ()=>{

        clearTimeout(timer);

        timer = setTimeout(async()=>{

            const ref = refInput.value.trim();
            if (ref.length < 3) return;

            articleStatus.innerHTML = "";
            articleBlock.style.display = "none";
            zoneCard.style.display = "none";
            depositCard.style.display = "none";

            const res = await fetch(`${API}/article/search/${ref}`);
            const data = await res.json();

            if(!res.ok){
                articleStatus.innerHTML = `<div class="status status-error">${data.error}</div>`;
                return;
            }

            currentArticle = data;

            // populate UI
            articleName.textContent = data.designation;
            articleRef.textContent = data.reference;
            articleStock.textContent = data.stock_total ?? data.stock;
            articleImage.src = data.image;

            zonesList.innerHTML = "";
            (data.zones ?? []).forEach(z=>{
                zonesList.innerHTML += `<span class="zone-badge">${z.zone}</span>`;
            });

            articleBlock.style.display = "flex";
            zoneCard.style.display = "block";

        }, 400);
    });



    /* ----------------------------------
       2) Auto-complétion zone + validation automatique
       ---------------------------------- */
    zoneInput.addEventListener("input", async ()=>{

        const q = zoneInput.value.trim().toUpperCase();
        if (q.length < 1){
            zoneSuggestions.style.display = "none";
            return;
        }

        const res = await fetch(`${API}/adresse/search/${q}`);
        const data = await res.json();

        if(!res.ok || !Array.isArray(data)){
            zoneSuggestions.style.display = "none";
            return;
        }

        zoneSuggestions.innerHTML = "";
        data.forEach(z=>{
            zoneSuggestions.innerHTML += `<div>${z.zone}</div>`;
        });

        zoneSuggestions.style.display = "block";
    });

    // Sélection d’une zone → validation automatique
    zoneSuggestions.addEventListener("click", async (e)=>{

        const zone = e.target.textContent;
        zoneInput.value = zone;
        zoneSuggestions.style.display = "none";

        zoneStatus.innerHTML = "";

        const res = await fetch(`${API}/stockage/adresser`,{
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

        if(!res.ok){
            zoneStatus.innerHTML = `<div class="status status-error">${data.error}</div>`;
            return;
        }

        zoneStatus.innerHTML = `<div class="status status-success">Zone validée</div>`;
        selectedZone.textContent = zone;
        depositCard.style.display = "block";
    });



    /* -----------------------
       3) Dépôt marchandises
       ----------------------- */
    depositBtn.addEventListener("click", async ()=>{

        const qty = parseInt(qtyInput.value);

        const res = await fetch(`${API}/stockage/miseAJourStock`,{
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": csrf
            },
            body:JSON.stringify({
                reference: currentArticle.reference,
                zone: selectedZone.textContent,
                quantite: qty
            })
        });

        const data = await res.json();

        if(!res.ok){
            depositStatus.innerHTML = `<div class="status status-error">${data.error}</div>`;
            return;
        }

        depositStatus.innerHTML = `
            <div class="status status-success">
                ${qty} unité(s) déposées.<br>
                Nouveau stock total : ${data.stock_total_article}
            </div>
        `;

        qtyInput.value = 1;
    });

});
</script>
@endpush

@endsection
