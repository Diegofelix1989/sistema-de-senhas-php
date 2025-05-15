<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se foram enviados os parâmetros necessários
if (!isset($_GET['local_id']) || !isset($_GET['filas'])) {
    echo json_encode([]);
    exit;
}

$local_id = intval($_GET['local_id']);
$filas_ids = explode(',', $_GET['filas']);

// Verificar se há IDs válidos
if (empty($filas_ids)) {
    echo json_encode([]);
    exit;
}

// Preparar placeholders para os IDs das filas
$placeholders = implode(',', array_fill(0, count($filas_ids), '?'));

// Buscar as próximas senhas aguardando por fila
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
";

// Preparar os parâmetros para a consulta
$params = array_merge([$local_id], $filas_ids);

// Executar a consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$senhas = $stmt->fetchAll();

echo json_encode($senhas);
