<?php
session_start();
if (isset($_SESSION['guiche_id'])) {
    include_once __DIR__ . '/includes/conexao.php';
    $guicheId = $_SESSION['guiche_id'];
    $stmt = $pdo->prepare("UPDATE guiches SET status_uso = 'disponivel' WHERE id = ?");
    $stmt->execute([$guicheId]);
}
session_destroy();
header("Location: index.php");
exit();
?>
