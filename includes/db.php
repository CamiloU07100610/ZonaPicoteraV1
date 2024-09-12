<?php
function conectarDB() {
    $serverName = "tcp:dtb.database.windows.net,1433";
    $connectionOptions = array(
        "Database" => "multimedia_portal",
        "Uid" => "CloudSAcff10363",
        "PWD" => "342461323_Yeiker_0710",
        "LoginTimeout" => 30,
        "Encrypt" => 1,
        "TrustServerCertificate" => 0,
        "CharacterSet" => "UTF-8" // Enforce UTF-8 encoding
    );
    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=multimedia_portal", $connectionOptions['Uid'], $connectionOptions['PWD'], [
            PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
        ]);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        return $conn;
    } catch (PDOException $e) {
        die("Error connecting to SQL Server: " . $e->getMessage());
    }
}