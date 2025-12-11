<div class="text-white">

    <h1 class="text-2xl font-bold mb-6">
        {{ Auth::user()->matricule ?? "OFAYE15" }}
    </h1>

    <nav class="flex flex-col space-y-4 mt-4">

        <div>
            <a href="{{ route('article.location') }}" class="flex items-center space-x-2">
            <span>📦</span>
            <span>Adresser un article</span>
        </a>
        </div>
        <br>

      <div>
         <a href="{{ route('zones.index') }}" class="flex items-center space-x-2">
            <span>📍</span>
            <span>Zone d'adressage</span>
        </a>
      </div> 
        <br>
        <div>
            <a href="{{ route('historique.index') }}" class="flex items-center space-x-2">
            <span>📜</span>
            <span>Historique</span>
        </a>
        </div>
        <br>
    </nav>

</div>
