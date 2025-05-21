<?php
include_once __DIR__ . '/../includes/conexao.php';
session_start();

header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Receber dados do POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['guiche_id']) || !isset($data['status'])) {
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$guicheId = $data['guiche_id'];
$status = $data['status'];

// Validar o status
if (!in_array($status, ['disponivel', 'em_uso'])) {
    echo json_encode(['error' => 'Status inválido']);
    exit;
}

try {
    // Atualizar status do guichê
    $stmt = $pdo->prepare("UPDATE guiches SET status_uso = :status WHERE id = :id");
    $stmt->execute([
        ':id' => $guicheId,
        ':status' => $status
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status do guichê atualizado com sucesso']);
    } else {
        echo json_encode(['error' => 'Guichê não encontrado ou nenhuma alteração feita']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao atualizar status do guichê: ' . $e->getMessage()]);
}
?>