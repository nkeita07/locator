<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'panier';
    protected $primaryKey = 'id_panier';
    protected $fillable = ['id_collaborateur', 'id_store', 'date_creation', 'date_validation', 'id_etat'];
    public $timestamps = false; // Pas de created_at/updated_at par convention Laravel

    // Les dates doivent être castées
    protected $casts = [
        'date_creation' => 'datetime',
        'date_validation' => 'datetime',
    ];

    // Relation: Appartient à un Collaborateur
    public function collaborateur()
    {
        return $this->belongsTo(Collaborateur::class, 'id_collaborateur', 'id_collaborateur');
    }

    // Relation: Appartient à un Magasin
    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'id_store', 'id_store');
    }

    // Relation: Appartient à un État de Panier
    public function etat()
    {
        return $this->belongsTo(EtatPanier::class, 'id_etat', 'id_etat');
    }
    
    // Relation: Un Panier a plusieurs Lignes de Panier
    public function lignesPanier()
    {
        return $this->hasMany(LignePanier::class, 'id_panier', 'id_panier');
    }
}