<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Report</title>
</head>

<body>
    <h1>Assets Report</h1>

    <table>
        <thead>
            <tr>
                <th style="text-align: center;" colspan="2">No.</th>
                <th style="text-align: center;" colspan="2">No Asset</th>
                <th style="text-align: center;" colspan="2">Merek</th>
                <th style="text-align: center;" colspan="2">Unit</th>
                <th style="text-align: center;" colspan="2">Kategori</th>
                <th style="text-align: center;" colspan="2">No Polisi</th>
                <th style="text-align: center;" colspan="2">Klasifikasi</th>
                <th style="text-align: center;" colspan="2">Nomor Seri</th>
                <th style="text-align: center;" colspan="2">Nomor Model</th>
                <th style="text-align: center;" colspan="2">No Mesin</th>
                <th style="text-align: center;" colspan="2">NIK</th>
                <th style="text-align: center;" colspan="2">Warna</th>
                <th style="text-align: center;" colspan="2">Pemilik</th>
                <th style="text-align: center;" colspan="2">Project</th>
                <th style="text-align: center;" colspan="2">Lokasi</th>
                <th style="text-align: center;" colspan="2">Pic</th>
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
                        <td style="text-align: center;" colspan="2">{{ $asset->no_asset ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->name ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->unit ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->category ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->no_polisi ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->klasifikasi ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->serial_number ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->model_number ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->no_mesin ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->nik ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->warna ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">{{ $asset->manager ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">N/A</td> <!-- No management project -->
                        <td style="text-align: center;" colspan="2">{{ $asset->assets_location ?? 'N/A' }}</td>
                        <td style="text-align: center;" colspan="2">
                            {{-- image --}}
                        </td>
                        <td style="text-align: center;" colspan="2">{{ $asset->status ?? 'N/A' }}</td>
                    </tr>
                    @php
                        $assetIndex++;
                    @endphp
                @else
                    @foreach ($asset->managementProjects as $management)
                        <tr>
                            <td style="text-align: center;" colspan="2">{{ $loop->parent->iteration }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->no_asset ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->name ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->unit ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->category ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->no_polisi ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->klasifikasi ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->serial_number ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->model_number ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->no_mesin ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->nik ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->warna ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->manager ?? 'N/A' }}</td>
                            <td style="text-align: center;" colspan="2">{{ $management->name }}</td>
                            <td style="text-align: center;" colspan="2">{{ $asset->assets_location ?? 'N/A' }}
                            </td>
                            <td style="text-align: center;" colspan="2">
                                {{-- image --}}
                            </td>
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
