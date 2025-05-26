<?php
//session_start();
//if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
//    header('Location: ../index.php');
//    exit();
//}
include_once __DIR__ . '/../../includes/conexao.php';

/**
 * Responde com JSON para requisições AJAX
 */
function responderJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Obtém todas as publicidades com informações da tela
 */
function obterPublicidades() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.id, p.titulo, p.tipo_midia, p.media_path, p.duracao, p.id_tela, t.nome AS tela 
                         FROM publicidades p 
                         JOIN telas t ON p.id_tela = t.id
                         ORDER BY p.titulo");
    return $stmt->fetchAll();
}

/**
 * Obtém todas as telas para os dropdowns dos modais
 */
function obterTelas() {
    global $pdo;
    $stmtTelas = $pdo->query("SELECT id, nome FROM telas ORDER BY nome");
    return $stmtTelas->fetchAll();
}

/**
 * Busca dados de uma publicidade específica pelo ID
 */
function obterPublicidadePorId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, titulo, tipo_midia, media_path, duracao, id_tela FROM publicidades WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Configurações de upload
    // O diretório de upload físico no servidor
    $uploadDirPhysical = dirname(__DIR__) . '/uploads/'; 
    // O prefixo do caminho que será salvo no banco de dados para acesso via navegador
    $uploadPathPrefix = '../uploads/'; 

    // Criar nova publicidade
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $titulo = trim($_POST['titulo']);
        $tipo_midia = $_POST['tipo_midia'];
        $duracao = (int)$_POST['duracao'];
        $id_tela = (int)$_POST['id_tela'];
        $media_path = ''; // Inicializa media_path

        if ($tipo_midia === 'imagem' || $tipo_midia === 'video') {
            if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($_FILES['media_file']['name']);
                $targetFilePath = $uploadDirPhysical . $fileName;

                // Garante que o diretório de upload físico existe
                if (!is_dir($uploadDirPhysical)) {
                    mkdir($uploadDirPhysical, 0777, true);
                }

                if (move_uploaded_file($_FILES['media_file']['tmp_name'], $targetFilePath)) {
                    // Salva o caminho com o prefixo desejado no banco de dados
                    $media_path = $uploadPathPrefix . $fileName; 
                } else {
                    $response['message'] = 'Erro ao fazer upload do arquivo.';
                    responderJSON($response);
                }
            } else {
                $response['message'] = 'Por favor, selecione um arquivo para upload.';
                responderJSON($response);
            }
        } elseif ($tipo_midia === 'texto') {
            $media_path = trim($_POST['media_text']);
        }
        
        if (empty($titulo)) {
            $response['message'] = 'O título da publicidade é obrigatório';
        } elseif (empty($tipo_midia)) {
            $response['message'] = 'O tipo de mídia é obrigatório';
        } elseif (empty($duracao) || $duracao < 1) {
            $response['message'] = 'A duração é obrigatória e deve ser um número positivo';
        } elseif (empty($id_tela)) {
            $response['message'] = 'A tela é obrigatória';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO publicidades (titulo, tipo_midia, media_path, duracao, id_tela) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $tipo_midia, $media_path, $duracao, $id_tela]);
                $response['success'] = true;
                $response['message'] = 'Publicidade criada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar publicidade: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Atualizar publicidade existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $titulo = trim($_POST['titulo']);
        $tipo_midia = $_POST['tipo_midia'];
        $duracao = (int)$_POST['duracao'];
        $id_tela = (int)$_POST['id_tela'];
        $media_path = $_POST['current_media_path'] ?? ''; 

        if ($tipo_midia === 'imagem' || $tipo_midia === 'video') {
            if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                // Para remover o arquivo antigo, precisamos do caminho físico.
                // O basename() é usado para obter apenas o nome do arquivo da string do banco de dados (../uploads/nome.ext)
                if (!empty($_POST['current_media_path'])) {
                    $oldFileName = basename($_POST['current_media_path']);
                    if (file_exists($uploadDirPhysical . $oldFileName)) {
                        unlink($uploadDirPhysical . $oldFileName);
                    }
                }

                $fileName = uniqid() . '_' . basename($_FILES['media_file']['name']);
                $targetFilePath = $uploadDirPhysical . $fileName;

                if (move_uploaded_file($_FILES['media_file']['tmp_name'], $targetFilePath)) {
                    $media_path = $uploadPathPrefix . $fileName;
                } else {
                    $response['message'] = 'Erro ao fazer upload do novo arquivo.';
                    responderJSON($response);
                }
            }
        } elseif ($tipo_midia === 'texto') {
            $media_path = trim($_POST['media_text']);
            // Se mudou para texto, remove a mídia anterior se for imagem/video
            if (!empty($_POST['current_media_path']) && (strpos($_POST['current_media_path'], '.jpg') || strpos($_POST['current_media_path'], '.png') || strpos($_POST['current_media_path'], '.mp4') || strpos($_POST['current_media_path'], '.gif'))) {
                $oldFileName = basename($_POST['current_media_path']);
                 if (file_exists($uploadDirPhysical . $oldFileName)) {
                    unlink($uploadDirPhysical . $oldFileName);
                }
            }
        }
        
        if (empty($titulo)) {
            $response['message'] = 'O título da publicidade é obrigatório';
        } elseif (empty($tipo_midia)) {
            $response['message'] = 'O tipo de mídia é obrigatório';
        } elseif (empty($duracao) || $duracao < 1) {
            $response['message'] = 'A duração é obrigatória e deve ser um número positivo';
        } elseif (empty($id_tela)) {
            $response['message'] = 'A tela é obrigatória';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE publicidades SET titulo = ?, tipo_midia = ?, media_path = ?, duracao = ?, id_tela = ? WHERE id = ?");
                $stmt->execute([$titulo, $tipo_midia, $media_path, $duracao, $id_tela, $id]);
                $response['success'] = true;
                $response['message'] = 'Publicidade atualizada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar publicidade: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Excluir publicidade
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            // Obtém o caminho da mídia antes de excluir o registro
            $publicidade = obterPublicidadePorId($id);
            if ($publicidade && ($publicidade['tipo_midia'] === 'imagem' || $publicidade['tipo_midia'] === 'video')) {
                // Para remover o arquivo antigo, precisamos do caminho físico.
                $fileNameToDelete = basename($publicidade['media_path']);
                if (!empty($publicidade['media_path']) && file_exists($uploadDirPhysical . $fileNameToDelete)) {
                    unlink($uploadDirPhysical . $fileNameToDelete);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM publicidades WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Publicidade excluída com sucesso!';
            $response['redirect'] = 'index.php';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir publicidade: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
    
    // Buscar dados de uma publicidade para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $publicidade = obterPublicidadePorId($id);
            
            if ($publicidade) {
                $response['success'] = true;
                $response['data'] = $publicidade;
            } else {
                $response['message'] = 'Publicidade não encontrada';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados da publicidade: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
}

// Buscar dados para exibição na página principal
$publicidades = obterPublicidades();
$telas = obterTelas();