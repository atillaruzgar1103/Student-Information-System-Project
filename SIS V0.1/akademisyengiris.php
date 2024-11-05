<?php
session_start(); // Session'ı başlat

// Veritabanı bağlantısı
$serverName = "ATILLAHP\\SQLEXPRESS"; // SQL Server adı
$connectionOptions = array(
    "Database" => "ogrencikayit",
    "Uid" => "", // Kullanıcı adı (Windows kimlik doğrulaması kullanıyorsanız boş bırakın)
    "PWD" => ""  // Şifre (Windows kimlik doğrulaması kullanıyorsanız boş bırakın)
);

// Bağlantıyı oluştur
$baglan = sqlsrv_connect($serverName, $connectionOptions);

// Bağlantı kontrolü
if ($baglan === false) {
    die("Veritabanına bağlanılamadı: " . print_r(sqlsrv_errors(), true));
}

$successMessage = "";
$errorMessage = "";
$tc_no = "";  // Öğrenci numarası başlangıç değeri
$sifre = "";   // Şifre başlangıç değeri

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tc_no = $_POST['tc_no'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if (!empty($tc_no) && !empty($sifre)) {
        // Sorgu ve parametreler
        $sql_check = "SELECT * FROM akademisyen WHERE tc_no = ?";
        $params_check = array($tc_no);
        $stmt_check = sqlsrv_query($baglan, $sql_check, $params_check);

        if ($stmt_check === false) {
            die("Sorgu sırasında hata oluştu: " . print_r(sqlsrv_errors(), true));
        }

        // Sonucu getir
        $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
        if ($row && $sifre === $row['sifre']) {
            // Giriş başarılı, oturuma TC numarasını ve rolü ekle
            $_SESSION['tc_no'] = $tc_no;
            $_SESSION['rol'] = $row['rol']; 
            header("Location: ogrenci_listesi.php");
            exit;
        } else {
            $errorMessage = "TC numaranız veya şifre yanlış.";
        }
    } else {
        $errorMessage = "Lütfen TC numaranızı ve şifrenizi giriniz.";
    }
}
?>


<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <title>Öğrenci Kayıt Sistemi</title>
    <style>
        body {
            display: flex;
            justify-content: center; /* Yatayda ortala */
            align-items: center;    /* Dikeyde ortala */
            height: 100vh;         /* Tam ekran yüksekliği */
            margin: 0;             /* Varsayılan margin'i kaldır */
        }
        .container {
            width: 500px;          /* Container genişliği */
            padding: 20px;         /* İçerik boşluğu */
            background-color: #f0f0f0; /* Arka plan rengi */
            border-radius: 5px;    /* Kenar yuvarlama */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Gölge efekti */
        }
        h2 {
            margin-bottom: 20px;   /* Container ile başlık arasında boşluk */
            text-align: center;     /* Başlığı ortala */
        }
        .form-label {
            margin-bottom: 5px;    /* Label ile input arasında boşluk */
        }
        .form-control {
            width: 100%;           /* Input alanı genişliği */
            padding: 10px;         /* İçerik boşluğu */
            border: 1px solid #ccc; /* Kenar rengi */
            border-radius: 4px;    /* Kenar yuvarlama */
        }
        div h6 {
            text-align: right; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Akademisyen Girişi</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="card p-5">
            <form method="POST" action="" autocomplete="off">
              
                <div class="mb-3">
                    <label for="tc_no" class="form-label">TC Kimlik Numaranız</label>
                    <input type="text" class="form-control" placeholder=" TC Numaranızı Giriniz." id="tc_no" name="tc_no" value="<?php echo htmlspecialchars($tc_no); ?>" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label" >Şifreniz</label>
                    <input type="password" class="form-control" placeholder="Şifrenizi Giriniz." id="sifre" name="sifre" value="" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş</button>
                <br>
                <br>
                <div class="link">
                    <a href="ogrencigiris.php" style="text-decoration: none;">Öğrenci Girişi</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>