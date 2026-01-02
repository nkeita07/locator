@extends('dashboard.main')


@section('content')

<style>
/* ===== GLOBAL ===== */
.container-historique {
    max-width: 1200px;
    margin: auto;
    padding: 1.5rem 1rem;
    font-size: 14px;
    color: #1f2937;
}

h2 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: .5rem;
}

/* ===== KPI ===== */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.kpi-card {
    background: #fff;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    border: 1px solid #e5e7eb;
}

.kpi-label {
    font-size: .8rem;
    color: #6b7280;
}

.kpi-value {
    font-size: 1.6rem;
    font-weight: 600;
    margin-top: .3rem;
}

.kpi-green { border-left: 5px solid #16a34a; }
.kpi-red   { border-left: 5px solid #dc2626; }
.kpi-orange{ border-left: 5px solid #f59e0b; }

.kpi-link {
    display: inline-block;
    margin-top: .5rem;
    font-size: .8rem;
    color: #2563eb;
    text-decoration: underline;
}

/* ===== FILTERS ===== */
.filters-box {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem;
    margin-bottom: 1rem;
}

.filters-box summary {
    cursor: pointer;
    font-weight: 500;
    color: #374151;
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px,1fr));
    gap: .8rem;
    margin-top: 1rem;
}

.filters-form label {
    font-size: .75rem;
    color: #6b7280;
}

.filters-form input,
.filters-form select {
    width: 100%;
    padding: .45rem .5rem;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: .85rem;
}

.filters-actions {
    grid-column: 1 / -1;
    display: flex;
    gap: .6rem;
    margin-top: .5rem;
}

.btn-primary {
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .45rem .9rem;
    font-size: .85rem;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
    border-radius: 8px;
    padding: .45rem .9rem;
    font-size: .85rem;
}

/* ===== EXPORT ===== */
.export-bar {
    text-align: right;
    margin-bottom: .5rem;
}

.btn-outline {
    border: 1px solid #2563eb;
    color: #2563eb;
    padding: .4rem .8rem;
    border-radius: 8px;
    font-size: .8rem;
}

/* ===== TABLE ===== */
.table-wrapper {
    overflow-x: auto;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.historique-table {
    width: 100%;
    border-collapse: collapse;
}

.historique-table th {
    text-align: left;
    font-size: .75rem;
    font-weight: 500;
    color: #6b7280;
    padding: .6rem;
    border-bottom: 1px solid #e5e7eb;
}

.historique-table td {
    padding: .6rem;
    font-size: .85rem;
    border-bottom: 1px solid #f1f5f9;
}

.empty {
    text-align: center;
    color: #9ca3af;
}

/* ===== BADGES ===== */
.badge {
    padding: .2rem .5rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 500;
}

.badge-add { background: #dcfce7; color: #166534; }
.badge-remove { background: #fee2e2; color: #991b1b; }
.badge-adressage { background: #e0e7ff; color: #3730a3; }

.taux-ok { color: #16a34a; font-weight: 500; }
.taux-low { color: #dc2626; font-weight: 500; }

/* ===== PAGINATION ===== */
.pagination-wrapper {
    margin-top: .8rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper svg { display:none; }

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
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

    {{-- KPI --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Taux global d’adressage</div>
            <div class="kpi-value">{{ $tauxGlobal }}%</div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="kpi-label">Articles adressés</div>
            <div class="kpi-value">{{ $articlesAdresses }}</div>
        </div>

        <div class="kpi-card kpi-red">
            <div class="kpi-label">Articles non adressés</div>
            <div class="kpi-value">{{ $articlesNonAdresses }}</div>
            <a href="{{ route('historique.non_adresses') }}" class="kpi-link">Afficher la liste</a>
        </div>

        <div class="kpi-card kpi-orange">
            <div class="kpi-label">Articles sur-stockés</div>
            <div class="kpi-value">{{ $articlesSurStockes }}</div>
            <a href="{{ route('historique.sur_stockes') }}" class="kpi-link">Afficher la liste</a>
        </div>
    </div>

    {{-- Filtres --}}
    <details class="filters-box">
        <summary>Filtres de l’historique</summary>

        <form method="GET" class="filters-form">
            <div>
                <label>Référence</label>
                <input type="text" name="reference" value="{{ request('reference') }}">
            </div>

            <div>
                <label>Zone</label>
                <input type="text" name="zone" value="{{ request('zone') }}">
            </div>

            <div>
                <label>Action</label>
                <select name="action">
                    <option value="">Toutes</option>
                    <option value="ADD" @selected(request('action')==='ADD')>Ajout</option>
                    <option value="REMOVE" @selected(request('action')==='REMOVE')>Retrait</option>
                    <option value="ADRESSAGE" @selected(request('action')==='ADRESSAGE')>Adressage</option>
                </select>
            </div>

            <div>
                <label>Date début</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}">
            </div>

            <div>
                <label>Date fin</label>
                <input type="date" name="date_end" value="{{ request('date_end') }}">
            </div>

            <div class="filters-actions">
                <button class="btn-primary">Appliquer</button>
                <a href="{{ route('historique.index') }}" class="btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </details>

    {{-- Export --}}
    <div class="export-bar">
        <a href="{{ route('historique.export.excel', request()->query()) }}" class="btn-outline">
            Exporter Excel
        </a>
    </div>

    {{-- Table --}}
    <div class="table-wrapper">
        <table class="historique-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Zone</th>
                    <th>Action</th>
                    <th>Qté</th>
                    <th>Taux</th>
                    <th>Utilisateur</th>
                </tr>
            </thead>
            <tbody>
            @forelse($historiques as $h)
                <tr>
                    <td>{{ $h->created_at }}</td>
                    <td>{{ $h->reference_article }}</td>
                    <td>{{ $h->zone }}</td>
                    <td>
                        @if($h->action_type === 'ADD')
                            <span class="badge badge-add">Ajout</span>
                        @elseif($h->action_type === 'REMOVE')
                            <span class="badge badge-remove">Retrait</span>
                        @else
                            <span class="badge badge-adressage">Adressage</span>
                        @endif
                    </td>
                    <td>{{ $h->quantite }}</td>
                    <td>
                        <span class="{{ $h->taux_adressage < 50 ? 'taux-low' : 'taux-ok' }}">
                            {{ $h->taux_adressage }}%
                        </span>
                    </td>
                    <td>{{ $h->nom_collaborateur }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">Aucune donnée</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($historiques->hasPages())
<div class="pagination-wrapper">

    <div class="pagination-info">
        Page {{ $historiques->currentPage() }}
        sur {{ $historiques->lastPage() }}
        — {{ $historiques->total() }} résultats
    </div>

    <div class="pagination">
        {{-- Précédent --}}
        @if ($historiques->onFirstPage())
            <span class="page disabled">‹</span>
        @else
            <a href="{{ $historiques->previousPageUrl() }}" class="page">‹</a>
        @endif

        {{-- Pages --}}
        @foreach ($historiques->getUrlRange(1, $historiques->lastPage()) as $page => $url)
            @if ($page == $historiques->currentPage())
                <span class="page active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($historiques->hasMorePages())
            <a href="{{ $historiques->nextPageUrl() }}" class="page">›</a>
        @else
            <span class="page disabled">›</span>
        @endif
    </div>

</div>
@endif


</div>

@endsection
