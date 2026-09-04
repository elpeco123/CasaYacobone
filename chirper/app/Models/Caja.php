<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'user_id',
        'fecha',
        'monto_inicial',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_inicial' => 'decimal:2',
    ];

    /**
     * Usuario que registró o modificó la apertura de caja.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la caja del día actual (o null si no fue abierta).
     */
    public static function obtenerCajaHoy(): ?self
    {
        return self::whereDate('fecha', Carbon::today())->latest()->first();
    }

    /**
     * Obtener el monto inicial del día de hoy (o 0 si no se abrió).
     */
    public static function montoInicialHoy(): float
    {
        return (float) (self::whereDate('fecha', Carbon::today())->latest()->value('monto_inicial') ?? 0);
    }

    /**
     * Obtener el monto inicial para una fecha específica.
     */
    public static function montoInicialFecha(string|Carbon $fecha): float
    {
        $date = $fecha instanceof Carbon ? $fecha->format('Y-m-d') : $fecha;
        return (float) (self::whereDate('fecha', $date)->latest()->value('monto_inicial') ?? 0);
    }
}
