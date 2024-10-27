<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Consumption Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Fuel Consumption Report</h1>

    <!-- Chart Image -->
    <h3>Fuel Consumption Over Time</h3>
    @if ($chartImage)
        <img src="{{ $chartImage }}" alt="Fuel Consumption Chart" style="width:100%; height:auto;">
    @else
        <p>No chart data available.</p>
    @endif

    <!-- Table Data -->
    <h3>Fuel Consumption Data</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Nama Project</th>
                <th>Nama Aset</th>
                <th>Banyak Penggunaan (Liter)</th>
                <th>Harga/Liter</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->management_project->name }}</td>
                    <td>{{ $item->asset->name }}</td>
                    <td>{{ $item->liter }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->liter * $item->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
