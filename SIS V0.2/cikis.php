<?php
session_start();

// Tüm oturum verilerini temizle
session_unset();

// Oturumu tamamen yok et
session_destroy();

// Giriş sayfasına yönlendir
header("Location: ogrencigiris.php");
exit;
