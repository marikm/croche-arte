<?php
    $host = "localhost";
    $dbname = "ecommerce_croche";
    $username = "root";
    $pass = "";

    define('BASE_URL', 'http://localhost/croche-arte');
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }