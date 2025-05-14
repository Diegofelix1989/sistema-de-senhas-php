<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$sql = "SELECT p.id, p.titulo, p.tipo_midia, p.media_path, p.duracao, p.data_criacao, t.nome as tela 
        FROM publicidades p 
        JOIN telas t ON p.id_tela = t.id";
$publicidades = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Publicidades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h3>Publicidades <a href="create.php" class="btn btn-success btn-sm">Nova</a>
    <a href="../logout.php" class="btn btn-danger btn-sm float-end">Sair</a>
</h3>
<a href="../admin.php" class="btn btn-secondary btn-sm">Voltar</a>
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo de Mídia</th>
            <th>Arquivo</th>
            <th>Duração (seg)</th>
            <th>Data de Criação</th>
            <th>Tela</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($publicidades as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['titulo'] ?></td>
            <td><?= ucfirst($p['tipo_midia']) ?></td>
            <td>
                <?php if($p['tipo_midia'] == 'imagem'): ?>
                    <img src="<?= $p['media_path'] ?>" height="50" alt="Imagem">
                <?php elseif($p['tipo_midia'] == 'video'): ?>
                    <a href="<?= $p['media_path'] ?>" target="_blank">Ver vídeo</a>
                <?php else: ?>
                    <?= substr($p['media_path'], 0, 30) ?>...
                <?php endif; ?>
            </td>
            <td><?= $p['duracao'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($p['data_criacao'])) ?></td>
            <td><?= $p['tela'] ?></td>
            <td>
                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>