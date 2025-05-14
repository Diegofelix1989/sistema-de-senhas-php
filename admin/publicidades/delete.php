<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';


$id = $_GET['id'];

// Busca informações da publicidade para verificar se há arquivo para excluir
$stmt = $pdo->prepare("SELECT * FROM publicidades WHERE id = ?");
$stmt->execute([$id]);
$publicidade = $stmt->fetch();

// Se for imagem ou vídeo, exclui o arquivo físico
if ($publicidade && ($publicidade['tipo_midia'] == 'imagem' || $publicidade['tipo_midia'] == 'video')) {
    if (file_exists($publicidade['media_path'])) {
        unlink($publicidade['media_path']);
    }
}

// Exclui o registro do banco de dados
$pdo->prepare("DELETE FROM publicidades WHERE id = ?")->execute([$id]);

header("Location: index.php");
exit();
?>