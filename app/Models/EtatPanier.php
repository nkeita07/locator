<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtatPanier extends Model
{
    use HasFactory;

    protected $table = 'etat_panier';
    protected $primaryKey = 'id_etat';
    protected $fillable = ['libelle'];
    public $timestamps = false;

    // Relation: Un État de Panier est lié à plusieurs Paniers
    public function paniers()
    {
        return $this->hasMany(Panier::class, 'id_etat', 'id_etat');
    }
}