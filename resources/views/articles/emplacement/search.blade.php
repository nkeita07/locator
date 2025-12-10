{{-- resources/views/articles/emplacement/search.blade.php --}}
@extends('dashboard.main')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

    <h1 class="text-xl font-semibold text-gray-800 mb-4">
        Rechercher un article
    </h1>

    {{-- Formulaire de recherche --}}
    <form id="articleSearchForm">
        @csrf
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Référence article
        </label>

        <input type="text" 
               id="article_search_input"
               class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300"
               placeholder="Ex : 2005494">

        <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
            Chercher
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("articleSearchForm");
    const input = document.getElementById("article_search_input");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        let reference = input.value.trim();
        if (!reference) return;

        // Redirection vers page résultats
        window.location.href = `/article/emplacement/${reference}`;
    });
});
</script>
@endpush
