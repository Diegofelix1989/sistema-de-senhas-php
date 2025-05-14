<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';
;

$telas = $pdo->query("SELECT * FROM telas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $tipo_midia = $_POST['tipo_midia'];
    $duracao = $_POST['duracao'];
    $id_tela = $_POST['id_tela'];
    $media_path = null;
    
    // Verifica se é upload de arquivo (imagem ou vídeo) ou texto simples
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
                    $media_path = $target_file;
                } else {
                    $error = "Erro ao fazer upload do arquivo.";
                }
            } else {
                $error = "Tipo de arquivo não permitido para " . $tipo_midia;
            }
        } else {
            $error = "Por favor, selecione um arquivo.";
        }
    } else { // tipo_midia == 'texto'
        $media_path = $_POST['texto_conteudo'];
    }
    
    if (!isset($error)) {
        $stmt = $pdo->prepare("INSERT INTO publicidades (titulo, tipo_midia, media_path, duracao, id_tela) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $tipo_midia, $media_path, $duracao, $id_tela]);
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nova Publicidade</title>
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
<h3>Nova Publicidade</h3>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label>Tipo de Mídia</label>
        <select name="tipo_midia" id="tipo_midia" class="form-control" required onchange="toggleMediaInput()">
            <option value="imagem">Imagem</option>
            <option value="video">Vídeo</option>
            <option value="texto">Texto</option>
        </select>
    </div>
    
    <div id="upload_section" class="mb-3">
        <label>Arquivo</label>
        <input type="file" name="media" class="form-control">
        <small class="text-muted">Formatos aceitos: jpg, jpeg, png, gif (para imagens) ou mp4, webm, mov, avi (para vídeos)</small>
    </div>
    
    <div id="texto_section" class="mb-3" style="display: none;">
        <label>Conteúdo do Texto</label>
        <textarea name="texto_conteudo" class="form-control" rows="5"></textarea>
    </div>
    
    <div class="mb-3">
        <label>Duração (segundos)</label>
        <input type="number" name="duracao" class="form-control" min="1" value="10" required>
    </div>
    
    <div class="mb-3">
        <label>Tela</label>
        <select name="id_tela" class="form-control" required>
            <?php foreach ($telas as $t): ?>
                <option value="<?= $t['id'] ?>"><?= $t['nome'] ?></option>
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