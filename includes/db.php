<?php
function conectarDB() {
    $serverName = "tcp:dtb.database.windows.net,1433";
    $connectionOptions = array(
        "Database" => "multimedia_portal",
        "Uid" => "CloudSAcff10363",
        "PWD" => "342461323_Yeiker_0710",
        "LoginTimeout" => 30,
        "Encrypt" => 1,
        "TrustServerCertificate" => 0
    );

    // Try to establish a connection using PDO
    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=multimedia_portal", $connectionOptions['Uid'], $connectionOptions['PWD']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Error connecting to SQL Server: " . $e->getMessage());
    }
}

