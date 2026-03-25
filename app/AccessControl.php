<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model
{
    protected $table = 'access_controls';

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        // Relación con usuario, incluso si ha sido eliminado
        return $this->belongsTo(\App\User::class, 'user_id')
            ->withTrashed(); // Incluye usuarios eliminados
    }
}
