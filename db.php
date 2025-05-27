<?php
$host = 'localhost';             
$db   = 'registration_db';       
$user = 'postgres';              
$pass = 'Password123!';              
$dsn  = "pgsql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
