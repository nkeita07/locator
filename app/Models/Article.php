<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';
    protected $primaryKey = 'id_article';
    
    // Tous les champs que nous pouvons remplir par programme (mass assignment)
    protected $fillable = [
        'reference', 
        'designation', 
        'image', 
        'stock' // Stock total de l'article dans le magasin
    ];

    public $timestamps = false; // 'created_at' est géré par la base de données, pas par les timestamps automatiques de Laravel

    // Relation: Un Article est dans plusieurs Adressages (via la table Adresser)
    public function adressages()
    {
        return $this->hasMany(Adresser::class, 'id_article', 'id_article');
    }
}