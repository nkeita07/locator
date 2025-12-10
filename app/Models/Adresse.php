<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;

    protected $table = 'adresse';
    protected $primaryKey = 'id_adresse';
    
    // Le tableau $fillable contient le nouveau nom de colonne : 'zone'
    protected $fillable = ['zone']; 

    public $timestamps = false; // Pas de colonnes created_at/updated_at par défaut

    // Relation: Une Adresse a plusieurs Adressages (Adresser)
    public function adressages()
    {
        return $this->hasMany(Adresser::class, 'id_adresse', 'id_adresse');
    }
}