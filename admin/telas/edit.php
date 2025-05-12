<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';


$id = $_GET['id'];
$tela = $pdo->prepare("SELECT * FROM telas WHERE id = ?");
$tela->execute([$id]);
$t = $tela->fetch();

$locais = $pdo->query("SELECT * FROM locais")->fetchAll();
$filas = $pdo->query("SELECT * FROM filas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $local_id = $_POST['local_id'];
    $fila_id = $_POST['fila_id'];
    $tipo_exibicao = $_POST['tipo_exibicao'];
    $stmt = $pdo->prepare("UPDATE telas SET nome = ?, local_id = ?, fila_id = ?, tipo_exibicao = ? WHERE id = ?");
    $stmt->execute([$nome, $local_id, $fila_id, $tipo_exibicao, $id]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Editar Tela</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Editar Tela</h3>
<form method="post">
    <div class="mb-3"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?= $t['nome'] ?>" required></div>
    <div class="mb-3">
        <label>Local</label>
        <select name="local_id" class="form-control" required>
            <?php foreach ($locais as $l): ?>
                <option value="<?= $l['id'] ?>" <?= ($t['local_id'] == $l['id']) ? 'selected' : '' ?>><?= $l['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Fila</label>
        <select name="fila_id" class="form-control" required>
            <?php foreach ($filas as $f): ?>
                <option value="<?= $f['id'] ?>" <?= ($t['fila_id'] == $f['id']) ? 'selected' : '' ?>><?= $f['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Tipo de Exibição</label>
        <select name="tipo_exibicao" class="form-control" required>
            <option value="senhas" <?= ($t['tipo_exibicao'] == 'senhas') ? 'selected' : '' ?>>Somente Senhas</option>
            <option value="publicidade" <?= ($t['tipo_exibicao'] == 'publicidade') ? 'selected' : '' ?>>Somente Publicidade</option>
            <option value="ambos" <?= ($t['tipo_exibicao'] == 'ambos') ? 'selected' : '' ?>>Ambos</option>
        </select>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
</body>
</html>

