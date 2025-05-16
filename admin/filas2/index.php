<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';
include_once 'filas_controller.php';

// Buscar todos os locais para os dropdowns dos modais
$stmtLocais = $pdo->query("SELECT id, nome FROM locais ORDER BY nome");
$locais = $stmtLocais->fetchAll();

// Buscar todas as filas com informações de local
$stmt = $pdo->query("SELECT f.id, f.nome, f.tipo, f.local_id, l.nome AS local 
                     FROM filas f 
                     JOIN locais l ON f.local_id = l.id
                     ORDER BY f.nome");
$filas = $stmt->fetchAll();

// Incluir o arquivo do template HTML
include_once 'views/template.php';
?>