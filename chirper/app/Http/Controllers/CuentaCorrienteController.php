<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CuentaCorrienteController extends Controller
{
    /**
     * Clientes con cuenta corriente, del que hace más que debe al que menos.
     *
     * Se cobra cada 30 días, así que la fecha de la deuda más vieja manda el orden.
     */
    public function index(Request $request): View
    {
        $soloDeudores = $request->boolean('deudores', true);

        $clientes = Cliente::with(['ventas:id,cliente_id,total,created_at', 'pagos:id,cliente_id,monto'])
            ->get()
            ->map(function (Cliente $cliente) {
                $cliente->saldoActual = $cliente->saldo();
                $cliente->desde = $cliente->deudaDesde();
                $cliente->diasDeuda = $cliente->desde ? (int) $cliente->desde->diffInDays(now(), absolute: true) : null;

                return $cliente;
            })
            ->when($soloDeudores, fn ($lista) => $lista->filter(fn ($c) => $c->saldoActual > 0))
            // Primero la deuda más vieja; los que no deben nada, al final.
            ->sortBy(fn ($c) => $c->desde?->timestamp ?? PHP_INT_MAX)
            ->values();

        return view('cuentas-corrientes.index', [
            'clientes' => $clientes,
            'soloDeudores' => $soloDeudores,
            'totalAdeudado' => $clientes->sum('saldoActual'),
            'vencidos' => $clientes->filter(fn ($c) => $c->saldoActual > 0 && ($c->diasDeuda ?? 0) >= 30)->count(),
        ]);
    }

    /**
     * Detalle del cliente: ventas a cuenta corriente, cobros y saldo.
     */
    public function show(Cliente $cliente): View
    {
        return view('cuentas-corrientes.show', [
            'cliente' => $cliente,
            'ventas' => $cliente->ventas()->with('user')->latest()->get(),
            'pagos' => $cliente->pagos()->with('user')->latest()->get(),
            'saldo' => $cliente->saldo(),
            'desde' => $cliente->deudaDesde(),
        ]);
    }

    /**
     * Registra un cobro: un adelanto o el saldo completo.
     */
    public function pagar(Request $request, Cliente $cliente): RedirectResponse
    {
        $saldo = $cliente->saldo();

        if ($saldo <= 0) {
            return back()->with('error', 'Este cliente no tiene deuda pendiente.');
        }

        $validado = $request->validate([
            'monto' => ['required', 'numeric', 'min:1', 'max:'.$saldo],
            'tipo_pago' => ['required', 'in:'.implode(',', array_keys(Venta::PAGOS))],
            'nota' => ['nullable', 'string', 'max:160'],
        ], [
            'monto.required' => 'Ingresá cuánto está pagando el cliente.',
            'monto.max' => 'El cliente debe $'.number_format($saldo, 0, ',', '.').'. No podés cobrar de más.',
        ]);

        $cliente->pagos()->create([
            'user_id' => Auth::id(),
            'monto' => $validado['monto'],
            'tipo_pago' => $validado['tipo_pago'],
            'nota' => $validado['nota'] ?? null,
        ]);

        $restante = $cliente->saldo();

        return redirect()->route('cuentas-corrientes.show', $cliente)->with(
            'success',
            $restante <= 0
                ? 'Cobro registrado. '.$cliente->nombreCompleto().' quedó sin deuda.'
                : 'Cobro registrado. Queda un saldo de $'.number_format($restante, 0, ',', '.').'.'
        );
    }
}
