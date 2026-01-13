<?php
// MUST be the VERY FIRST THING (no spaces above)

session_start();

$host = "localhost";
$db   = "hbm_bank";
$user = "root";
$pass = "12345678";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed");
}
