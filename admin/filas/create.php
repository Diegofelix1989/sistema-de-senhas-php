<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$locais = $pdo->query("SELECT * FROM locais")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $local_id = $_POST['local_id'];
    $stmt = $pdo->prepare("INSERT INTO filas (nome, local_id) VALUES (?, ?)");
    $stmt->execute([$nome, $local_id]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Nova Fila</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Nova Fila</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" required></div>
    <div class="mb-3">
        <label>Local</label>
        <select name="local_id" class="form-control" required>
            <?php foreach ($locais as $l): ?>
                <option value="<?= $l['id'] ?>"><?= $l['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>

