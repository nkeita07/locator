@extends('dashboard.main')

@section('content')
<style>
.container-historique {
    padding: 24px;
}

.page-header h2 {
    font-weight: 600;
    margin-bottom: 4px;
}

.page-subtitle {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 20px;
}

.table-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
}

.historique-table {
    width: 100%;
    border-collapse: collapse;
}

.historique-table th {
    text-align: left;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.historique-table td {
    padding: 10px;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
}

.empty {
    text-align: center;
    color: #9ca3af;
    padding: 20px;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    font-weight: 500;
    background: #fff7ed;
    color: #c2410c;
}

/* ===== PAGINATION (identique historique) ===== */
.pagination-wrapper {
    margin-top: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
}

.pagination-info {
    font-size: .8rem;
    color: #6b7280;
}

.pagination {
    display: flex;
    gap: .3rem;
}

.page {
    padding: .35rem .65rem;
    border-radius: 8px;
    border: 1px solid #dbeafe;
    font-size: .8rem;
    color: #2563eb;
    text-decoration: none;
    background: #eff6ff;
}

.page:hover {
    background: #dbeafe;
}

.page.active {
    background: #2563eb;
    color: #fff;
    font-weight: 500;
}

.page.disabled {
    color: #9ca3af;
    background: #f3f4f6;
    border-color: #e5e7eb;
    cursor: not-allowed;
}
</style>

<div class="container-historique">

    <div class="page-header">
        <h2>Articles sur-stockés</h2>
        <p class="page-subtitle">
            Articles dont la quantité adressée dépasse le stock théorique.
        </p>
    </div>

    <div class="table-wrapper">
        <table class="historique-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th>Stock article</th>
                    <th>Stock adressé</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($articles as $article)
                <tr>
                    <td>{{ $article->reference }}</td>
                    <td>{{ $article->designation }}</td>
                    <td>{{ $article->stock }}</td>
                    <td>{{ $article->stock_adresse }}</td>
                    <td>
                        <a
                            href="{{ route('article.location', ['reference' => $article->reference]) }}"
                            class="btn-action"
                        >
                            Corriger
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">
                        Aucun article sur-stocké
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Affichage de {{ $articles->firstItem() ?? 0 }}
            à {{ $articles->lastItem() ?? 0 }}
            sur {{ $articles->total() }} résultats
        </div>

        <div class="pagination">
            {{-- Précédent --}}
            @if ($articles->onFirstPage())
                <span class="page disabled">‹</span>
            @else
                <a href="{{ $articles->previousPageUrl() }}" class="page">‹</a>
            @endif

            {{-- Pages --}}
            @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
                @if ($page == $articles->currentPage())
                    <span class="page active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($articles->hasMorePages())
                <a href="{{ $articles->nextPageUrl() }}" class="page">›</a>
            @else
                <span class="page disabled">›</span>
            @endif
        </div>
    </div>

</div>
@endsection
