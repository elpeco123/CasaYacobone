<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    public const ESTADO_ABIERTA = 'abierta';

    public const ESTADO_CERRADA = 'cerrada';

    protected $fillable = [
        'user_id',
        'fecha',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'monto_inicial',
        'observaciones',
        'total_efectivo',
        'total_tarjeta',
        'total_debito',
        'total_credito',
        'total_cuenta_corriente',
        'total_factura',
        'total_retiros',
        'cantidad_ventas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'total_efectivo' => 'decimal:2',
        'total_tarjeta' => 'decimal:2',
        'total_debito' => 'decimal:2',
        'total_credito' => 'decimal:2',
        'total_cuenta_corriente' => 'decimal:2',
        'total_factura' => 'decimal:2',
        'total_retiros' => 'decimal:2',
    ];

    /**
     * Usuario que registró o modificó la apertura de caja.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ventas realizadas dentro del período de esta caja.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Retiros (gastos) realizados dentro del período de esta caja.
     */
    public function retiros(): HasMany
    {
        return $this->hasMany(Retiro::class);
    }

    /**
     * Indica si la caja sigue abierta.
     */
    public function estaAbierta(): bool
    {
        return $this->estado === self::ESTADO_ABIERTA;
    }

    /**
     * Total general vendido en la caja (snapshot al cierre o cálculo en vivo).
     */
    public function totalGeneral(): float
    {
        return (float) $this->total_efectivo
            + (float) $this->total_tarjeta
            + (float) $this->total_debito
            + (float) $this->total_credito
            + (float) $this->total_factura
            + (float) $this->total_cuenta_corriente;
    }

    /**
     * Efectivo físico: cambio inicial + ventas en efectivo − retiros.
     */
    public function efectivoFisico(): float
    {
        return (float) $this->monto_inicial + (float) $this->total_efectivo - (float) $this->total_retiros;
    }

    /**
     * La caja actualmente abierta (hay una sola a la vez, la opere quien la opere).
     */
    public static function abierta(): ?self
    {
        return self::where('estado', self::ESTADO_ABIERTA)
            ->latest()
            ->first();
    }
}
