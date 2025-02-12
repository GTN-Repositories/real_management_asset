        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Asset</th>
                    <th>Type</th>
                    <th>Problem</th>
                    <th>Management Project</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>DATE ESTIMATE RFU</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ 'INS-' . Illuminate\Support\Facades\Crypt::decrypt($item->id) }}</td>
                        <td>{{ $item->name ?? null }}</td>
                        <td>{{ Illuminate\Support\Facades\Crypt::decrypt($item->asset->id) . ' - ' . $item->asset->name . ' - ' . $item->asset->license_plate ?? '-' }}</td>
                        <td>{{ $item->type ?? null }}</td>
                        <td>{{ str_replace(['<p>', '</p>'], '', $item->note) ?? null }}</td>
                        <td>{{ $item->managementProject->name ?? null }}</td>
                        <td>{{ $item->location ?? null }}</td>
                        <td>{{ $item->date ?? null }}</td>
                        <td>{{ $item->estimate_finish ?? null }}</td>
                        <td>{{ $item->urgention ?? null }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
