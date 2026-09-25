<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaGasto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CajaController extends Controller
{
    /**
     * Muestra la caja abierta (única, compartida) o el formulario de apertura.
     */
    public function index(): View
    {
        $cajaAbierta = Caja::with('user')->where('estado', Caja::ESTADO_ABIERTA)
            ->latest()
            ->first();

        $ventasPorForma = array_fill_keys(array_keys(Venta::PAGOS), 0.0);
        $ventasEfectivo = 0.0;
        $cantidadVentas = 0;
        $retiros = collect();
        $totalRetiros = 0.0;

        if ($cajaAbierta) {
            $ventasPorForma = Venta::desglosePorPago(Venta::where('caja_id', $cajaAbierta->id));
            $ventasEfectivo = $ventasPorForma['efectivo'];
            $cantidadVentas = Venta::where('caja_id', $cajaAbierta->id)->count();

            $retiros = $cajaAbierta->retiros()->with(['user', 'categoriaGasto'])->latest()->get();
            $totalRetiros = (float) $retiros->sum('monto');
        }

        $categoriasGasto = CategoriaGasto::orderBy('nombre')->get(['id', 'nombre']);

        $montoInicial = $cajaAbierta ? (float) $cajaAbierta->monto_inicial : 0.0;
        $totalEfectivoEnCaja = $montoInicial + $ventasEfectivo - $totalRetiros;
        $totalVendidoCaja = array_sum($ventasPorForma);

        return view('caja.index', compact(
            'cajaAbierta',
            'montoInicial',
            'ventasPorForma',
            'ventasEfectivo',
            'cantidadVentas',
            'retiros',
            'totalRetiros',
            'categoriasGasto',
            'totalEfectivoEnCaja',
            'totalVendidoCaja'
        ));
    }

    /**
     * Abre la caja (período de ventas). Solo una abierta a la vez, la abra quien la abra.
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

        if ($abierta = Caja::abierta()) {
            $quien = $abierta->user?->name ?? 'otro usuario';

            return redirect()->route('caja.index')
                ->with('error', 'Ya hay una caja abierta por '.$quien.' (desde las '.$abierta->fecha_apertura?->format('H:i').'). Cerrala antes de abrir una nueva.');
        }

        $ahora = Carbon::now();
        $caja = Caja::create([
            'user_id' => Auth::id(),
            'fecha' => $ahora->toDateString(),
            'fecha_apertura' => $ahora,
            'estado' => Caja::ESTADO_ABIERTA,
            'monto_inicial' => $validated['monto_inicial'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        return redirect()->route('ventas.index')
            ->with('success', 'Caja #'.$caja->id.' abierta con $'.number_format($caja->monto_inicial, 0, ',', '.').' de cambio inicial. Las ventas se asignan a esta caja.');
    }

    /**
     * Cierra la caja: guarda hora de cierre y totales por medio de pago.
     * Como la caja es única y compartida, puede cerrarla quien esté de turno.
     */
    public function cerrar(Caja $caja): RedirectResponse
    {
        if (! $caja->estaAbierta()) {
            return redirect()->route('caja.index')
                ->with('error', 'La caja #'.$caja->id.' ya está cerrada.');
        }

        $porForma = Venta::desglosePorPago(Venta::where('caja_id', $caja->id));
        $totalRetiros = (float) $caja->retiros()->sum('monto');

        $caja->update([
            'estado' => Caja::ESTADO_CERRADA,
            'fecha_cierre' => Carbon::now(),
            'total_efectivo' => $porForma['efectivo'],
            'total_tarjeta' => $porForma['tarjeta'] ?? 0,
            'total_debito' => $porForma['debito'],
            'total_credito' => $porForma['credito'],
            'total_factura' => $porForma['factura'],
            'total_cuenta_corriente' => $porForma['cuenta_corriente'],
            'total_retiros' => $totalRetiros,
            'cantidad_ventas' => Venta::where('caja_id', $caja->id)->count(),
        ]);

        $caja->refresh();

        return redirect()->route('caja.index')
            ->with('success', 'Caja #'.$caja->id.' cerrada. Vendido: $'.number_format($caja->totalGeneral(), 0, ',', '.').' en '.$caja->cantidad_ventas.' ventas. Efectivo físico final: $'.number_format($caja->efectivoFisico(), 0, ',', '.').'. Abrí una nueva caja para seguir vendiendo.');
    }
}
