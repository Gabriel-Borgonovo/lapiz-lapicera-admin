<!DOCTYPE html>
<html>
<head>
    <title>Ticket de Venta</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Ticket de Venta #{{ $ticketNumber }}</h1>
    <p>Fecha: {{ $sale->created_at->format('d/m/Y') }}</p>
    <p>Cliente: {{ $sale->client_name ?? 'N/A' }}</p>

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
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit_price }}</td>
                <td>{{ $item->quantity * $item->unit_price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Total antes de ajustes: ${{ $sale->total_before_adjustments }}</p>
    <p>Descuento: {{ $sale->discount_percent }}%</p>
    <p>Recargo: {{ $sale->surcharge_percent }}%</p>
    <h3>Total Final: ${{ $sale->total_amount }}</h3>
</body>
</html>