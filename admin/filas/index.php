<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$stmt = $pdo->query("SELECT f.id, f.nome, l.nome AS local FROM filas f JOIN locais l ON f.local_id = l.id");
$filas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Filas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Filas <a href="create.php" class="btn btn-success btn-sm">Nova</a>
        <a href="../index.php" class="btn btn-danger btn-sm float-end">Voltar</a>
    <table class="table table-striped mt-3">
        <thead><tr><th>ID</th><th>Nome</th><th>Local</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($filas as $f): ?>
            <tr>
                <td><?= $f['id'] ?></td>
                <td><?= $f['nome'] ?></td>
                <td><?= $f['local'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $f['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
