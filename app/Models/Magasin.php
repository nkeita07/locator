<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    use HasFactory;

    protected $table = 'magasin';
    protected $primaryKey = 'id_store';
    
    // Champs qui peuvent être remplis massivement
    protected $fillable = ['nom', 'localisation'];

    // Désactive les colonnes 'created_at' et 'updated_at'
    public $timestamps = false; 

    // Relation: Un Magasin a plusieurs Collaborateurs
    public function collaborateurs()
    {
        return $this->hasMany(Collaborateur::class, 'id_store', 'id_store');
    }

    // Relation: Un Magasin a plusieurs Paniers (historique des prélèvements)
    public function paniers()
    {
        return $this->hasMany(Panier::class, 'id_store', 'id_store');
    }
}