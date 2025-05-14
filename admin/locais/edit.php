<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM locais WHERE id = ?");
$stmt->execute([$id]);
$local = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $stmt = $pdo->prepare("UPDATE locais SET nome = ? WHERE id = ?");
    $stmt->execute([$nome, $id]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Editar Local</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Editar Local</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?= $local['nome'] ?>" required></div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>
