<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'categoria_id',
        'proveedor_id',
        'talle',
        'marca',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
        ];
    }

    /**
     * Get the categoria that owns the producto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Get the proveedor that owns the producto.
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Get the venta items for the producto.
     */
    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    /**
     * Scope: productos con stock bajo (stock <= stock_minimo).
     */
    public function scopeStockBajo(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    /**
     * Scope: productos sin stock.
     */
    public function scopeSinStock(Builder $query): Builder
    {
        return $query->where('stock', 0);
    }

    /**
     * Get valor total del stock a precio de compra (costo).
     */
    public function getValorStockCompraAttribute(): float
    {
        return (float) ($this->precio_compra * $this->stock);
    }

    /**
     * Get valor total del stock a precio de venta.
     */
    public function getValorStockVentaAttribute(): float
    {
        return (float) ($this->precio_venta * $this->stock);
    }
}
