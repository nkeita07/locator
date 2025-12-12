<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Collaborateur extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'collaborateurs'; // ✅ CORRIGÉ
    protected $primaryKey = 'id_collaborateur';

    protected $fillable = [
        'name',
        'email',
        'password',
        'feid'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * ----------------------------------------------------------------------
     * RELATION : ROLES
     * ----------------------------------------------------------------------
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
     * ----------------------------------------------------------------------
     * AUTORISATIONS
     * ----------------------------------------------------------------------
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->where('libelle', $role)
            ->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()
            ->whereIn('libelle', $roles)
            ->exists();
    }

    /**
     * ----------------------------------------------------------------------
     * HELPERS
     * ----------------------------------------------------------------------
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isLogisticien(): bool
    {
        return $this->hasRole('logisticien');
    }

    public function isVendeur(): bool
    {
        return $this->hasRole('vendeur');
    }
}
