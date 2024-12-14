<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Project</th>
            <th>Nama Asset</th>
            <th>Nama Karyawan</th>
            <th>Tanggal Awal</th>
            <th>Tanggal Akhir</th>
            <th>Petty Cash</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assets as $index => $item)
            @php
                $assetList = \App\Models\Asset::whereIn('id', is_array($item->asset_id) ? $item->asset_id : [])->get();
                $employeeList = \App\Models\Employee::whereIn('id', is_array($item->employee_id) ? $item->employee_id : [])->get();
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $assetList->sortBy('id')->map(function($asset) {
                    return Crypt::decrypt($asset->id) . ' - ' . $asset->name . ' - ' . $asset->license_plate;
                })->join(', ') }}</td>
                <td>{{ $employeeList->pluck('name')->join(', ') }}</td>
                <td>{{ $item->start_date ?? '-' }}</td>
                <td>{{ $item->end_date ?? '-' }}</td>
                <td>{{ number_format($item->petty_cash) }}</td>
                <td>{{ $item->calculation_method }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

