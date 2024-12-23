<table>
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">ID Asset</th>
            <th style="text-align: center;">name</th>
            <th style="text-align: center;">asset number</th>
            <th style="text-align: center;">Total Loadsheet</th>
            <th style="text-align: center;">liter</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->format_id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->asset_number }}</td>
                <td>{{ $item->total_loadsheet }}</td>
                <td>{{ $item->liter }}</td>
            </tr>
        @endforeach
    </tbody>
</table>