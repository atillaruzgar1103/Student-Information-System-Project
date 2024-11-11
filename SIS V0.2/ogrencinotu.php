<?php


session_start();

include 'baglan.php';

if (!isset($_SESSION['ogr_no'])) {
    header("Location: ogrencigiris.php");
    exit;
}

// Oturumdan alınan bilgiler
$ogr_no = $_SESSION['ogr_no']; // Öğrenci numarasını oturumdan al
$ad = isset($_SESSION['ad']) ? $_SESSION['ad'] : 'Ad tanımlı değil'; 
$soyad = isset($_SESSION['soyad']) ? $_SESSION['soyad'] : 'Soyad tanımlı değil';

$notlar = [];

if (!empty($ogr_no)) {
    // Notları sorgulama
    $sqlNotlar = "SELECT * FROM notlar WHERE ogr_no = ?";
    $stmtNotlar = sqlsrv_prepare($baglan, $sqlNotlar, array(&$ogr_no));

    if ($stmtNotlar) {
        sqlsrv_execute($stmtNotlar);

        // Notları diziye ekle
        while ($row = sqlsrv_fetch_array($stmtNotlar, SQLSRV_FETCH_ASSOC)) {
            $notlar[] = $row;
        }
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Notları</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
            display: flex;
            justify-content: flex-start; 
            
            height: 100vh;         
            margin: 0;            
            background-image: url("bg.svg");
            background-size: cover; 
            background-position: center; 
        }
</style>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Notlar</h1>
        <p>Hoş geldiniz, <?php echo htmlspecialchars($ad . " " . $soyad); ?></p>
        
        <!-- Notlar Tablosu -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Ders Adı</th>
                    <th>Vize</th>
                    <th>Final</th>
                    <th>Bütünleme</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notlar as $not): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($not['dersadi']); ?></td>
                        <td><?php echo htmlspecialchars($not['vize']); ?></td>
                        <td><?php echo htmlspecialchars($not['final']); ?></td>
                        <td><?php echo htmlspecialchars($not['butunleme']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bootstrap 5 JS ve Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    </div>
</body>
</html>
