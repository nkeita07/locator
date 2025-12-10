<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresser extends Model
{
    use HasFactory;

    protected $table = 'adresser';
    protected $primaryKey = 'id_adresser';
    
    // Clés étrangères et champ de stock de la table pivot
    protected $fillable = ['id_article', 'id_adresse', 'stock'];

    public $timestamps = false; 

    // Relation: Appartient à un Article
    public function article()
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    // Relation: Appartient à une Adresse/Zone
    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }
}