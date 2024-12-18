        <table>
            <thead>
                <tr>
                    <th>Nama Project</th>
                    <th>Asset ID</th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Liter</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Lasted KM Asset</th>
                    {{-- <th>Loadsheet</th> --}}
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $fuel)
                    <tr>
                        <td>{{ $fuel->management_project->name }}</td>
                        <td>{{ 'AST - '. $fuel->asset_id }}</td>
                        <td>{{ $fuel->employee->name }}</td>
                        <td>{{ $fuel->date }}</td>
                        <td>{{ $fuel->liter }}</td>
                        <td>{{ $fuel->price }}</td>
                        <td>{{ $fuel->category }}</td>
                        <td>{{ $fuel->lasted_km_asset }}</td>
                        {{-- <td>{{ $fuel->loadsheet }}</td> --}}
                        <td>{{ $fuel->hours }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
