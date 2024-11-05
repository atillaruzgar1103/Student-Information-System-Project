<?php
session_start();

// SQL Server bağlantısı
$serverName = "ATILLAHP\\SQLEXPRESS"; // SQL Server adı
$connectionOptions = array(
    "Database" => "ogrencikayit",
    "Uid" => "", // Kullanıcı adı (Windows kimlik doğrulaması kullanıyorsanız boş bırakın)
    "PWD" => ""  // Şifre (Windows kimlik doğrulaması kullanıyorsanız boş bırakın)
);

// Bağlantıyı oluştur
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Bağlantı kontrolü
if ($conn === false) {
    die("Veritabanına bağlanılamadı: " . print_r(sqlsrv_errors(), true));
}

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['tc_no'])) {
    header("Location: akademisyengiris.php");
    exit;
}

$successMessage = "";
$errorMessage = "";

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tcno = $_POST['tc_no'] ?? '';
    $ad = ucwords(strtolower($_POST['ad'])); 
    $soyad = ucwords(strtolower($_POST['soyad'])); 
    $bolum = $_POST['bolum'] ?? '';
    $sinif = $_POST['sinif'] ?? '';
    $ogr_no = $_POST['ogr_no'] ?? null; // Öğrenci numarasını al
    $rol = "Öğrenci";

    if (!empty($tcno) && !empty($ad) && !empty($soyad) && !empty($bolum) && !empty($sinif)) {
        // Aynı TC numarasına sahip öğrenci var mı kontrol et
        $sql_check = "SELECT COUNT(*) AS count FROM ogrenciler WHERE tc_no = ? AND ogr_no != ?";
        $params_check = array($tcno, $ogr_no);
        $stmt_check = sqlsrv_query($conn, $sql_check, $params_check);

        if ($stmt_check === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);

        if ($row['count'] > 0) {
            $errorMessage = "Bu TC numarası ile kayıtlı bir öğrenci zaten mevcut.";
        } else {
            if ($ogr_no) {
                // Güncelleme sorgusu
                $sql = "UPDATE ogrenciler SET tc_no = ?, ad = ?, soyad = ?, bolum = ?, sinif = ? WHERE ogr_no = ?";
                $params = array($tcno, $ad, $soyad, $bolum, $sinif, $ogr_no);
            } else {
                // Ekleme sorgusu
                $sql = "INSERT INTO ogrenciler (tc_no, ad, soyad, bolum, sinif, sifre) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array($tcno, $ad, $soyad, $bolum, $sinif, $tcno);
            }

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                $successMessage = "Öğrenci başarıyla " . ($ogr_no ? "güncellendi" : "kaydedildi") . "!";
            }
        }
    } else {
        $errorMessage = "Tüm alanlar doldurulmalıdır.";
    }
}

// Öğrenci bilgilerini alma (güncelleme için)
$ogr_no = $_GET['ogr_no'] ?? null;
$student = null;

if ($ogr_no) {
    $sql = "SELECT * FROM ogrenciler WHERE ogr_no = ?";
    $params = array($ogr_no);
    $stmt = sqlsrv_query($conn, $sql, $params);
    $student = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

// Bağlantıyı kapat
sqlsrv_close($conn);
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Öğrenci Kayıt/Güncelle</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-image: url("bg.svg");
            background-size: cover; 
            background-position: center; /
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
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <h2><?php echo $student ? "Öğrenci Güncelle" : "Öğrenci Kayıt"; ?></h2>
        <form method="post" action="">
            <input type="hidden" name="ogr_no" value="<?php echo htmlspecialchars($student['ogr_no'] ?? ''); ?>">
            <div class="mb-3">
                <label for="tc_no" class="form-label">TC Numarası</label>
                <input type="text" class="form-control transparent-input" id="tc_no" name="tc_no" value="<?php echo htmlspecialchars($student['tc_no'] ?? ''); ?>" required style="width: 400px;">
            </div>
            <div class="mb-3">
                <label for="ad" class="form-label">Ad</label>
                <input type="text" class="form-control transparent-input " id="ad" name="ad" value="<?php echo htmlspecialchars($student['ad'] ?? ''); ?>" required style="width: 400px;">
            </div>

            <div class="mb-3">
                <label for="soyad" class="form-label">Soyad</label>
                <input type="text" class="form-control transparent-input" id="soyad" name="soyad" value="<?php echo htmlspecialchars($student['soyad'] ?? ''); ?>" required style="width: 400px;">
            </div>
            <div class="mb-3">
                <label for="bolum" class="form-label">Bölüm</label>
                <select class="form-select transparent-input " id="bolum" name="bolum" required style="width: 400px;">
                    <option value="" disabled <?php echo !$student ? "selected" : ""; ?>>Seçiniz</option>
                    <option value="Yazılım Mühendisliği" <?php echo ($student && $student['bolum'] === 'Yazılım Mühendisliği') ? "selected" : ""; ?>>Yazılım Mühendisliği</option>
                    <option value="Mekatronik Mühendisliği" <?php echo ($student && $student['bolum'] === 'Mekatronik Mühendisliği') ? "selected" : ""; ?>>Mekatronik Mühendisliği</option>
                    <option value="Bilgisayar programcılığı" <?php echo ($student && $student['bolum'] === 'Bilgisayar programcılığı') ? "selected" : ""; ?>>Bilgisayar programcılığı</option>
                    <option value="Bilgisayar Mühendisliği" <?php echo ($student && $student['bolum'] === 'Bilgisayar Mühendisliği') ? "selected" : ""; ?>>Bilgisayar Mühendisliği</option>
                    <option value="Makine Mühendisliği" <?php echo ($student && $student['bolum'] === 'Makine Mühendisliği') ? "selected" : ""; ?>>Makine Mühendisliği</option>
                    <option value="Elektrik Elektronik Mühendisliği" <?php echo ($student && $student['bolum'] === 'Elektrik Elektronik Mühendisliği') ? "selected" : ""; ?>>Elektrik Elektronik Mühendisliği</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="sinif" class="form-label">Sınıf</label>
                <select class="form-select transparent-input" id="sinif" name="sinif" required style="width: 400px;">
                    <option value="" disabled <?php echo !$student ? "selected" : ""; ?>>Seçiniz</option>
                    <option value="Hazırlık" <?php echo ($student && $student['sinif'] === 'Hazırlık') ? "selected" : ""; ?>>Hazırlık</option>
                    <option value="1. Sınıf" <?php echo ($student && $student['sinif'] === '1. Sınıf') ? "selected" : ""; ?>>1. Sınıf</option>
                    <option value="2. Sınıf" <?php echo ($student && $student['sinif'] === '2. Sınıf') ? "selected" : ""; ?>>2. Sınıf</option>
                    <option value="3. Sınıf" <?php echo ($student && $student['sinif'] === '3. Sınıf') ? "selected" : ""; ?>>3. Sınıf</option>
                    <option value="4. Sınıf" <?php echo ($student && $student['sinif'] === '4. Sınıf') ? "selected" : ""; ?>>4. Sınıf</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $student ? "Güncelle" : "Kaydet"; ?></button>
        </form>
        <a href="ogrenci_listesi.php" class="btn btn-secondary mt-3">Öğrenci Listesi</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
