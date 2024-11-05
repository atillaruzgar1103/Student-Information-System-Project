<?php
session_start();

include 'baglan.php';

if (!isset($_SESSION['ogr_no'])) {
    header("Location: ogrencigiris.php");
    exit;
}

// Ad ve soyadı oturumdan al
$ad = isset($_SESSION['ad']) ? $_SESSION['ad'] : 'Ad tanımlı değil'; 
$soyad = isset($_SESSION['soyad']) ? $_SESSION['soyad'] : 'Soyad tanımlı değil';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Paneli</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <style>
       body {
            margin: 0;
            height: 100vh;
            background-image: url("bg.svg");
            background-size: cover; 
            background-position: center; 
        }
        
        .hamburger {
            cursor: pointer;
            font-size: 30px;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        /* Menü kenar barı */
        .menu {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 999;
            top: 0;
            left: 0;
            background-color: #1b1c1c;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .menu a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .menu a:hover {
            color: #f1f1f1;
        }

        .menu .close-btn {
            position: absolute;
            top: 5px;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .menu .header {
            color: white;
            font-size: 24px;
            margin-left: 32px;
            margin-bottom: 30px;
        }

        
        .profilim-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .profilim-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            font-size: 30px;
            color: #a4a6a4;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>
<body>

    
    <span class="hamburger" onclick="openMenu()">&#9776;</span>

    
    <div class="profilim-icon" onclick="window.location.href='profilim.php'">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
        </svg>
    </div>
    
    <div id="menu" class="menu">
        <a href="javascript:void(0)" class="close-btn" onclick="closeMenu()">&times;</a>
        <div class="h4" style="color: white;">Merhaba, <?php echo htmlspecialchars($ad . ' ' . $soyad); ?></div>
        <a href="#">Transcript</a>
        <a href="#">Notlar</a>
        <a href="cikis.php">Çıkış Yap</a>
    </div>    

    <script>
        
        function openMenu() {
            document.getElementById("menu").style.width = "250px";
        }

       
        function closeMenu() {
            document.getElementById("menu").style.width = "0";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>