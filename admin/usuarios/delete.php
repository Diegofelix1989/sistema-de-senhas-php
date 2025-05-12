<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$id = $_GET['id'];
$pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit();
?>
