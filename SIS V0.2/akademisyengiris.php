<?php

session_start();

// Veritabanı bağlantısı
// Veritabanı bağlantısı
$serverName = "ATILLAHP\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "ogrencikayit", 
    "Uid" => "", 
    "PWD" => "" ,
    "CharacterSet" => "UTF-8"
    
);
$baglan = sqlsrv_connect($serverName, $connectionOptions);
if ($baglan === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Varsayılan değer ataması
$tc_no = $sifre = $errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Post verilerini al
    $tc_no = $_POST['tc_no'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if (!empty($tc_no) && !empty($sifre)) {
        // Kullanıcıyı sorgula
        $sql_check = "SELECT * FROM akademisyen WHERE tc_no = ?";
        $params_check = array($tc_no);
        $stmt_check = sqlsrv_query($baglan, $sql_check, $params_check);

        if ($stmt_check === false) {
            die("Sorgu sırasında hata oluştu: " . print_r(sqlsrv_errors(), true));
        }

        // Sonuç alın
        $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
        if ($row && $sifre === $row['sifre']) {
            // Giriş başarılı, oturum verilerini ayarla
            $_SESSION['tc_no'] = $tc_no;
            $_SESSION['Ad'] = $row['Ad'];
            $_SESSION['Soyad'] = $row['Soyad'];
            $_SESSION['rol'] = $row['rol'];

            // Rol kontrolü ve yönlendirme
            if ($row['rol'] == 'Yönetici') {
                // Yönetici sayfasına yönlendir
                header("Location: ogrenci_listesi.php");
                exit;  // Yönlendirmeden önce exit kullanın
            } elseif ($row['rol'] == 'Akademisyen') {
                // Akademisyen sayfasına yönlendir
                header("Location: akademisyenpanel.php");
                exit;  // Yönlendirmeden önce exit kullanın
            } else {
                // Bilinmeyen rol durumu
                $errorMessage = "Bilinmeyen bir rol ile giriş yapmaya çalıştınız.". htmlspecialchars($row['rol']);
            }
        } else {
            $errorMessage = "TC numaranız veya şifreniz yanlış.";
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
        <h2>Akademisyen Girişi</h2>
        
        <!-- Hata mesajı -->
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="card p-5">
            <form method="POST" action="" autocomplete="off">
               <div class="mb-3">
                    <label for="tc_no" class="form-label">TC Kimlik Numaranız</label>
                    <input type="text" class="form-control" placeholder="TC Numaranızı Giriniz." id="tc_no" name="tc_no" value="<?php echo htmlspecialchars($tc_no); ?>" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="sifre" class="form-label" >Şifreniz</label>
                    <input type="password" class="form-control" placeholder="Şifrenizi Giriniz." id="sifre" name="sifre" value="" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Giriş</button>
                <br><br>
                <div class="link">
                    <a href="ogrencigiris.php" style="text-decoration: none;">Öğrenci Girişi</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>