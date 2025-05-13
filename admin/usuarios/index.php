<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Usuários <a href="create.php" class="btn btn-success btn-sm">Novo</a> <a href="../index.php" class="btn btn-danger btn-sm float-end">Voltar</a></h3>
    <table class="table table-striped mt-3">
        <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['nome'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['tipo'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
