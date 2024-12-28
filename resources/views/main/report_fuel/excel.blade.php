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
                <th style="text-align: center;" rowspan="2" colspan="1">No.</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Management Project</th>
                <th style="text-align: center;" rowspan="2" colspan="1">SN</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Unit</th>
                <th style="text-align: center;" colspan="2">Periode</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Total Hari</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Pemakaian Solar</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Total Loadsheet</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Liter/Trip</th>
                <th style="text-align: center;" rowspan="2" colspan="1">Rata-rata/Hari</th>
            </tr>
            <tr>
                <th style="text-align: center;" colspan="1">Awal</th>
                <th style="text-align: center;" colspan="1">Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedFuelConsumptions = $fuelConsumptions->groupBy('asset_id');
                $startDate = \Carbon\Carbon::parse($startDate);
                $endDate = \Carbon\Carbon::parse($endDate);
                $groupedFuelConsumptions = $groupedFuelConsumptions->sortByDesc(function ($group) {
                    return $group
                        ->first()
                        ->loadsheetsManagement()
                        ->where('asset_id', $group->first()->asset_id)
                        ->sum('loadsheet');
                });
            @endphp

            @foreach ($groupedFuelConsumptions as $assetId => $group)
                @php
                    $firstFuel = $group->first();
                    $totalLiter = $group->sum('liter');
                    $totalLoadsheet = $group
                        ->first()
                        ->loadsheetsManagement()
                        ->where('asset_id', $assetId)
                        ->sum('loadsheet');
                    $days = $startDate->diffInDays($endDate);
                @endphp

                <tr>
                    <td style="text-align: center;" colspan="1">{{ $loop->iteration }}</td>
                    <td style="text-align: center;" colspan="1">{{ $firstFuel->management_project->name ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;" colspan="1">{{ $firstFuel->asset->serial_number ?? '' }}</td>
                    <td style="text-align: center;" colspan="1">
                        {{ $firstFuel->asset->license_plate . ' - ' . $firstFuel->asset->name . ' - ' . $firstFuel->asset->asset_number ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;" colspan="1">
                        {{ $startDate->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="1">
                        {{ $endDate->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="1">{{ $days }}</td>
                    <td style="text-align: center;" colspan="1">{{ $totalLiter }}</td>
                    <td style="text-align: center;" colspan="1">{{ $totalLoadsheet }}</td>
                    <td style="text-align: center;" colspan="1">
                        {{ number_format($totalLiter / max($totalLoadsheet, 1), 2) }}
                    </td>
                    <td style="text-align: center;" colspan="1">
                        {{ number_format($totalLiter / max($days, 1), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
