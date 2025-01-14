<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Project Report</title>
</head>

<body>
    <table class="datatables table table-striped table-poppins " id="data-table">
        <thead class="border-top">
            <tr>
                <th>ID</th>
                <th>Nama Project</th>
                <th>ID Asset</th>
                <th>Nama Karyawan</th>
                <th>Periode Awal</th>
                <th>Periode Akhir</th>
                <th>Nilai Project</th>
                <th>Petty Cash</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
                <tr>
                    <td>{{ $item->format_id ?? '-' }}</td>
                    <td>{{ $item->name ?? '-' }}</td>
                    <td>{{ implode(', ', $item->asset_id) ?? '-' }}</td>
                    <td>{{ $item->employees ?? '-' }}</td>
                    <td>{{ $item->start_date ?? '-' }}</td>
                    <td>{{ $item->end_date ?? '-' }}</td>
                    <td>{{ number_format($item->value_project, 0, ',', '.') ?? '-' }}</td>
                    <td>{{ number_format($item->petty_cash, 0, ',', '.') ?? '-' }}</td>
                    <td>{{ $item->calculation_method ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
