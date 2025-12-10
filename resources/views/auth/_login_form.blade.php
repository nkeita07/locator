{{-- Fichier : resources/views/auth/_login_form.blade.php --}}

<form action="#" method="POST">
     @csrf
     
    {{-- Champ Profil --}}
    <div class="mb-4">
        <label for="profil" class="block text-gray-600 mb-2">Profil</label>
        <input type="text" 
               id="profil" 
               name="profil" 
               class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
               required>
    </div>

    {{-- Champ Mot de passe --}}
    <div class="mb-8">
        <label for="password" class="block text-gray-600 mb-2">Mot de passe</label>
        <input type="password" 
               id="password" 
               name="password" 
               class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
               required>
    </div>

    {{-- Bouton Connexion --}}
    <button type="submit" 
            class="w-full py-3 text-white font-semibold rounded-lg connexion-button-bg transition duration-300 hover:bg-indigo-700">
        Connexion
    </button>
</form>