<?php
include 'baglan.php';

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ogr_no = $_POST['ogr_no'];
    $vize = $_POST['vize'];
    $final = $_POST['final'];
    $butunleme = $_POST['butunleme'];
    $dersadi = $_POST['dersadi'];

    // Not ekleme sorgusu
    $sql = "INSERT INTO notlar (ogr_no, vize, final, butunleme, dersadi) VALUES (?, ?, ?, ?, ?)";
    $params = array($ogr_no, $vize, $final, $butunleme, $dersadi);
    $stmt = sqlsrv_query($baglan, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<div class='alert alert-success' role='alert'>Notlar başarıyla kaydedildi!</div>";
    }
}

// Öğrenci numarasını al
$ogr_no = $_GET['ogr_no'] ?? null;

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Not Girişi</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-image: url("bg.svg");
            background-size: cover; 
            background-position: center; 
        }

        .transparent-input {
    background-color: rgba(211, 211, 211, 0.7); 
    border: 3px solid rgba(211, 211, 211, 0.7); 
    color: black; 


}

    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Öğrenci Not Girişi</h2>
        <form method="post" action="">
            <input type="hidden" name="ogr_no" value="<?php echo htmlspecialchars($ogr_no); ?>">
            <div class="mb-3">
                <label for="vize" class="form-label">Vize Notu</label>
                <input type="number" step="0.01" class="form-control transparent-input" id="vize" name="vize" required style="width: 400px;">
            </div>
            <div class="mb-3">
                <label for="final" class="form-label">Final Notu</label>
                <input type="number" step="0.01" class="form-control transparent-input" id="final" name="final" required style="width: 400px;">
            </div>
            <div class="mb-3">
                <label for="butunleme" class="form-label">Bütünleme Notu</label>
                <input type="number" step="0.01" class="form-control transparent-input" id="butunleme" name="butunleme" style="width: 400px;">
            </div>
            <div class="mb-3">
                 <label for="dersadi" class="form-label">Ders Adı</label>
                 <select class="form-select transparent-input" id="dersadi" name="dersadi" required style="width: 400px;">
                 <option value="" disabled selected>Seçiniz</option>
                 <option value="Matematik">Matematik</option>
                 <option value="Nesne Tabanlı Programlama">Nesne Tabanlı Programlama</option>
                 <option value="Veritabanı">Veritabanı Programlama</option>
                 <option value="Görsel Programlama">Görsel Programlama</option>
                 <option value="Fizik">Fizik</option>
            </select>
            </div>
            <button type="submit" class="btn btn-primary">Notları Kaydet</button>
        </form>
        <a href="ogrenci_listesi.php" class="btn btn-secondary mt-3">Geri Dön</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Bağlantıyı kapat
sqlsrv_close($baglan);
?>