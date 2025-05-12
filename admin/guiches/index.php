<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$sql = "SELECT g.id, g.nome, l.nome AS local, f.nome AS fila 
        FROM guiches g 
        JOIN locais l ON g.local_id = l.id 
        JOIN filas f ON g.fila_id = f.id";
$guiches = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guichês</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Guichês <a href="create.php" class="btn btn-success btn-sm">Novo</a>
    <a href="../logout.php" class="btn btn-danger btn-sm float-end">Sair</a>
</h3>
<table class="table table-striped mt-3">
    <thead><tr><th>ID</th><th>Nome</th><th>Local</th><th>Fila</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($guiches as $g): ?>
        <tr>
            <td><?= $g['id'] ?></td>
            <td><?= $g['nome'] ?></td>
            <td><?= $g['local'] ?></td>
            <td><?= $g['fila'] ?></td>
            <td>
                <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="delete.php?id=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
