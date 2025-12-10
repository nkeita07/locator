<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutLignePanier extends Model
{
    use HasFactory;

    protected $table = 'statut_ligne_panier';
    protected $primaryKey = 'id_statut';
    protected $fillable = ['libelle'];
    public $timestamps = false;

    // Relation: Un Statut est lié à plusieurs Lignes de Panier
    public function lignesPanier()
    {
        return $this->hasMany(LignePanier::class, 'id_statut', 'id_statut');
    }
}