<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueStock extends Model
{
    protected $table = 'historique_stock';

    public $timestamps = false;

    protected $fillable = [
        'id_article',
        'reference_article',
        'designation_article',
        'zone',
        'action_type',
        'quantite',
        'stock_avant',
        'stock_apres',
        'stock_total_article',
        'stock_total_adresse',
        'taux_adressage',
        'id_collaborateur',
        'nom_collaborateur',
        'role_collaborateur',
        'created_at'
    ];
}
