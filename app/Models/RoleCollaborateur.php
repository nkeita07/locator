<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleCollaborateur extends Model
{
    use HasFactory;

    protected $table = 'role_collaborateur';
    protected $primaryKey = 'id_role';
    protected $fillable = ['libelle'];
    public $timestamps = false;

    // Relation: Un Rôle a plusieurs Collaborateurs
    public function collaborateurs()
    {
        return $this->hasMany(Collaborateur::class, 'id_role', 'id_role');
    }
}