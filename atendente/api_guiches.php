<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se foi enviado o parâmetro local_id
if (!isset($_GET['local_id'])) {
    echo json_encode([]);
    exit;
}

$local_id = intval($_GET['local_id']);

// Buscar os guichês do local especificado que estão ativos e disponíveis
$sql = "SELECT id, nome FROM guiches 
        WHERE local_id = :local_id 
        AND status_ativo = 'ativo' 
        AND status_uso = 'disponivel' 
        ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute(['local_id' => $local_id]);
$guiches = $stmt->fetchAll();

echo json_encode($guiches);