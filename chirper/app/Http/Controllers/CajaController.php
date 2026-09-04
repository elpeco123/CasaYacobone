<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CajaController extends Controller
{
    /**
     * Muestra la vista de gestión y estado de Caja.
     */
    public function index(): View
    {
        $hoy = Carbon::today();
        $cajaHoy = Caja::with('user')->whereDate('fecha', $hoy)->latest()->first();

        // Ventas del día en efectivo
        $ventasHoyEfectivo = Venta::whereDate('created_at', $hoy)
            ->where('tipo_pago', 'efectivo')
            ->sum('total');

        $ventasHoyTarjeta = Venta::whereDate('created_at', $hoy)
            ->where('tipo_pago', 'tarjeta')
            ->sum('total');

        $ventasHoyFactura = Venta::whereDate('created_at', $hoy)
            ->where('tipo_pago', 'factura')
            ->sum('total');

        $montoInicial = $cajaHoy ? (float) $cajaHoy->monto_inicial : 0.0;
        $totalEfectivoEnCaja = $montoInicial + $ventasHoyEfectivo;
        $totalVendidoHoy = $ventasHoyEfectivo + $ventasHoyTarjeta + $ventasHoyFactura;

        // Historial de aperturas de caja
        $historialCajas = Caja::with('user')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->take(20)
            ->get();

        return view('caja.index', compact(
            'cajaHoy',
            'montoInicial',
            'ventasHoyEfectivo',
            'ventasHoyTarjeta',
            'ventasHoyFactura',
            'totalEfectivoEnCaja',
            'totalVendidoHoy',
            'historialCajas'
        ));
    }

    /**
     * Registra o actualiza la apertura / cambio inicial de la caja de hoy.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'monto_inicial' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ], [
            'monto_inicial.required' => 'El monto inicial de cambio es obligatorio.',
            'monto_inicial.numeric' => 'El monto debe ser un número válido.',
            'monto_inicial.min' => 'El monto no puede ser negativo.',
        ]);

        $hoy = Carbon::today();
        $caja = Caja::whereDate('fecha', $hoy)->latest()->first();

        if ($caja) {
            $caja->update([
                'user_id' => Auth::id(),
                'monto_inicial' => $validated['monto_inicial'],
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            $mensaje = 'Cambio en caja actualizado correctamente a $' . number_format($caja->monto_inicial, 0, ',', '.');
        } else {
            $caja = Caja::create([
                'user_id' => Auth::id(),
                'fecha' => $hoy,
                'monto_inicial' => $validated['monto_inicial'],
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            $mensaje = 'Caja del día abierta exitosamente con $' . number_format($caja->monto_inicial, 0, ',', '.') . ' de cambio inicial.';
        }

        return redirect()->back()->with('success', $mensaje);
    }
}
