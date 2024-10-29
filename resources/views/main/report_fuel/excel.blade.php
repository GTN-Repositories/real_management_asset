<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Consumption Report</title>
    <style>
        /* Set general table styling */
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: auto;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            white-space: nowrap;
        }

        /* Set a specific width for each column */
        th:nth-child(1), td:nth-child(1) {
            width: 50px; /* No. column */
        }

        th:nth-child(2), td:nth-child(2) {
            width: 200px; /* Management Project column */
        }

        th:nth-child(3), td:nth-child(3) {
            width: 150px; /* Unit column */
        }

        th:nth-child(4), th:nth-child(5),
        td:nth-child(4), td:nth-child(5) {
            width: 100px; /* Periode Awal and Akhir columns */
        }

        th:nth-child(6), td:nth-child(6) {
            width: 100px; /* Total Hari */
        }

        th:nth-child(7), td:nth-child(7) {
            width: 120px; /* Pemakaian Solar */
        }

        th:nth-child(8), td:nth-child(8) {
            width: 120px; /* Total Loadsheet */
        }

        th:nth-child(9), td:nth-child(9) {
            width: 120px; /* Liter/Trip */
        }

        th:nth-child(10), td:nth-child(10) {
            width: 150px; /* Rata-rata/Hari */
        }

        /* Styling for header */
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Alternate row colors for better readability */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h1>Fuel Consumption Report</h1>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Management Project</th>
                <th rowspan="2">Unit</th>
                <th colspan="2">Periode</th>
                <th rowspan="2">Total Hari</th>
                <th rowspan="2">Pemakaian Solar</th>
                <th rowspan="2">Total Loadsheet</th>
                <th rowspan="2">Liter/Trip</th>
                <th rowspan="2">Rata-rata/Hari</th>
            </tr>
            <tr>
                <th>Awal</th>
                <th>Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fuelConsumptions as $index => $fuel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $fuel->management_project->name ?? 'N/A' }}</td>
                    <td>{{ $fuel->asset->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($fuel->management_project->start_date)->format('d-M-y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($fuel->management_project->end_date)->format('d-M-y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($fuel->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($fuel->management_project->end_date)) }}</td>
                    <td>{{ $fuel->liter }}</td>
                    <td>{{ $fuel->loadsheet }}</td>
                    <td>{{ number_format($fuel->liter / max($fuel->loadsheet, 1), 2) }}</td>
                    <td>{{ number_format($fuel->liter / max(\Carbon\Carbon::parse($fuel->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($fuel->management_project->end_date)), 1), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
