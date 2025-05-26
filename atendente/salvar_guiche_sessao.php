<?php
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['guiche_id'])) {
    $_SESSION['guiche_id'] = $data['guiche_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'guiche_id não informado']);
} 