<table>
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama Project</th>
            <th style="text-align: center;">Total Loadsheet</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->project_name }}</td>
                <td>{{ $item->total_loadsheet }}</td>
            </tr>
        @endforeach
    </tbody>
</table>