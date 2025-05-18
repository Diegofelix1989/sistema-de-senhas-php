<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
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
 * Obtém todos os locais
 */
function obterLocais() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, nome, descricao, criado_em 
                         FROM locais 
                         ORDER BY nome");
    return $stmt->fetchAll();
}

/**
 * Busca dados de um local específico pelo ID
 */
function obterLocalPorId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, nome, descricao FROM locais WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Verifica se o nome do local já existe
 */
function nomeLocalExiste($nome, $id = null) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) FROM locais WHERE nome = ?";
    $params = [$nome];
    
    // Se for uma atualização, ignorar o registro atual
    if ($id) {
        $sql .= " AND id != ?";
        $params[] = $id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Verifica se o local possui filas associadas (para validar exclusão)
 */
function localPossuiFilas($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM filas WHERE local_id = ?");
    $stmt->execute([$id]);
    return (int)$stmt->fetchColumn() > 0;
}

// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Criar novo local
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        
        if (empty($nome)) {
            $response['message'] = 'O nome do local é obrigatório';
        } elseif (nomeLocalExiste($nome)) {
            $response['message'] = 'Este nome de local já está em uso';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO locais (nome, descricao) VALUES (?, ?)");
                $stmt->execute([$nome, $descricao]);
                $response['success'] = true;
                $response['message'] = 'Local criado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar local: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Atualizar local existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        
        if (empty($nome)) {
            $response['message'] = 'O nome do local é obrigatório';
        } elseif (nomeLocalExiste($nome, $id)) {
            $response['message'] = 'Este nome de local já está em uso';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE locais SET nome = ?, descricao = ? WHERE id = ?");
                $stmt->execute([$nome, $descricao, $id]);
                $response['success'] = true;
                $response['message'] = 'Local atualizado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar local: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Excluir local
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        // Verificar se o local possui filas associadas
        if (localPossuiFilas($id)) {
            $response['message'] = 'Não é possível excluir este local pois existem filas associadas a ele.';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM locais WHERE id = ?");
                $stmt->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'Local excluído com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao excluir local: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Buscar dados de um local para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $local = obterLocalPorId($id);
            
            if ($local) {
                $response['success'] = true;
                $response['data'] = $local;
            } else {
                $response['message'] = 'Local não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados do local: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
}

// Buscar dados para exibição na página principal
$locais = obterLocais();
