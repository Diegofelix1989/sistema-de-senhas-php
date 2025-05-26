<?php
require_once __DIR__ . '/../includes/conexao.php';

$local_id = isset($_GET['local_id']) ? (int)$_GET['local_id'] : null;

// Lista de locais para o seletor
$locais = $pdo->query("SELECT id, nome FROM locais ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar filas com totais de senhas por status
$filas = $pdo->query(
    "SELECT f.id, f.nome, f.tipo, f.prefixo, COUNT(CASE WHEN s.status = 'aguardando' THEN 1 END) AS aguardando, COUNT(CASE WHEN s.status = 'em_atendimento' THEN 1 END) AS em_atendimento
     FROM filas f
     LEFT JOIN senhas s ON s.fila_id = f.id
     " . ($local_id ? "WHERE f.local_id = $local_id" : "") .
    " GROUP BY f.id, f.nome, f.tipo, f.prefixo
     ORDER BY f.tipo DESC, f.nome ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Últimas senhas atendidas por fila
$ultimasPorFila = [];
foreach ($filas as $fila) {
    $ultimasPorFila[$fila['id']] = $pdo->query(
        "SELECT s.id, s.numero, s.atendimento_finalizado_em, g.nome as guiche_nome
         FROM senhas s
         LEFT JOIN guiches g ON s.guiche_id = g.id
         WHERE s.fila_id = {$fila['id']} AND s.status = 'atendida'"
        . " ORDER BY s.atendimento_finalizado_em DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode([
    'locais' => $locais,
    'filas' => $filas,
    'ultimasPorFila' => $ultimasPorFila,
]); 