        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Nama Project</th>
                    <th>Asset ID</th>
                    <th>Nama Karyawan</th>
                    <th>Hours</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Soil Type ID</th>
                    <th>Kilometer</th>
                    <th>Loadsheet</th>
                    <th>Per Load</th>
                    <th>Lose Factor</th>
                    <th>Cubication</th>
                    <th>Price</th>
                    <th>Billing Status</th>
                    <th>Remarks</th>
                    <th>BPIT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $fuel)
                    <tr>
                        <td>{{ $fuel->date }}</td>
                        <td>{{ $fuel->management_project->name }}</td>
                        <td>{{ $fuel->asset_id }}</td>
                        <td>{{ $fuel->employee->name }}</td>
                        <td>{{ $fuel->hours }}</td>
                        <td>{{ $fuel->type }}</td>
                        <td>{{ $fuel->location }}</td>
                        <td>{{ $fuel->soilType->name }}</td>
                        <td>{{ $fuel->kilometer }}</td>
                        <td>{{ $fuel->loadsheet }}</td>
                        <td>{{ $fuel->perload }}</td>
                        <td>{{ $fuel->lose_factor }}</td>
                        <td>{{ $fuel->cubication }}</td>
                        <td>{{ $fuel->price }}</td>
                        <td>{{ $fuel->billing_status }}</td>
                        <td>{{ $fuel->remarks }}</td>
                        <td>{{ $fuel->bpit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
