<?php

session_start();

include 'baglan.php';

$successMessage = "";
$errorMessage = "";
$ogr_no = "";  // Öğrenci numarası başlangıç değeri
$sifre = "";   // Şifre başlangıç değeri

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ogr_no = $_POST['ogr_no'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if (!empty($ogr_no) && !empty($sifre)) {
        $sql_check = "SELECT ogr_no, sifre, ad, soyad FROM ogrenciler WHERE ogr_no = ?";
        $params_check = array($ogr_no);
        $stmt_check = sqlsrv_query($baglan, $sql_check, $params_check);

        if ($stmt_check === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
        if ($row) {
            // Şifreyi doğrudan kontrol et
            if ($sifre === $row['sifre']) {
                // Başarılı giriş, oturum değişkenlerini ayarla
                $_SESSION['ogr_no'] = $row['ogr_no']; // Öğrenci numarası
                $_SESSION['ad'] = $row['ad'];         // Öğrenci adı
                $_SESSION['soyad'] = $row['soyad'];   // Öğrenci soyadı
                $_SESSION['rol'] = $row['rol'];

                // Öğrenci paneline yönlendir
                header("Location: ogrencisayfasi.php");
                exit;
            }
            // Öğrenci paneline yönlendir
            header("Location: ogrencisayfasi.php");
            exit;
        } else {
            $errorMessage = "Öğrenci numarası veya şifre yanlış.";
        }
    } else {
        $errorMessage = "Lütfen öğrenci numaranızı ve şifrenizi giriniz.";
    }
}
?>

<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">

    <title>Öğrenci Kayıt Sistemi</title>
    <style>
        body {
            display: flex;
            justify-content: flex-start; 
            align-items: center;    
            height: 100vh;         
            margin: 0;            
            background-image: url("bg.svg");
            background-size: cover; 
            background-position: center; 
        }
        .container {
            width: 500px;         
            padding: 20px;         
            background-color: #f0f0f0; 
            border-radius: 5px;   
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
            margin-left: 80px;
        }
        h2 {
            margin-bottom: 20px;   
            text-align: center;    
        }
        .form-label {
            margin-bottom: 5px;   
        }
        .form-control {
            width: 100%;           
            padding: 10px;         
            border: 1px solid #ccc; 
            border-radius: 4px;   
        }
        div h6 {
            text-align: right; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Öğrenci Girişi</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="card p-5">
            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label for="ogr_no" class="form-label">Öğrenci Numaranız</label>
                    <input type="text" class="form-control" placeholder="Öğrenci Numaranızı Giriniz." id="ogr_no" name="ogr_no" value="<?php echo htmlspecialchars($ogr_no); ?>" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label">Şifreniz</label>
                    <input type="password" class="form-control" placeholder="Varsayılan Şifreniz TC Kimlik Numaranızdır." id="sifre" name="sifre" value="" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş</button>
                <br>
                <br>
                <div class="link">
                    <a href="akademisyengiris.php" style="text-decoration: none;">Akademisyen Girişi</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
