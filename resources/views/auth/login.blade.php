{{-- Fichier : resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Locator SN</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<div class="flex h-screen">

        {{-- COLONNE GAUCHE : Image d'Appel à l'Action --}}
        {{-- L'image est img_connexion.png --}}
        <div class="hidden lg:block lg:w-1/2 bg-cover bg-center relative login-image-column" 
             style="background-image: url('{{ asset('images/img connexion.png') }}');">
        </div>

<body class="font-sans">
   
        {{-- COLONNE DROITE : Le Formulaire de Connexion --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
            <div class="max-w-md w-full">

        <img src="{{ asset('images/logo.jpg') }}"  alt="Logo Decathlon" class="mx-auto h-12 mb-12" />
                <h1 class="text-3xl font-semibold text-gray-800 mb-8">Hello teammate!</h1>
                
                {{-- LE FORMULAIRE SERA ICI --}}
                @include('auth._login_form')

            </div>
        </div>

    </div>
</body>
</html>