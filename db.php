<?php
$host = '127.0.0.1';
$user = 'area_administrativa';
$password = ''; // Substitua pela sua senha do MySQL
$dbname = 'areaadmin';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>