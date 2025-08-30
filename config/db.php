<?php

$host   = '127.0.0.1';
$dbName = 'agenda';
$dbUser = 'root';
$dbPass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Erros como exceções
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorno padrão como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepares nativos do MySQL
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('Falha na conexão: ' . $e->getMessage());
}
