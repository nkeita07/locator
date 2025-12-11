@extends('dashboard.main')

@section('title', 'Dashboard')

@section('content')

<style>
.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.feature-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.7rem;
    display: flex;
    align-items: center;
    gap: 1.4rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    transition: transform .15s ease, box-shadow .2s ease;
    cursor: pointer;
}

.feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.feature-icon {
    width: 72px;
    height: 72px;
    border-radius: 0px;
    background: #E8EEFF;
    display: flex;
    justify-content: center;
    align-items: center;
}

.feature-icon img {
    width: 42px;
    height: 42px;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1E1E2D;
}

.feature-sub {
    font-size: .9rem;
    color: #6B7280;
    margin-top: .2rem;
}

.arrow {
    margin-left: auto;
    font-size: 1.4rem;
    color: #2B3DB8;
}
@media screen {
    .feature-icon img {
    width: 80%;
    height: 70%;
}
}
</style>


<div class="feature-grid">

    <!-- Adressage rapide -->
    <a href="{{ route('article.location') }}">
        <div class="feature-card">
            <div class="feature-icon">
                <img src="https://contents.mediadecathlon.com/s1123587/k$25453898a268acbbd9d36ed1fb53adb5/Store%20Location%20Green.png">
            </div>

            <div>
                <div class="feature-title">Adressage rapide</div>
                <div class="feature-sub">
                    Saisir une référence pour adresser un article.
                </div>
            </div>

            <div class="arrow">→</div>
        </div>
    </a>

    <!-- Historique -->
    <a href="{{ route('historique.index') }}">
        <div class="feature-card">
            <div class="feature-icon">
                <img src="https://contents.mediadecathlon.com/s1353854/k$5c64eaf4ab2c7de79ea5578bc332c9db/histirique.png">
            </div>

            <div>
                <div class="feature-title">Historique des mouvements</div>
                <div class="feature-sub">
                    Consultez les derniers mouvements de stock.
                </div>
            </div>

            <div class="arrow">→</div>
        </div>
    </a>

</div>

@endsection
