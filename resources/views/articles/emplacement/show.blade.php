{{-- resources/views/articles/emplacement/show.blade.php --}}
@extends('dashboard.main')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-xl">

    {{-- HEADER --}}
    <div class="flex justify-between items-center border-b pb-4 mb-6">
        <h1 class="text-xl font-semibold text-gray-800">
            Emplacement de l'article
        </h1>
        <div class="text-sm text-gray-500">
            Stock global : {{ $stockGlobal }}
        </div>
    </div>

    {{-- ARTICLE INFO --}}
    <div class="flex items-center space-x-4 mb-8">

        <img src="{{ $article->image ?? asset('images/default.jpg') }}"
             alt="Image article"
             class="w-24 h-24 object-cover rounded-md shadow">

        <div>
            <p class="text-2xl font-bold text-gray-900">
                {{ $article->designation }}
            </p>
            <p class="text-gray-600 text-lg">
                Référence : {{ $article->reference }}
            </p>
        </div>
    </div>

    {{-- LISTE DES EMPLACEMENTS --}}
    <div class="mb-10">
        <div class="grid grid-cols-4 gap-4 border-b pb-2 mb-3 font-semibold text-gray-700">
            <div>Adresse</div>
            <div class="col-span-2 text-center">Actions</div>
            <div class="text-right">
                Stock adressé (Total : {{ $stockTotalAdresses }})
            </div>
        </div>

        @forelse ($adressages as $adressage)
            <div class="grid grid-cols-4 gap-4 py-3 border-b items-center">
                
                <div class="font-medium text-gray-800">
                    {{ $adressage->adresse->zone }}
                </div>

                <div class="col-span-2 flex justify-center items-center space-x-3">

                    <button 
                        class="stock-btn bg-blue-600 text-white px-3 py-1 rounded"
                        data-action="decrement"
                        data-zone="{{ $adressage->adresse->zone }}">
                        –
                    </button>

                    <span class="font-semibold text-gray-800">
                        {{ $adressage->stock }}
                    </span>

                    <button 
                        class="stock-btn bg-blue-600 text-white px-3 py-1 rounded"
                        data-action="increment"
                        data-zone="{{ $adressage->adresse->zone }}">
                        +
                    </button>

                </div>

                <div class="text-right text-gray-500">
                    <i class="fas fa-box"></i>
                </div>

            </div>
        @empty
            <p class="text-center text-gray-400 py-4">
                Aucun emplacement référencé pour cet article.
            </p>
        @endforelse
    </div>

    {{-- AJOUT D’UNE NOUVELLE ADRESSE --}}
    <form id="newZoneForm" class="pt-4 border-t">
        @csrf

        <div class="flex items-end space-x-3">

            <div class="flex-1">
                <label class="text-sm text-gray-700">Nouvelle adresse</label>
                <input type="text" 
                       id="new_zone"
                       class="w-full p-2 border rounded"
                       placeholder="Ex : D5-3">
            </div>

            <div class="w-24">
                <label class="text-sm text-gray-700">Qté</label>
                <input type="number"
                       id="new_quantity"
                       value="1"
                       min="1"
                       class="w-full p-2 border rounded">
            </div>

            <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Ajouter
            </button>

        </div>
    </form>

    <div id="statusMessage" class="mt-4 text-sm text-center"></div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const reference = "{{ $article->reference }}";

    /* -----------------------------------------------------
       FONCTION GÉNÉRIQUE : Mise à jour stock
    ------------------------------------------------------ */
    async function sendUpdate(zone, quantite) {

        const response = await fetch("/api/stockage/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({
                reference: reference,
                zone: zone,
                quantite: quantite
            })
        });

        const data = await response.json();

        if (!response.ok) {
            alert(data.error ?? "Erreur lors de la mise à jour.");
            return;
        }

        location.reload();
    }

    /* -----------------------------------------------------
       BOUTONS + / -
    ------------------------------------------------------ */
    document.querySelectorAll(".stock-btn").forEach(btn => {

        btn.addEventListener("click", () => {

            const zone = btn.dataset.zone;
            const action = btn.dataset.action;

            const quantite = action === "increment" ? 1 : -1;

            sendUpdate(zone, quantite);
        });
    });

    /* -----------------------------------------------------
       AJOUT D’UNE NOUVELLE ADRESSE
    ------------------------------------------------------ */
    document.getElementById("newZoneForm").addEventListener("submit", e => {
        e.preventDefault();

        const zone = document.getElementById("new_zone").value.trim().toUpperCase();
        const quantite = parseInt(document.getElementById("new_quantity").value);

        if (!zone || quantite <= 0) {
            alert("Veuillez saisir une zone valide et une quantité positive.");
            return;
        }

        sendUpdate(zone, quantite);
    });

});
</script>
@endpush
