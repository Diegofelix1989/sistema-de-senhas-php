<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$sql = "SELECT s.id, s.nome, l.nome AS local, q.nome AS fila, s.tipo_exibicao 
        FROM telas s 
        JOIN locais l ON s.local_id = l.id 
        JOIN filas q ON s.fila_id = q.id";
$telas = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Telas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Telas <a href="create.php" class="btn btn-success btn-sm">Nova</a>
    <a href="../logout.php" class="btn btn-danger btn-sm float-end">Sair</a>
</h3>
<a href="../admin.php" class="btn btn-secondary btn-sm">Voltar</a>
<table class="table table-striped mt-3">
    <thead><tr><th>ID</th><th>Nome</th><th>Local</th><th>Fila</th><th>Tipo de Exibição</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($telas as $t): ?>
        <tr>
            <td><?= $t['id'] ?></td>
            <td><?= $t['nome'] ?></td>
            <td><?= $t['local'] ?></td>
            <td><?= $t['fila'] ?></td>
            <td><?= ucfirst($t['tipo_exibicao']) ?></td>
            <td>
                <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="delete.php?id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
