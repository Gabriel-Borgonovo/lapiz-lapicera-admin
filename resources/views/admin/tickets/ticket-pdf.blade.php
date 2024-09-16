<!DOCTYPE html>
<html>

<head>
    <title>Ticket de Venta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header p {
            font-size: 18px;
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Encabezado de la empresa -->
    <div class="header">
        <h1>Lápiz Lapicera - Pago Fácil</h1> <!-- Nombre de la empresa -->
        <p>Ticket de Venta #{{ $ticketNumber }}</p> <!-- Número del ticket -->
    </div>

    <!-- Información del cliente -->
    <p>Fecha: {{ $sale->created_at->format('d/m/Y') }}</p> <!-- Fecha de emisión -->
    <p>Nombre del Cliente: {{ $sale->client_name ?? 'N/A' }}</p> <!-- Nombre del cliente -->
    <p>Nombre de la Empresa: {{ $sale->client_company ?? 'N/A' }}</p> <!-- Nombre de la empresa -->

    <!-- Tabla unificada de productos, recargos y descuentos -->
    <table class="table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <!-- Lista de productos -->
            @foreach ($sale->saleItems as $item)
                <tr>
                    <td>{{ $item->product->name }} (x{{ $item->quantity }})</td>
                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
            @endforeach

            <!-- Recargo o Descuento -->
            @if ($sale->surcharge_percent > 0)
                <tr>
                    <td>{{ __('Recargo del ') }}{{ $sale->surcharge_percent }}%
                        {{ __(' por pago con tarjeta y/o QR.') }}</td>
                    <td>${{ number_format($sale->total_before_adjustments * ($sale->surcharge_percent / 100), 2) }}
                    </td>
                </tr>
            @elseif ($sale->discount_percent > 0)
                <tr>
                    <td>{{ __('Descuento del ') }}{{ $sale->discount_percent }}% {{ __(' por promoción vigente.') }}
                    </td>
                    <td>- ${{ number_format($sale->total_before_adjustments * ($sale->discount_percent / 100), 2) }}
                    </td>
                </tr>
            @endif

        </tbody>
        <tfoot>
            <!-- Total final -->
            <tr>
                <th style="text-align: right;">Monto Total:</th>
                <th>${{ number_format($sale->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
