<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Stok Barang Rendah</title>
</head>
<body>
    <h1>Pengingat Stok Barang Rendah</h1>
    <p>Halo,</p>
    <p>Stok untuk item <strong>{{ $itemName }}</strong> saat ini tinggal <strong>{{ $quantity }}</strong>, sementara batas minimum adalah <strong>{{ $minimumStock }}</strong>.</p>
    <p>Mohon segera lakukan pengadaan untuk item ini.</p>
    <p>Terima kasih.</p>
</body>
</html>
