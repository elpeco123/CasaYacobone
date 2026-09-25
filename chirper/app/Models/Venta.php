<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    /**
     * Formas de pago vigentes: clave guardada => etiqueta que ve la gente.
     */
    public const PAGOS = [
        'efectivo' => 'Efectivo',
        'debito' => 'Débito',
        'credito' => 'Crédito',
        'factura' => 'Factura',
        'cuenta_corriente' => 'Cuenta corriente',
    ];

    /**
     * Formas de pago que ya no se ofrecen pero existen en ventas viejas.
     */
    public const PAGOS_LEGADO = ['tarjeta' => 'Tarjeta'];

    /**
     * Recargo fijo del crédito, en porcentaje.
     */
    public const RECARGO_CREDITO = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'caja_id',
        'cliente_id',
        'tipo_pago',
        'subtotal',
        'descuento_porcentaje',
        'monto_descuento',
        'monto_recargo',
        'total',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento_porcentaje' => 'decimal:2',
            'monto_descuento' => 'decimal:2',
            'monto_recargo' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the venta.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the caja (período) this venta belongs to.
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Cliente de la venta a cuenta corriente (null en el resto).
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Etiqueta de la forma de pago, incluidas las ventas viejas con "tarjeta".
     */
    public static function etiquetaPago(?string $tipo): string
    {
        return self::PAGOS[$tipo] ?? self::PAGOS_LEGADO[$tipo] ?? self::PAGOS['efectivo'];
    }

    /**
     * Total vendido por forma de pago. Devuelve siempre todas las formas
     * vigentes (en cero si no hubo) y suma "tarjeta" solo si las ventas viejas
     * del período la usaron.
     *
     * @return array<string, float>
     */
    public static function desglosePorPago(Builder $query): array
    {
        $sumas = (clone $query)
            ->selectRaw('tipo_pago, SUM(total) as suma')
            ->groupBy('tipo_pago')
            ->pluck('suma', 'tipo_pago');

        $desglose = [];
        foreach (array_keys(self::PAGOS) as $tipo) {
            $desglose[$tipo] = (float) ($sumas[$tipo] ?? 0);
        }

        if ((float) ($sumas['tarjeta'] ?? 0) > 0) {
            $desglose['tarjeta'] = (float) $sumas['tarjeta'];
        }

        return $desglose;
    }

    /**
     * Get the items for the venta.
     */
    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }
}
