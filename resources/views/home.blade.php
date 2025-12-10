<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Locator</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="container">
        <img src="{{ asset('locator/images/logo_decat.png') }}" alt="Logo Decathlon" class="logo">
        <h1 class="title">Bienvenue sur Product Locator</h1>
        <div class="icon-container">
            <img src="{{ asset('locator/images/logo_google_pointer.png') }}" alt="Icône de localisation" class="locator-icon">
        </div>
        <a href="{{ route('connexion') }}" class="connexion-btn">Connexion</a>
    </div>
</body>
</html>
