<?php
// Certifique-se de que erros não sejam exibidos na resposta
ini_set('display_errors', 0);
error_reporting(0);

// Defina o header para JSON
header('Content-Type: application/json');

include_once __DIR__ . '/../includes/conexao.php';

try {
    // Verificar se foi enviado o parâmetro local_id
    if (!isset($_GET['local_id'])) {
        echo json_encode([]);
        exit;
    }

    $local_id = intval($_GET['local_id']);

    // Buscar as filas do local especificado
    $sql = "SELECT id, nome, sigla FROM filas WHERE local_id = :local_id ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['local_id' => $local_id]);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($filas);
} catch (Exception $e) {
    // Em caso de erro, retorne um objeto JSON com mensagem de erro
    echo json_encode(['error' => 'Erro ao carregar filas']);
}
