<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';


$id = $_GET['id'];
$publicidade = $pdo->prepare("SELECT * FROM publicidades WHERE id = ?");
$publicidade->execute([$id]);
$p = $publicidade->fetch();

if (!$p) {
    header("Location: index.php");
    exit();
}

$telas = $pdo->query("SELECT * FROM telas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $tipo_midia = $_POST['tipo_midia'];
    $duracao = $_POST['duracao'];
    $id_tela = $_POST['id_tela'];
    $media_path = $p['media_path']; // Mantém o mesmo se não for atualizado
    
    // Verifica se há upload de novo arquivo (imagem ou vídeo) ou atualização de texto
    if ($tipo_midia == 'imagem' || $tipo_midia == 'video') {
        if (!empty($_FILES['media']['name'])) {
            $target_dir = "../uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Verifica tipo de arquivo
            $allowed_extensions = ($tipo_midia == 'imagem') ? 
                ['jpg', 'jpeg', 'png', 'gif'] : 
                ['mp4', 'webm', 'mov', 'avi'];
                
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                    // Se o arquivo antigo existir e não for texto, exclui
                    if ($p['media_path'] && $p['tipo_midia'] != 'texto' && file_exists($p['media_path'])) {
                        unlink($p['media_path']);
                    }
                    $media_path = $target_file;
                } else {
                    $error = "Erro ao fazer upload do arquivo.";
                }
            } else {
                $error = "Tipo de arquivo não permitido para " . $tipo_midia;
            }
        }
    } else { // tipo_midia == 'texto'
        $media_path = $_POST['texto_conteudo'];
    }
    
    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE publicidades SET titulo = ?, tipo_midia = ?, media_path = ?, duracao = ?, id_tela = ? WHERE id = ?");
        $stmt->execute([$titulo, $tipo_midia, $media_path, $duracao, $id_tela, $id]);
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Publicidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleMediaInput() {
            const tipoMidia = document.getElementById('tipo_midia').value;
            document.getElementById('upload_section').style.display = tipoMidia !== 'texto' ? 'block' : 'none';
            document.getElementById('texto_section').style.display = tipoMidia === 'texto' ? 'block' : 'none';
        }
    </script>
</head>
<body class="p-4">
<h3>Editar Publicidade</h3>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($p['titulo']) ?>" required>
    </div>
    
    <div class="mb-3">
        <label>Tipo de Mídia</label>
        <select name="tipo_midia" id="tipo_midia" class="form-control" required onchange="toggleMediaInput()">
            <option value="imagem" <?= ($p['tipo_midia'] == 'imagem') ? 'selected' : '' ?>>Imagem</option>
            <option value="video" <?= ($p['tipo_midia'] == 'video') ? 'selected' : '' ?>>Vídeo</option>
            <option value="texto" <?= ($p['tipo_midia'] == 'texto') ? 'selected' : '' ?>>Texto</option>
        </select>
    </div>
    
    <div id="upload_section" class="mb-3">
        <label>Arquivo</label>
        <?php if(($p['tipo_midia'] == 'imagem' || $p['tipo_midia'] == 'video') && $p['media_path']): ?>
            <div class="mb-2">
                <?php if($p['tipo_midia'] == 'imagem'): ?>
                    <img src="<?= $p['media_path'] ?>" height="100" alt="Imagem atual">
                <?php else: ?>
                    <a href="<?= $p['media_path'] ?>" target="_blank">Ver vídeo atual</a>
                <?php endif; ?>
                <p>Arquivo atual: <?= basename($p['media_path']) ?></p>
            </div>
        <?php endif; ?>
        <input type="file" name="media" class="form-control">
        <small class="text-muted">Deixe em branco para manter o arquivo atual. Formatos aceitos: jpg, jpeg, png, gif (para imagens) ou mp4, webm, mov, avi (para vídeos)</small>
    </div>
    
    <div id="texto_section" class="mb-3">
        <label>Conteúdo do Texto</label>
        <textarea name="texto_conteudo" class="form-control" rows="5"><?= ($p['tipo_midia'] == 'texto') ? htmlspecialchars($p['media_path']) : '' ?></textarea>
    </div>
    
    <div class="mb-3">
        <label>Duração (segundos)</label>
        <input type="number" name="duracao" class="form-control" min="1" value="<?= $p['duracao'] ?>" required>
    </div>
    
    <div class="mb-3">
        <label>Tela</label>
        <select name="id_tela" class="form-control" required>
            <?php foreach ($telas as $t): ?>
                <option value="<?= $t['id'] ?>" <?= ($p['id_tela'] == $t['id']) ? 'selected' : '' ?>><?= $t['nome'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>

<script>
    // Inicializa o estado do formulário
    document.addEventListener('DOMContentLoaded', function() {
        toggleMediaInput();
    });
</script>
</body>
</html>