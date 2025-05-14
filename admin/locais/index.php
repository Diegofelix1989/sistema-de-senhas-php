<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$stmt = $pdo->query("SELECT * FROM locais");
$locais = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Locais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Locais <a href="create.php" class="btn btn-success btn-sm">Novo</a> 
        <a href="../index.php" class="btn btn-danger btn-sm float-end">Voltar</a>
    <table class="table table-striped mt-3">
        <thead><tr><th>ID</th><th>Nome</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach ($locais as $l): ?>
            <tr>
                <td><?= $l['id'] ?></td>
                <td><?= $l['nome'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $l['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="delete.php?id=<?= $l['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
