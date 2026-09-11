<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Retiro extends Model
{
    protected $fillable = [
        'caja_id',
        'user_id',
        'categoria_gasto_id',
        'monto',
        'concepto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    /**
     * Caja (período) de donde salió el dinero.
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Usuario que registró el retiro.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Categoría del gasto (impuestos, mantenimiento, varios, ...).
     */
    public function categoriaGasto(): BelongsTo
    {
        return $this->belongsTo(CategoriaGasto::class);
    }
}
