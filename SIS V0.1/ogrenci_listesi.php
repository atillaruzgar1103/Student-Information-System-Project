<?php
session_start(); // Session başlat

// Veritabanı bağlantısı
$serverName = "ATILLAHP\\SQLEXPRESS"; 
$connectionOptions = array(
    "Database" => "ogrencikayit", 
    "Uid" => "", 
    "PWD" => "" 
);

$baglan = sqlsrv_connect($serverName, $connectionOptions);
if ($baglan === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $ogr_no = $_GET['delete'];

    // İşlem başlangıcı
    sqlsrv_begin_transaction($baglan);

    try {
        // Önce notlar tablosundan öğrencinin notlarını sil
        $deleteNotlarSql = "DELETE FROM notlar WHERE ogr_no = ?";
        $deleteNotlarParams = array($ogr_no);
        $deleteNotlarStmt = sqlsrv_query($baglan, $deleteNotlarSql, $deleteNotlarParams);

        if ($deleteNotlarStmt === false) {
            throw new Exception("Notlar silinirken bir hata oluştu.");
        }

        // Ardından öğrenci tablosundan öğrenciyi sil
        $deleteSql = "DELETE FROM ogrenciler WHERE ogr_no = ?";
        $deleteParams = array($ogr_no);
        $deleteStmt = sqlsrv_query($baglan, $deleteSql, $deleteParams);

        if ($deleteStmt === false) {
            throw new Exception("Öğrenci silinirken bir hata oluştu.");
        }

        // İşlemi onayla
        sqlsrv_commit($baglan);
        $_SESSION['message'] = 'Öğrenci başarıyla silindi.'; // Mesajı ayarla
    } catch (Exception $e) {
        // Hata durumunda işlemi geri al
        sqlsrv_rollback($baglan);
        $_SESSION['error'] = 'Hata: ' . $e->getMessage(); // Hata mesajını ayarla
    }

    // Yeniden yönlendirme
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Öğrenci verilerini çekme sorgusu
$sql = "SELECT ogr_no, tc_no, ad, soyad, bolum, sinif FROM ogrenciler";
$stmt = sqlsrv_query($baglan, $sql);

// Notları alma işlemi
$notlar = [];
$ogr_no = isset($_GET['ogr_no']) ? $_GET['ogr_no'] : ''; // Öğrenci numarasını al

if (!empty($ogr_no)) {
    // Notları sorgulama
    $sqlNotlar = "SELECT * FROM notlar WHERE ogr_no = ?";
    $paramsNotlar = array($ogr_no);
    $stmtNotlar = sqlsrv_query($baglan, $sqlNotlar, $paramsNotlar);

    if ($stmtNotlar !== false) {
        while ($row = sqlsrv_fetch_array($stmtNotlar, SQLSRV_FETCH_ASSOC)) {
            $notlar[] = $row; // Notları diziye ekle
        }
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}

// Not güncelleme işlemi
if (isset($_POST['update'])) {
    $vize = $_POST['vize'];
    $final = $_POST['final'];
    $butunleme = $_POST['butunleme'];
    $dersadi = $_POST['dersadi'];
    $ogr_no_to_update = $_POST['ogr_no_to_update'];

    $updateSql = "UPDATE notlar SET vize = ?, final = ?, butunleme = ?, dersadi = ? WHERE ogr_no = ?";
    $updateParams = array($vize, $final, $butunleme, $dersadi, $ogr_no_to_update);
    $updateStmt = sqlsrv_query($baglan, $updateSql, $updateParams);

    if ($updateStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<div class='alert alert-success' role='alert'>Notlar başarıyla güncellendi.</div>";
        // Güncellenmiş notları tekrar sorgulama
        $stmtNotlar = sqlsrv_query($baglan, $sqlNotlar, $paramsNotlar);
        $notlar = []; // Notları sıfırla
        while ($row = sqlsrv_fetch_array($stmtNotlar, SQLSRV_FETCH_ASSOC)) {
            $notlar[] = $row;
        }
    }
}

// Not silme işlemi
if (isset($_POST['delete_not'])) {
    $ogr_no_to_delete = $_POST['ogr_no_to_delete'];
    $deleteNotSql = "DELETE FROM notlar WHERE ogr_no = ?";
    $deleteNotParams = array($ogr_no_to_delete);
    $deleteNotStmt = sqlsrv_query($baglan, $deleteNotSql, $deleteNotParams);

    if ($deleteNotStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<div class='alert alert-success' role='alert'>Notlar başarıyla silindi.</div>";
        $notlar = []; // Silindikten sonra notları sıfırla
    }
}
if (!isset($_SESSION['tc_no'])) {
    header("Location: akademisyengiris.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Öğrenci Listesi</title>
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

        .equal-btn {
            width: 90px; 
        }
        
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Öğrenci Listesi</h2>
        
        <!-- Başarı veya hata mesajını göster -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Mesajı gösterdikten sonra temizle
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Hata mesajını gösterdikten sonra temizle
                ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">*</th>
                    <th scope="col">TC Numarası</th>
                    <th scope="col">Öğrenci Numarası</th>
                    <th scope="col">Ad</th>
                    <th scope="col">Soyad</th>
                    <th scope="col">Bölüm</th>
                    <th scope="col">Sınıf</th>
                    <th scope="col">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt !== false) {
                    $index = 1; 
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<th scope='row'>" . $index++ . "</th>";
                        echo "<td>" . htmlspecialchars($row['tc_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ogr_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ad']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['soyad']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bolum']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sinif']) . "</td>";
                        echo "<td>
                                <a href='ogrencikayit.php?ogr_no=" . htmlspecialchars($row['ogr_no']) . "' class='btn btn-success btn-sm equal-btn'>Güncelle</a>
                                <a href='?delete=" . htmlspecialchars($row['ogr_no']) . "' class='btn btn-danger btn-sm equal-btn' onclick='return confirm(\"Silmek istediğinize emin misiniz?\");'>Sil</a>
                                <a href='notlar.php?ogr_no=" . htmlspecialchars($row['ogr_no']) . "' class='btn btn-primary btn-sm equal-btn'>Not Gir</a>
                                <a href='?ogr_no=" . htmlspecialchars($row['ogr_no']) . "' class='btn btn-secondary btn-sm equal-btn'>Notlar</a>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <?php if (!empty($ogr_no)): ?>
            <h3><?php echo htmlspecialchars($ogr_no); ?> Öğrenci Notları</h3>
            <?php if (!empty($notlar)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Vize</th>
                            <th scope="col">Final</th>
                            <th scope="col">Bütünleme</th>
                            <th scope="col">Ders Adı</th>
                            <th scope="col">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notlar as $not): ?>
                            <tr>
                                <form method="POST" action="">
                                    <input type="hidden" name="ogr_no_to_update" value="<?php echo htmlspecialchars($ogr_no); ?>">
                                    <td>
                                        <input type="number" name="vize" value="<?php echo htmlspecialchars($not['vize']); ?>" required style="margin-top: 13px;">
                                    </td>
                                    <td>
                                        <input type="number" name="final" value="<?php echo htmlspecialchars($not['final']); ?>" required style="margin-top: 13px;">
                                    </td>
                                    <td>
                                        <input type="number" name="butunleme" value="<?php echo htmlspecialchars($not['butunleme']); ?>" required style="margin-top: 13px;">
                                    </td>
                                    <td style="display: flex; flex-direction: column; align-items: 10px;">
                                        <label for="dersadi" class="form-label" ></label>
                                    <select class="form-select transparent-input" id="dersadi" name="dersadi" required style="width: 200px; text-align: center;">
                                        <option value="" disabled <?php echo empty($not['dersadi']) ? "selected" : ""; ?>>Seçiniz</option>
                                        <option value="Matematik" <?php echo ($not['dersadi'] === 'Matematik') ? "selected" : ""; ?>>Matematik</option>
                                        <option value="Nesne Tabanlı Programlama" <?php echo ($not['dersadi'] === 'Nesne Tabanlı Programlama') ? "selected" : ""; ?>>Nesne Tabanlı Programlama</option>
                                        <option value="Veritabanı" <?php echo ($not['dersadi'] === 'Veritabanı') ? "selected" : ""; ?>>Veritabanı Programlama</option>
                                        <option value="Görsel Programlama" <?php echo ($not['dersadi'] === 'Görsel Programlama') ? "selected" : ""; ?>>Görsel Programlama</option>
                                        <option value="Fizik" <?php echo ($not['dersadi'] === 'Fizik') ? "selected" : ""; ?>>Fizik</option>
                                    </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="update" class="btn btn-success btn-sm equal-btn" style="margin-top: 13px;" >Güncelle</button>
                                        <button type="submit" name="delete_not" class="btn btn-danger btn-sm equal-btn" onclick='return confirm("Silmek istediğinize emin misiniz?");' style="margin-top: 13px;">Sil</button>
                                        <input type="hidden" name="ogr_no_to_delete" value="<?php echo htmlspecialchars($not['ogr_no']); ?>" style="margin-top: 13px;">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">Bu öğrenciye ait not bulunmamaktadır.</div> <!-- Not yoksa mesaj -->
            <?php endif; ?>
        <?php endif; ?>

        <a href="ogrencikayit.php" class="btn btn-primary">Yeni Öğrenci Kaydet</a>
        <br>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
sqlsrv_close($baglan);
?>