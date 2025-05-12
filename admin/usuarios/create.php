<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha, $tipo]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Novo Usuário</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Novo Usuário</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label>Senha</label><input type="password" name="senha" class="form-control" required></div>
    <div class="mb-3"><label>Tipo</label>
        <select name="tipo" class="form-select">
            <option value="admin">Admin</option>
            <option value="atendente">Atendente</option>
        </select>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>
