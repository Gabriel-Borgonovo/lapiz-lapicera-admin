<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Caja del ') }} {{ $date }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #000; }
        h3 { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>{{ __('Caja del día ') }} {{ $date }}</h2>

    <table>
        <thead>
            <tr>
                <th>{{ __('Índice') }}</th>
                <th>{{ __('Descripción') }}</th>
                <th>{{ __('Monto') }}</th>
                <th>{{ __('Tipo') }}</th>
                <th>{{ __('Fecha') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIncome = 0;
                $totalExpenses = 0;
            @endphp

            <!-- Mostrar Ingresos -->
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td> <!-- Mostrar el índice de la venta -->
                    <td>{{ $sale->client_name ?? 'Venta sin cliente' }}</td>
                    <td>${{ $sale->total_amount }}</td>
                    <td>{{ __('Ingreso') }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                </tr>
                @php
                    $totalIncome += $sale->total_amount;
                @endphp
            @endforeach

            <!-- Mostrar Egresos -->
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $loop->iteration + count($sales) }}</td> <!-- Continuar el índice después de los ingresos -->
                    <td>{{ $expense->description }}</td>
                    <td>- ${{ $expense->amount }}</td>
                    <td>{{ __('Egreso') }}</td>
                    <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                </tr>
                @php
                    $totalExpenses += $expense->amount;
                @endphp
            @endforeach
        </tbody>
    </table>

    <h3>{{ __('Resumen del Día') }}</h3>
    <p>{{ __('Total de Ingresos: ') }} ${{ $totalIncome }}</p>
    <p>{{ __('Total de Egresos: ') }} - ${{ $totalExpenses }}</p>
    <p>{{ __('Saldo Neto: ') }} ${{ $totalIncome - $totalExpenses }}</p>
</body>
</html>


