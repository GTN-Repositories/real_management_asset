<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Report</title>
</head>

<body>
    <h1>Assets Export</h1>

    <table>
        <thead>
            <tr>
                <th style="text-align: center;" colspan="2">No</th>
                <th style="text-align: center;" colspan="2">Gambar</th>
                <th style="text-align: center;" colspan="2">ID</th>
                <th style="text-align: center;" colspan="2">Kategori</th>
                <th style="text-align: center;" colspan="2">Merek</th>
                <th style="text-align: center;" colspan="2">Unit</th>
                <th style="text-align: center;" colspan="2">Tipe</th>
                <th style="text-align: center;" colspan="2">Nopol</th>
                <th style="text-align: center;" colspan="2">Classification</th>
                <th style="text-align: center;" colspan="2">No Rangka</th>
                <th style="text-align: center;" colspan="2">No Mesin</th>
                <th style="text-align: center;" colspan="2">NIK</th>
                <th style="text-align: center;" colspan="2">Warna</th>
                <th style="text-align: center;" colspan="2">Pemilik</th>
                <th style="text-align: center;" colspan="2">Location</th>
                <th style="text-align: center;" colspan="2">PIC</th>
                <th style="text-align: center;" colspan="2">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $assetIndex = 1;
            @endphp

            @foreach ($assets as $asset)
                @if ($asset->managementProjects->isEmpty())
                    <tr>
                        <td style="text-align: center;" colspan="2">{{ $assetIndex }}</td>
                        <td style="text-align: center;" colspan="2">
                            {{-- image --}}
                        </td>
                        <td style="text-align: center;" colspan="2">{{ $asset->relationId ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->category ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->name ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->unit ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->type ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->license_plate ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->classification ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->chassis_number ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->machine_number ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->nik ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->color ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->owner ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->assets_location ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->pic ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->status ?? 'N/A' }}</td>
                    </tr>
                    @php
                        $assetIndex++;
                    @endphp
                @else
                    @foreach ($asset->managementProjects as $management)
                        <tr>
                            <td style="text-align: center;" colspan="2">{{ $loop->parent->iteration }}</td>
                            <td style="text-align: center;" colspan="2">
                                {{-- image --}}
                            </td>
                            <td style="text-align: center;" colspan="2">{{ $asset->relationId ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->category ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->name ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->unit ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->type ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->license_plate ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->classification ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->chassis_number ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->machine_number ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->nik ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->color ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->owner ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->assets_location ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->pic ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->status ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                    @php
                        $assetIndex++;
                    @endphp
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>
