<table>
    <thead>
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Management</th>
            <th style="text-align: center;">Asset</th>
            <th style="text-align: center;">Date</th>
            <th style="text-align: center;">Location</th>
            <th style="text-align: center;">Soil Type</th>
            <th style="text-align: center;">BPIT</th>
            <th style="text-align: center;">Kilometer</th>
            <th style="text-align: center;">Loadsheet</th>
            <th style="text-align: center;">Per Load</th>
            <th style="text-align: center;">Factor Lose</th>
            <th style="text-align: center;">Cubication</th>
            <th style="text-align: center;">Price</th>
            <th style="text-align: center;">Billing Status</th>
            <th style="text-align: center;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->management_project_id }}</td>
                <td>{{ $item->asset_id }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ $item->soil_type_id }}</td>
                <td>{{ $item->bpit }}</td>
                <td>{{ $item->kilometer }}</td>
                <td>{{ $item->loadsheet }}</td>
                <td>{{ $item->perload }}</td>
                <td>{{ $item->lose_factor }}</td>
                <td>{{ $item->cubication }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->billing_status }}</td>
                <td>{{ $item->remarks }}</td>
            </tr>
        @endforeach
    </tbody>
</table>