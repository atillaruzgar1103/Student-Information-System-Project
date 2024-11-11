<?php

session_start();
if (!isset($_SESSION['tc_no'])) {
  header("Location: akademisyengiris.php");
  exit;
}

include 'baglan.php';

$successMessage = "";
$errorMessage = "";

$tc_no = $_SESSION['tc_no'] ?? null;

$sql = "SELECT Ad, Soyad, rol FROM akademisyen WHERE tc_no = ?";     
$params = array($tc_no);
$stmt = sqlsrv_query($baglan, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$ad = $row['Ad'];
$soyad = $row['Soyad'];
$rol = $row['rol'];


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset=" UTF-8 ">
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
              <h5><?php echo htmlspecialchars($ad) . ' ' . htmlspecialchars($soyad); ?></h5>
              <p>Tc No: <?php echo htmlspecialchars($tc_no); ?></p>
            </div>
            <div class="col-md-8">
              <div class="card-body p-4">
                <h6>Bilgiler</h6>
                <hr class="mt-0 mb-4">
                
                <!-- akademisyen Bilgilerini Göster -->
                <p><strong>Tc No:</strong> <?php echo htmlspecialchars($tc_no); ?></p>
                <p><strong>Ad:</strong> <?php echo htmlspecialchars($ad); ?></p>
                <p><strong>Soyad:</strong> <?php echo htmlspecialchars($soyad); ?></p>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars($rol); ?></p>
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