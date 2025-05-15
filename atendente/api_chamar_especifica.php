<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se foram enviados os parâmetros necessários
if (!isset($_GET['senha_id']) || !isset($_GET['guiche_id'])) {
    echo json_encode(['error' => 'Parâmetros incompletos']);
    exit;
}

$senha_id = intval($_GET['senha_id']);
$guiche_id = intval($_GET['guiche_id']);

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Verificar se a senha existe e está aguardando
    $sql_check = "
        SELECT 
            s.id, 
            s.numero, 
            s.criado_em, 
            s.status,
            f.id AS fila_id, 
            f.nome AS fila_nome
        FROM 
            senhas s
        JOIN 
            filas f ON s.fila_id = f.id
        WHERE 
            s.id = ?
    ";
    //f.sigla AS fila_sigla
    
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$senha_id]);
    $senha = $stmt_check->fetch();
    
    if (!$senha) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Senha não encontrada']);
        exit;
    }
    
    if ($senha['status'] !== 'aguardando') {
        $pdo->rollBack();
        echo json_encode(['error' => 'Esta senha não está aguardando atendimento']);
        exit;
    }
    
    // Atualizar o status da senha para 'em_atendimento'
    $timestamp = date('Y-m-d H:i:s');
    $sql_update = "
        UPDATE senhas
        SET 
            status = 'em_atendimento',
            chamada_por = ?, -- ID do usuário poderia ser enviado no futuro
            chamada_em = ?,
            guiche_id = ?
        WHERE 
            id = ?
    ";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        1, // ID do usuário (fixo por enquanto)
        $timestamp,
        $guiche_id,
        $senha_id
    ]);
    
    // Adicionar o timestamp de chamada ao objeto de retorno
    $senha['chamada_em'] = $timestamp;
    
    // Confirmar transação
    $pdo->commit();
    
    // Retornar senha chamada
    echo json_encode($senha);
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $pdo->rollBack();
    echo json_encode(['error' => 'Erro ao chamar senha específica: ' . $e->getMessage()]);
}
