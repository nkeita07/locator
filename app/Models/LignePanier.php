<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LignePanier extends Model
{
    use HasFactory;

    protected $table = 'ligne_panier';
    protected $primaryKey = 'id_ligne';
    
    protected $fillable = [
        'id_panier', 
        'id_article', 
        'quantite', 
        'id_adresse_prelevement', 
        'id_statut'
    ];
    public $timestamps = false;

    // Relation: Appartient à un Panier
    public function panier()
    {
        return $this->belongsTo(Panier::class, 'id_panier', 'id_panier');
    }

    // Relation: Appartient à un Article
    public function article()
    {
        return $this->belongsTo(Article::class, 'id_article', 'id_article');
    }

    // Relation: Appartient à une Adresse (de prélèvement)
    public function adressePrelevement()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse_prelevement', 'id_adresse');
    }

    // Relation: Appartient à un Statut de Ligne de Panier
    public function statut()
    {
        return $this->belongsTo(StatutLignePanier::class, 'id_statut', 'id_statut');
    }
}