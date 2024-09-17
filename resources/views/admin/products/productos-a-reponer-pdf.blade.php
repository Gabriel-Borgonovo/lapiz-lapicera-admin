<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Compras</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Título principal -->
    <div class="header">
        <h1>Lista de productos a reponer</h1>
        <!-- Fecha actual -->
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Productos sin stock -->
    <h2>Productos sin stock</h2>
    @if($productosSinStock->isEmpty())
        <p>No hay productos sin stock.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código de Barras</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosSinStock as $index => $producto)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $producto->barcode }}</td>
                        <td>{{ $producto->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Productos al límite de stock -->
    <h2>Productos al límite de stock</h2>
    @if($productosAlLimite->isEmpty())
        <p>No hay productos al límite de stock.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código de Barras</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosAlLimite as $index => $producto)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $producto->barcode }}</td>
                        <td>{{ $producto->name }}</td>
                        <td>{{ $producto->stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>

