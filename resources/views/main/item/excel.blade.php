<table class="table">
    <thead>
        <tr>
            <th>Part Number</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Kode Barang</th>
            <th>Status</th>
            <th>Merek</th>
            <th>Stock</th>
            <th>Nomor Invoice</th>
            <th>Nama Supplier</th>
            <th>Alamat Supplier</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->part }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->stock }}</td>
                <td>{{ $item->no_invoice }}</td>
                <td>{{ $item->supplier_name }}</td>
                <td>{{ $item->supplier_addrees }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
