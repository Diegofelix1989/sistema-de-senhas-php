<?php
include_once __DIR__ . '/../includes/conexao.php';

if (!isset($_GET['local_id'])) {
    echo json_encode([]);
    exit;
}

$local_id = intval($_GET['local_id']);

$sql = "
SELECT s.numero, g.nome AS guiche, s.chamada_em
FROM senhas s
JOIN filas f ON s.fila_id = f.id
JOIN guiches g ON s.guiche_id = g.id
WHERE f.local_id = :local_id
  AND s.status = 'em_atendimento'
ORDER BY s.chamada_em DESC
LIMIT 6
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['local_id' => $local_id]);
$senhas = $stmt->fetchAll();

echo json_encode($senhas);
