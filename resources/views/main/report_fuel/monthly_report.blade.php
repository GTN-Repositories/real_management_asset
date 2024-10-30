<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \Carbon\Carbon::now()->format('F') }}</title>
</head>

<body>
    <h1>Data Penggunaan Bahan Bakar Periode {{ \Carbon\Carbon::now()->format('F') }}</h1>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="text-align: center;" colspan="2">No.</th>
                <th rowspan="2" style="text-align: center;" colspan="2">Unit</th>
                <th rowspan="2" style="text-align: center;" colspan="2">Nama Driver</th>
                <th colspan="{{ \Carbon\Carbon::now()->daysInMonth }}" style="text-align: center;">
                    {{ \Carbon\Carbon::now()->format('F') }}</th>
            </tr>
            <tr>
                @for ($day = 1; $day <= \Carbon\Carbon::now()->daysInMonth; $day++)
                    <th style="text-align: center;">{{ $day }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($fuelConsumptions as $index => $consumption)
                <tr>
                    <td style="text-align: center;" colspan="2">{{ $index + 1 }}</td>
                    <td style="text-align: center;" colspan="2">{{ $consumption->asset->name }}</td>
                    <td style="text-align: center;" colspan="2">{{ $consumption->driver_name ?? 'N/A' }}</td>
                    @for ($day = 1; $day <= \Carbon\Carbon::now()->daysInMonth; $day++)
                        @php
                            $consumptionDay = \Carbon\Carbon::parse($consumption->date)->day;
                        @endphp
                        <td
                            style="text-align: center; background-color: {{ $consumptionDay == $day ? 'green' : 'transparent' }};">
                            @if ($consumptionDay == $day)
                                {{ $consumption->liter }}
                            @endif
                        </td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
