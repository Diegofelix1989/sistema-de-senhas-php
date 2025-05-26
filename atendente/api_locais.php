<?php
include_once __DIR__ . '/../includes/conexao.php';

$stmt = $pdo->query("SELECT id, nome FROM locais ORDER BY nome");
$locais = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($locais); 