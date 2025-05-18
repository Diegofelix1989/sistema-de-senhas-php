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
 * Obtém todas as telas com informações de local e fila
 */
function obterTelas() {
    global $pdo;
    $stmt = $pdo->query("SELECT t.id, t.nome, t.tipo_exibicao, 
                         l.nome AS local, f.nome AS fila, l.id AS local_id, f.id AS fila_id
                         FROM telas t 
                         JOIN locais l ON t.local_id = l.id
                         JOIN filas f ON t.fila_id = f.id
                         ORDER BY t.nome");
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
 * Obtém todas as filas para os dropdowns dos modais
 */
function obterFilas() {
    global $pdo;
    $stmtFilas = $pdo->query("SELECT id, nome FROM filas ORDER BY nome");
    return $stmtFilas->fetchAll();
}

/**
 * Busca dados de uma tela específica pelo ID
 */
function obterTelaPorId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, nome, local_id, fila_id, tipo_exibicao FROM telas WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Criar nova tela
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $local_id = (int)$_POST['local_id'];
        $fila_id = (int)$_POST['fila_id'];
        $tipo_exibicao = $_POST['tipo_exibicao'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da tela é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO telas (nome, local_id, fila_id, tipo_exibicao) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $local_id, $fila_id, $tipo_exibicao]);
                $response['success'] = true;
                $response['message'] = 'Tela criada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar tela: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Atualizar tela existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $local_id = (int)$_POST['local_id'];
        $fila_id = (int)$_POST['fila_id'];
        $tipo_exibicao = $_POST['tipo_exibicao'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da tela é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE telas SET nome = ?, local_id = ?, fila_id = ?, tipo_exibicao = ? WHERE id = ?");
                $stmt->execute([$nome, $local_id, $fila_id, $tipo_exibicao, $id]);
                $response['success'] = true;
                $response['message'] = 'Tela atualizada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar tela: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Excluir tela
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM telas WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Tela excluída com sucesso!';
            $response['redirect'] = 'index.php';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir tela: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
    
    // Buscar dados de uma tela para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $tela = obterTelaPorId($id);
            
            if ($tela) {
                $response['success'] = true;
                $response['data'] = $tela;
            } else {
                $response['message'] = 'Tela não encontrada';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados da tela: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
}

// Buscar dados para exibição na página principal
$telas = obterTelas();
$locais = obterLocais();
$filas = obterFilas();
?>