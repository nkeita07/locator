<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historique_stock', function (Blueprint $table) {
            $table->id();

            // Article
            $table->unsignedBigInteger('id_article');
            $table->string('reference_article', 50);
            $table->string('designation_article')->nullable();

            // Zone
            $table->string('zone', 50)->nullable();

            // Action
            $table->enum('action_type', [
                'ADRESSAGE',
                'ADD',
                'REMOVE',
                'CORRECTION'
            ]);

            // Quantités
            $table->integer('quantite');
            $table->integer('stock_avant');
            $table->integer('stock_apres');

            // Analyse stock
            $table->integer('stock_total_article');
            $table->integer('stock_total_adresse');
            $table->decimal('taux_adressage', 5, 2);

            // Utilisateur
            $table->unsignedBigInteger('id_collaborateur')->nullable();
            $table->string('nom_collaborateur')->nullable();
            $table->string('role_collaborateur')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index('id_article');
            $table->index('reference_article');
            $table->index('action_type');
            $table->index('zone');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_stock');
    }
};
