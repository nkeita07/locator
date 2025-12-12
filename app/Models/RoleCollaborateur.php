<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleCollaborateur extends Model
{
    protected $table = 'role_collaborateur';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = ['libelle'];
}
