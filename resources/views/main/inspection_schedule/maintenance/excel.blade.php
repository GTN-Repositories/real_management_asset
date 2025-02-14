<table>
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>date</th>
            <th>name</th>
            <th>Asset</th>
            <th>Inspeksi ID</th>
            <th>Mekanik</th>
            <th>workshop</th>
            <th>status</th>
            <th>code_delay</th>
            <th>delay_reason</th>
            <th>estimate_finish</th>
            <th>delay_hours</th>
            <th>start_maintenace</th>
            <th>end_maintenace</th>
            <th>deviasi</th>
            <th>finish_at</th>
            <th>km</th>
            <th>hm</th>
            <th>location</th>
            <th>detail_problem</th>
            <th>action_to_do</th>
            <th>urgention</th>
            <th>pic</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ 'MNT-' . Illuminate\Support\Facades\Crypt::decrypt($item->id) }}</td>
                <td>{{ $item->date ?? null }}</td>
                <td>{{ $item->name ?? null }}</td>
                <td>{{ isset($item->inspection_schedule) && isset($item->inspection_schedule->asset) ? ('AST - ' . $item->inspection_schedule->asset_id . ' - ' . ($item->inspection_schedule->asset->name ?? null) . ' - ' . ($item->inspection_schedule->asset->serial_number ?? '-')) : '' }}</td>
                <td>{{ isset($item->inspection_schedule) ? ('INS-' . $item->inspection_schedule_id) : '' }}</td>
                <td>{{ $item->employee_id ?? null }}</td>
                <td>{{ $item->workshop ?? null }}</td>
                <td>{{ $item->status ?? null }}</td>
                <td>{{ $item->code_delay ?? null }}</td>
                <td>{{ $item->delay_reason ?? null }}</td>
                <td>{{ $item->estimate_finish ?? null }}</td>
                <td>{{ $item->delay_hours ?? null }}</td>
                <td>{{ $item->start_maintenace ?? null }}</td>
                <td>{{ $item->end_maintenace ?? null }}</td>
                <td>{{ $item->deviasi ?? null }}</td>
                <td>{{ $item->finish_at ?? null }}</td>
                <td>{{ $item->km ?? null }}</td>
                <td>{{ $item->hm ?? null }}</td>
                <td>{{ $item->location ?? null }}</td>
                <td>{{ $item->detail_problem ?? null }}</td>
                <td>{{ $item->action_to_do ?? null }}</td>
                <td>{{ $item->urgention ?? null }}</td>
                <td>{{ $item->pic ?? null }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
