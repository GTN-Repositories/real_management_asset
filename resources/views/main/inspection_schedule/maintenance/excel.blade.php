        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Project</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $fuel)
                    <tr>
                        <td>{{ $fuel->name }}</td>
                        <td>{{ $fuel->jobTitle->name ?? null }}</td>
                        <td>{{ $fuel->managementProject->name ?? null }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
