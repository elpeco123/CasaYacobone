<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'direccion',
    ];

    /**
     * Ventas a cuenta corriente del cliente.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Cobros registrados en el panel de cuentas corrientes.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(CuentaCorrientePago::class);
    }

    public function nombreCompleto(): string
    {
        return $this->apellido.', '.$this->nombre;
    }

    /**
     * Lo que el cliente debe: ventas a cuenta corriente menos cobros.
     */
    public function saldo(): float
    {
        return round((float) $this->ventas()->sum('total') - (float) $this->pagos()->sum('monto'), 2);
    }

    /**
     * Fecha de la venta más vieja que sigue impaga (los cobros se imputan en
     * orden, de la más vieja a la más nueva). Null si no debe nada.
     */
    public function deudaDesde(): ?Carbon
    {
        $restante = (float) $this->pagos()->sum('monto');

        foreach ($this->ventas()->orderBy('created_at')->get(['created_at', 'total']) as $venta) {
            $restante -= (float) $venta->total;
            if ($restante < 0) {
                return $venta->created_at;
            }
        }

        return null;
    }
}
