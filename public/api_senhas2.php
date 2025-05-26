<?php
include_once __DIR__ . '/../includes/conexao.php';

if (!isset($_GET['local_id'])) {
    echo json_encode([]);
    exit;
}

$local_id = intval($_GET['local_id']);

$sql = "
SELECT s.numero, g.nome AS guiche, s.status, s.chamada_em
FROM senhas s
JOIN filas f ON s.fila_id = f.id
JOIN guiches g ON s.guiche_id = g.id
WHERE f.local_id = :local_id
  AND s.chamada_em IS NOT NULL -- Apenas senhas que já foram chamadas
ORDER BY s.chamada_em DESC
LIMIT 6 -- Limita para as 6 últimas senhas chamadas
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['local_id' => $local_id]);
$senhas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Usar FETCH_ASSOC para facilitar

// Retorna as senhas para o frontend.
// O frontend irá filtrar qual é a 'em atendimento' para destaque e quais são para o histórico.
echo json_encode($senhas);

?>