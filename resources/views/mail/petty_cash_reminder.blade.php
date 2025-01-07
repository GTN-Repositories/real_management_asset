<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Permintaan Petty Cash</title>
</head>
<body>
    <h1>Pengingat Permintaan Petty Cash</h1>
    <p>Halo,</p>
    <p>Permintaan petty cash untuk proyek <strong>{{ $projectName }}</strong> sebesar <strong>Rp {{ number_format($amount, 2) }}</strong> telah diajukan pada <strong>{{ $requestDate }}</strong>.</p>
    <p>Mohon segera diproses sebelum batas waktu berakhir.</p>
    <p>Terima kasih.</p>
</body>
</html>
