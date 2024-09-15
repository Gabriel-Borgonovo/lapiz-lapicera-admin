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
        .table th, .table td { 
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

    <!-- Información de la venta -->
    <p>Fecha: {{ $sale->created_at->format('d/m/Y') }}</p> <!-- Fecha de emisión -->

    <!-- Tabla de productos -->
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Lista de productos -->
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
            </tr>
            @endforeach

            <!-- Recargo o Descuento -->
            @if ($sale->surcharge_percent > 0)
            <tr>
                <td colspan="3">{{ __('Recargo del ') }}{{ $sale->surcharge_percent }}% {{ __(' por pago con tarjeta y/o QR.') }}</td>
                <td>{{ number_format($sale->total_amount * ($sale->surcharge_percent / 100), 2) }}</td>
            </tr>
            @elseif ($sale->discount_percent > 0)
            <tr>
                <td colspan="3">{{ __('Descuento del ') }}{{ $sale->discount_percent }}% {{ __(' por promoción vigente.') }}</td>
                <td>-{{ number_format($sale->total_amount * ($sale->discount_percent / 100), 2) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <!-- Total final -->
            <tr>
                <th colspan="3" style="text-align: right;">Monto Total:</th>
                <th>{{ number_format($sale->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
