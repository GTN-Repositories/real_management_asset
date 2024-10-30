<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \Carbon\Carbon::now()->format('F') }}</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px;
        }
    </style>
</head>

<body>
    <h1>Data Penggunaan Bahan Bakar Periode {{ \Carbon\Carbon::now()->format('F') }}</h1>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="text-align: center;">No.</th>
                <th rowspan="2" style="text-align: center;">Unit</th>
                <th rowspan="2" style="text-align: center;">Nama Driver</th>
                <th colspan="{{ (int) \Carbon\Carbon::now()->daysInMonth }}" style="text-align: center;">
                    {{ \Carbon\Carbon::now()->format('F') }}
                </th>
            </tr>
            <tr>
                @for ($day = 1; $day <= \Carbon\Carbon::now()->daysInMonth; $day++)
                    <th style="text-align: center;">{{ $day }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($fuelConsumptions as $index => $data)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="text-align: center;">{{ $data['asset_name'] }}</td>
                    <td style="text-align: center;">{{ $data['driver_name'] }}</td>
                    @for ($day = 1; $day <= \Carbon\Carbon::now()->daysInMonth; $day++)
                        <td
                            style="text-align: center; background-color: {{ isset($data['daily_consumption'][$day]) ? '#90EE90' : 'transparent' }};">
                            {{ $data['daily_consumption'][$day] ?? '' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
