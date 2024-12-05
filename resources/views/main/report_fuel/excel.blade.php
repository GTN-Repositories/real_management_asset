<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Consumption Report</title>
</head>

<body>
    <h1>Fuel Consumption Report</h1>

    <!-- Table Data -->
    <table>
        <thead>
            <tr>
                <th style="text-align: center;" rowspan="2" colspan="2">No.</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Management Project</th>
                <th style="text-align: center;" rowspan="2" colspan="2">SN</th>
                <th style="text-align: center;" rowspan="2" colspan="4">Unit</th>
                <th style="text-align: center;" colspan="4">Periode</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Total Hari</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Pemakaian Solar</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Total Loadsheet</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Liter/Trip</th>
                <th style="text-align: center;" rowspan="2" colspan="2">Rata-rata/Hari</th>
            </tr>
            <tr>
                <th style="text-align: center;" colspan="2">Awal</th>
                <th style="text-align: center;" colspan="2">Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fuelConsumptions as $index => $fuel)
                <tr>
                    <td style="text-align: center;" colspan="2">{{ $index + 1 }}</td>
                    <td style="text-align: center;" colspan="2">{{ $fuel->management_project->name ?? 'N/A' }}</td>
                    <td style="text-align: center;" colspan="2">{{ $fuel->asset->serial_number ?? 'N/A' }}</td>
                    <td style="text-align: center;" colspan="4">
                        {{ $fuel->asset->license_plate . ' - ' . $fuel->asset->name . ' - ' . $fuel->asset->asset_number ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;" colspan="2">
                        {{ \Carbon\Carbon::parse($fuel->management_project->start_date)->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="2">
                        {{ \Carbon\Carbon::parse($fuel->management_project->end_date)->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="2">
                        {{ \Carbon\Carbon::parse($fuel->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($fuel->management_project->end_date)) }}
                    </td>
                    <td style="text-align: center;" colspan="2">{{ $fuel->liter }}</td>
                    <td style="text-align: center;" colspan="2">
                        {{ $fuel->loadsheetsManagement()->where('asset_id', $fuel->asset_id)->sum('loadsheet') }}
                    </td>

                    <td style="text-align: center;" colspan="2">
                        {{ number_format($fuel->liter /max($fuel->loadsheetsManagement()->where('asset_id', $fuel->asset_id)->sum('loadsheet'),1),2) }}
                    </td>
                    <td style="text-align: center;" colspan="2">
                        {{ number_format($fuel->liter / max(\Carbon\Carbon::parse($fuel->management_project->start_date)->diffInDays(\Carbon\Carbon::parse($fuel->management_project->end_date)), 1), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
