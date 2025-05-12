<?php
$host = 'localhost';
$db   = 'gerenciador_filas';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Conexão realizada com sucesso!";
} catch (PDOException $e) {
     echo "Erro na conexão com o banco de dados: " . $e->getMessage();
     exit;
}
?>

