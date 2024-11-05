<?php
session_start();

include 'baglan.php';

$successMessage = "";
$errorMessage = "";

$ogr_no = $_SESSION['ogr_no'] ?? null;

$sql = "SELECT ad, soyad, eposta, telefon FROM ogrenciler WHERE ogr_no = ?";     
$params = array($ogr_no);
$stmt = sqlsrv_query($baglan, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$ad = $row['ad'];
$soyad = $row['soyad'];
$eposta = $row['eposta'];
$telefon = $row['telefon'];

// Dosya yükleme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // eposta ve telefon güncellemesi
    if (isset($_POST['eposta']) && isset($_POST['telefon'])) {
        $eposta = $_POST['eposta'];
        $telefon = $_POST['telefon'];
        
        $query = "UPDATE ogrenciler SET eposta = ?, telefon = ? WHERE ogr_no = ?";
        $params = array($eposta, $telefon, $ogr_no);
        $stmt = sqlsrv_query($baglan, $query, $params);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $successMessage = "Bilgiler başarıyla güncellendi!";
    }

    // Profil fotoğrafı yükleme
    if (isset($_FILES['profil_foto'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES['profil_foto']['name']);

        // Dizin var mı kontrol et
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Dizin yoksa oluştur
        }

        // Dosyayı taşımaya çalış
        if (move_uploaded_file($_FILES['profil_foto']['tmp_name'], $targetFile)) {
            // Burada profil fotoğrafını veritabanına kaydedebilirsiniz
            // Örneğin, "profil_resmi" adında bir sütun varsa:
            $updateQuery = "UPDATE ogrenciler SET profil_foto = ? WHERE ogr_no = ?";
            $params = array($targetFile, $ogr_no);
            $stmt = sqlsrv_query($baglan, $updateQuery, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            $successMessage = "Profil fotoğrafı başarıyla yüklendi!";
        } else {
            $errorMessage = "Dosya yükleme hatası.";
        }
    }
    $sql = "SELECT profil_foto FROM ogrenciler WHERE ogr_no = ?";
$params = array($ogr_no);
$stmt = sqlsrv_query($baglan, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$profilFoto = $row['profil_foto'];
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
</head>
<body>

<section class="vh-100" style="background-color: #f4f5f7;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-md-4 gradient-custom text-center text-white"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="<?php echo htmlspecialchars($profil_foto); ?>" alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
                
              <h5><?php echo htmlspecialchars($ad) . ' ' . htmlspecialchars($soyad); ?></h5>
              <p>Öğrenci No: <?php echo htmlspecialchars($ogr_no); ?></p>
            </div>
            <div class="col-md-8">
              <div class="card-body p-4">
                <h6>Bilgiler</h6>
                <hr class="mt-0 mb-4">
                
                <!-- Öğrenci Bilgilerini Göster -->
                <p><strong>Öğrenci No:</strong> <?php echo htmlspecialchars($ogr_no); ?></p>
                <p><strong>Ad:</strong> <?php echo htmlspecialchars($ad); ?></p>
                <p><strong>Soyad:</strong> <?php echo htmlspecialchars($soyad); ?></p>
                <p><strong>eposta:</strong> <?php echo htmlspecialchars($eposta); ?></p>
                <p><strong>Telefon:</strong> <?php echo htmlspecialchars($telefon); ?></p>
                
                <!-- Güncelleme Formu -->
                <form method="POST" action="" enctype="multipart/form-data">
                  <div class="row pt-1">
                    <div class="col-6 mb-3">
                      <label for="eposta" class="form-label"><h6>eposta</h6></label>
                      <input type="eposta" name="eposta" id="eposta" class="form-control" value="<?php echo htmlspecialchars($eposta); ?>">
                    </div>
                    <div class="col-6 mb-3">
                      <label for="telefon" class="form-label"><h6>Telefon</h6></label>
                      <input type="text" name="telefon" id="telefon" class="form-control" value="<?php echo htmlspecialchars($telefon); ?>">
                    </div>
                    <div class="col-12 mb-3">
                      <label for="profil_foto" class="form-label"><h6>Profil Fotoğrafı</h6></label>
                      <input type="file" name="profil_foto" id="profil_foto" class="form-control">
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Bilgileri Güncelle</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
</head>
<body>

<section class="vh-100" style="background-color: #f4f5f7;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-lg-6 mb-4 mb-lg-0">
        <div class="card mb-3" style="border-radius: .5rem;">
          <div class="row g-0">
            <div class="col-md-4 gradient-custom text-center text-white"
              style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
              <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp"
                alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
              <h5><?php echo htmlspecialchars($ad) . ' ' . htmlspecialchars($soyad); ?></h5>
              <p>Öğrenci No: <?php echo htmlspecialchars($ogr_no); ?></p>
            </div>
            <div class="col-md-8">
              <div class="card-body p-4">
                <h6>Bilgiler</h6>
                <hr class="mt-0 mb-4">
                
                <!-- Öğrenci Bilgilerini Göster -->
                <p><strong>Öğrenci No:</strong> <?php echo htmlspecialchars($ogr_no); ?></p>
                <p><strong>Ad:</strong> <?php echo htmlspecialchars($ad); ?></p>
                <p><strong>Soyad:</strong> <?php echo htmlspecialchars($soyad); ?></p>
                <p><strong>eposta:</strong> <?php echo htmlspecialchars($eposta); ?></p>
                <p><strong>Telefon:</strong> <?php echo htmlspecialchars($telefon); ?></p>
                
                <!-- Güncelleme Formu -->
                <form method="POST" action="" enctype="multipart/form-data">
                  <div class="row pt-1">
                    <div class="col-6 mb-3">
                      <label for="eposta" class="form-label"><h6>Eposta</h6></label>
                      <input type="eposta" name="eposta" id="eposta" class="form-control" value="<?php echo htmlspecialchars($eposta); ?>">
                    </div>
                    <div class="col-6 mb-3">
                      <label for="telefon" class="form-label"><h6>Telefon</h6></label>
                      <input type="text" name="telefon" id="telefon" class="form-control" value="<?php echo htmlspecialchars($telefon); ?>">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="profil_foto" class="form-label"><h6>Profil Fotoğrafı</h6></label>
                    <input type="file" name="profil_foto" id="profil_foto" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary">Bilgileri Güncelle</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>