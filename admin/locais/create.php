<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $stmt = $pdo->prepare("INSERT INTO locais (nome) VALUES (?)");
    $stmt->execute([$nome]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Novo Local</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Novo Local</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" required></div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>

