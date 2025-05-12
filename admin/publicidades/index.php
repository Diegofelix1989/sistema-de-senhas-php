<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

$sql = "SELECT p.id, p.titulo, p.tipo_midia, p.media_path, p.duracao, p.data_criacao, t.nome as tela 
        FROM publicidades p 
        JOIN telas t ON p.id_tela = t.id";
$publicidades = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Publicidades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2ecc71;
            --secondary-dark: #27ae60;
            --danger: #e74c3c;
            --danger-dark: #c0392b;
            --light: #f8f9fa;
            --dark: #343a40;
            --text: #333;
            --border-radius: 8px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: var(--text);
            padding: 0;
            margin: 0;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .content-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .btn-custom-primary {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 16px;
            transition: all 0.2s;
        }
        
        .btn-custom-primary:hover {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .btn-custom-secondary {
            background-color: var(--secondary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 16px;
            transition: all 0.2s;
        }
        
        .btn-custom-secondary:hover {
            background-color: var(--secondary-dark);
            color: white;
        }
        
        .btn-custom-danger {
            background-color: var(--danger);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 16px;
            transition: all 0.2s;
        }
        
        .btn-custom-danger:hover {
            background-color: var(--danger-dark);
            color: white;
        }
        
        .btn-custom-light {
            background-color: #e9ecef;
            color: var(--dark);
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 16px;
            transition: all 0.2s;
        }
        
        .btn-custom-light:hover {
            background-color: #dee2e6;
        }
        
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-custom th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-custom td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .table-custom tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        .badge-media {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-imagem {
            background-color: #e3f2fd;
            color: #0d6efd;
        }
        
        .badge-video {
            background-color: #f9e4e8;
            color: #d63384;
        }
        
        .badge-outro {
            background-color: #fff3cd;
            color: #ffc107;
        }
        
        .preview-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        
        .preview-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .video-icon {
            color: #d63384;
            font-size: 1.5rem;
        }
        
        .file-icon {
            color: #6c757d;
            font-size: 1.5rem;
        }
        
        .actions-column {
            white-space: nowrap;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-ad me-2"></i>Gerenciamento de Publicidades
                </h1>
                <div>
                    <a href="../logout.php" class="btn-custom-danger">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <a href="../admin.php" class="btn-custom-light back-button">
                    <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
                </a>
                <a href="create.php" class="btn-custom-secondary">
                    <i class="fas fa-plus me-1"></i> Nova Publicidade
                </a>
            </div>
        </div>
        
        <!-- Content Section -->
        <div class="content-card">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Arquivo</th>
                        <th>Duração</th>
                        <th>Criado em</th>
                        <th>Tela</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($publicidades as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= $p['titulo'] ?></td>
                        <td>
                            <?php 
                            $badgeClass = 'badge-outro';
                            if($p['tipo_midia'] == 'imagem') {
                                $badgeClass = 'badge-imagem';
                            } elseif($p['tipo_midia'] == 'video') {
                                $badgeClass = 'badge-video';
                            }
                            ?>
                            <span class="badge-media <?= $badgeClass ?>">
                                <?= ucfirst($p['tipo_midia']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="preview-container">
                            <?php if($p['tipo_midia'] == 'imagem'): ?>
                                <img src="<?= $p['media_path'] ?>" alt="<?= $p['titulo'] ?>">
                            <?php elseif($p['tipo_midia'] == 'video'): ?>
                                <a href="<?= $p['media_path'] ?>" target="_blank" title="Ver vídeo">
                                    <i class="fas fa-video video-icon"></i>
                                </a>
                            <?php else: ?>
                                <i class="fas fa-file file-icon"></i>
                            <?php endif; ?>
                            </div>
                        </td>
                        <td><?= $p['duracao'] ?> seg</td>
                        <td><?= date('d/m/Y H:i', strtotime($p['data_criacao'])) ?></td>
                        <td>
                            <span class="badge-media badge-outro">
                                <?= $p['tela'] ?>
                            </span>
                        </td>
                        <td class="actions-column">
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn-custom-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn-custom-danger btn-sm ms-1" 
                               onclick="return confirm('Tem certeza que deseja excluir esta publicidade?')">
                                <i class="fas fa-trash me-1"></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($publicidades)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i> Nenhuma publicidade cadastrada.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>