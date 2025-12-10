<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Collaborateur extends Authenticatable
{
    use HasFactory;

    protected $table = 'collaborateurs';
    protected $primaryKey = 'id_collaborateur';

    // Indique à Laravel qu'il n’y a PAS de remember_token dans cette table
    protected $rememberTokenName = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'feid',
    ];

    public $timestamps = false; // Ta table n'a ni created_at ni updated_at définis par Eloquent

    /**
     * 🔹 Relation Many-to-Many : un collaborateur possède plusieurs rôles
     * Via la table pivot collaborateur_role
     */
    public function roles()
    {
        return $this->belongsToMany(
            RoleCollaborateur::class,
            'collaborateur_role',
            'id_collaborateur',
            'id_role'
        );
    }

    /**
     * 🔹 Vérifie si l'utilisateur possède un rôle donné
     * Usage : $user->hasRole('admin')
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('libelle', $role)->exists();
    }

    /**
     * 🔹 Relation éventuelle : collaborateur appartient à un magasin
     * (Ne sera active que si tu ajoutes id_store dans la table collaborateurs)
     */
    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'id_store', 'id_store');
    }
}
