<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se foram enviados os parâmetros necessários
if (!isset($_GET['local_id']) || !isset($_GET['guiche_id']) || !isset($_GET['filas'])) {
    echo json_encode(['error' => 'Parâmetros incompletos']);
    exit;
}

$local_id = intval($_GET['local_id']);
$guiche_id = intval($_GET['guiche_id']);
$filas_ids = explode(',', $_GET['filas']);

// Verificar se há IDs válidos
if (empty($filas_ids)) {
    echo json_encode(['error' => 'Nenhuma fila selecionada']);
    exit;
}

try {
    // Iniciar transação
    $pdo->beginTransaction();
    
    // Preparar placeholders para os IDs das filas
    $placeholders = implode(',', array_fill(0, count($filas_ids), '?'));
    
    // Buscar a próxima senha aguardando
    $sql = "
        SELECT 
            s.id, 
            s.numero, 
            s.criado_em, 
            f.id AS fila_id, 
            f.nome AS fila_nome
        FROM 
            senhas s
        JOIN 
            filas f ON s.fila_id = f.id
        WHERE 
            f.local_id = ? 
            AND s.fila_id IN ($placeholders)
            AND s.status = 'aguardando'
        ORDER BY 
            s.criado_em ASC
        LIMIT 1
    ";
    //f.sigla AS fila_sigla
    // Preparar os parâmetros para a consulta
    $params = array_merge([$local_id], $filas_ids);
    
    // Executar a consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $senha = $stmt->fetch();
    
    if (!$senha) {
        // Não há senhas aguardando
        $pdo->commit();
        echo json_encode(['error' => 'Não há senhas aguardando nas filas selecionadas']);
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
        $senha['id']
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
    echo json_encode(['error' => 'Erro ao chamar próxima senha: ' . $e->getMessage()]);
}
