<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaGasto extends Model
{
    protected $table = 'categoria_gastos';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Retiros registrados en esta categoría de gasto.
     */
    public function retiros(): HasMany
    {
        return $this->hasMany(Retiro::class);
    }
}
