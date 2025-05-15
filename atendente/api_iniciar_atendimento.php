<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se foi enviado o parâmetro da senha_id
if (!isset($_GET['senha_id'])) {
    echo json_encode(['error' => 'ID da senha não fornecido']);
    exit;
}

$senha_id = intval($_GET['senha_id']);

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Verificar se a senha existe e está em atendimento
    $sql_check = "
        SELECT 
            s.id, 
            s.numero, 
            s.status,
            s.atendimento_iniciado_em
        FROM 
            senhas s
        WHERE 
            s.id = ?
    ";
    
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$senha_id]);
    $senha = $stmt_check->fetch();
    
    if (!$senha) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Senha não encontrada']);
        exit;
    }
    
    if ($senha['status'] !== 'em_atendimento') {
        $pdo->rollBack();
        echo json_encode(['error' => 'Esta senha não está em estado de atendimento']);
        exit;
    }
    
    if ($senha['atendimento_iniciado_em'] !== null) {
        $pdo->rollBack();
        echo json_encode(['error' => 'O atendimento desta senha já foi iniciado']);
        exit;
    }
    
    // Registrar o início do atendimento
    $timestamp = date('Y-m-d H:i:s');
    $sql_update = "
        UPDATE senhas
        SET 
            atendimento_iniciado_em = ?
        WHERE 
            id = ?
    ";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        $timestamp,
        $senha_id
    ]);
    
    // Confirmar transação
    $pdo->commit();
    
    // Retornar resultado
    echo json_encode([
        'success' => true,
        'senha_id' => $senha_id,
        'atendimento_iniciado_em' => $timestamp
    ]);
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $pdo->rollBack();
    echo json_encode(['error' => 'Erro ao iniciar atendimento: ' . $e->getMessage()]);
}