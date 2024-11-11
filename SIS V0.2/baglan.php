<?php
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
    die(print_r(sqlsrv_errors(), true));
}

?>