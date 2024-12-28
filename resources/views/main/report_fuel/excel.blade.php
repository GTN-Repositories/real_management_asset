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
            @php
                $groupedFuelConsumptions = $fuelConsumptions->groupBy('asset_id');
                $startDate = \Carbon\Carbon::parse($startDate);
                $endDate = \Carbon\Carbon::parse($endDate);
                $groupedFuelConsumptions = $groupedFuelConsumptions->sortByDesc(function ($group) {
                    return $group->first()->loadsheetsManagement()->where('asset_id', $group->first()->asset_id)->sum('loadsheet');
                })->sortBy(function ($group) {
                    return $group->first()->asset->serial_number;
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
                    <td style="text-align: center;" colspan="2">{{ $loop->iteration }}</td>
                    <td style="text-align: center;" colspan="2">{{ $firstFuel->management_project->name ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;" colspan="2">{{ $firstFuel->asset->serial_number ?? '' }}</td>
                    <td style="text-align: center;" colspan="4">
                        {{ $firstFuel->asset->license_plate . ' - ' . $firstFuel->asset->name . ' - ' . $firstFuel->asset->asset_number ?? 'N/A' }}
                    </td>
                    <td style="text-align: center;" colspan="2">
                        {{ $startDate->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="2">
                        {{ $endDate->format('d-M-y') }}</td>
                    <td style="text-align: center;" colspan="2">{{ $days }}</td>
                    <td style="text-align: center;" colspan="2">{{ $totalLiter }}</td>
                    <td style="text-align: center;" colspan="2">{{ $totalLoadsheet }}</td>
                    <td style="text-align: center;" colspan="2">
                        {{ number_format($totalLiter / max($totalLoadsheet, 1), 2) }}
                    </td>
                    <td style="text-align: center;" colspan="2">
                        {{ number_format($totalLiter / max($days, 1), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
