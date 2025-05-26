<?php
include_once __DIR__ . '/../includes/conexao.php';

if (!isset($_GET['local_id'])) {
    echo json_encode([]);
    exit;
}

$local_id = intval($_GET['local_id']);

$sql = "
SELECT p.titulo, p.tipo_midia, p.duracao, p.media_path
FROM publicidades p
JOIN telas t ON p.id_tela = t.id
WHERE t.local_id = :local_id
ORDER BY p.data_criacao DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['local_id' => $local_id]);
$publicidades = $stmt->fetchAll();

// Ajustar o caminho da mídia para ser acessível via URL
foreach ($publicidades as &$p) {
    // Remover "../" e garantir que o caminho seja acessível pela URL do servidor
    $p['media_path'] = str_replace('../', '', $p['media_path']);
    // Adiciona o domínio completo ao caminho para formar a URL completa
    $p['media_path'] = 'http://192.168.0.108/sistema-de-senhas-php002/admin/' . $p['media_path'];
}

echo json_encode($publicidades);
?>
