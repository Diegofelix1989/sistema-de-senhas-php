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
 * Obtém todas as filas com informações de local
 */
function obterFilas() {
    global $pdo;
    $stmt = $pdo->query("SELECT f.id, f.nome, f.tipo, f.prefixo, f.local_id, l.nome AS local 
                         FROM filas f 
                         JOIN locais l ON f.local_id = l.id
                         ORDER BY f.nome");
    return $stmt->fetchAll();
}

/**
 * Obtém todos os locais para os dropdowns dos modais
 */
function obterLocais() {
    global $pdo;
    $stmtLocais = $pdo->query("SELECT id, nome FROM locais ORDER BY nome");
    return $stmtLocais->fetchAll();
}

/**
 * Busca dados de uma fila específica pelo ID
 */
function obterFilaPorId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, nome, tipo, prefixo, local_id FROM filas WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Verifica se o prefixo já existe para o local selecionado
 */
function prefixoExiste($prefixo, $local_id, $id = null) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) FROM filas WHERE prefixo = ? AND local_id = ?";
    $params = [$prefixo, $local_id];
    
    // Se for uma atualização, ignorar o registro atual
    if ($id) {
        $sql .= " AND id != ?";
        $params[] = $id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return (int)$stmt->fetchColumn() > 0;
}

// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Criar nova fila
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $tipo = $_POST['tipo'];
        $prefixo = strtoupper(trim($_POST['prefixo']));
        $local_id = (int)$_POST['local_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da fila é obrigatório';
        } elseif (empty($prefixo)) {
            $response['message'] = 'O prefixo da fila é obrigatório';
        } elseif (strlen($prefixo) > 5) {
            $response['message'] = 'O prefixo deve ter no máximo 5 caracteres';
        } elseif (prefixoExiste($prefixo, $local_id)) {
            $response['message'] = 'Este prefixo já está em uso para o local selecionado';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO filas (nome, tipo, prefixo, local_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $tipo, $prefixo, $local_id]);
                $response['success'] = true;
                $response['message'] = 'Fila criada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar fila: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Atualizar fila existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $tipo = $_POST['tipo'];
        $prefixo = strtoupper(trim($_POST['prefixo']));
        $local_id = (int)$_POST['local_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da fila é obrigatório';
        } elseif (empty($prefixo)) {
            $response['message'] = 'O prefixo da fila é obrigatório';
        } elseif (strlen($prefixo) > 5) {
            $response['message'] = 'O prefixo deve ter no máximo 5 caracteres';
        } elseif (prefixoExiste($prefixo, $local_id, $id)) {
            $response['message'] = 'Este prefixo já está em uso para o local selecionado';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE filas SET nome = ?, tipo = ?, prefixo = ?, local_id = ? WHERE id = ?");
                $stmt->execute([$nome, $tipo, $prefixo, $local_id, $id]);
                $response['success'] = true;
                $response['message'] = 'Fila atualizada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar fila: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Excluir fila
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM filas WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Fila excluída com sucesso!';
            $response['redirect'] = 'index.php';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir fila: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
    
    // Buscar dados de uma fila para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $fila = obterFilaPorId($id);
            
            if ($fila) {
                $response['success'] = true;
                $response['data'] = $fila;
            } else {
                $response['message'] = 'Fila não encontrada';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados da fila: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
}

// Buscar dados para exibição na página principal
$filas = obterFilas();
$locais = obterLocais();
