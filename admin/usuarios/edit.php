<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];
    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $tipo, $id]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Editar Usuário</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Editar Usuário</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?= $usuario['nome'] ?>" required></div>
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $usuario['email'] ?>" required></div>
    <div class="mb-3"><label>Tipo</label>
        <select name="tipo" class="form-select">
            <option value="admin" <?= $usuario['tipo'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="atendente" <?= $usuario['tipo'] == 'atendente' ? 'selected' : '' ?>>Atendente</option>
        </select>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>
