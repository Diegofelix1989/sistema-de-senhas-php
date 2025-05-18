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
 * Obtém todos os usuários
 */
function obterUsuarios() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY nome");
    return $stmt->fetchAll();
}

/**
 * Busca dados de um usuário específico pelo ID
 */
function obterUsuarioPorId($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Verifica se o email já existe
 */
function emailExiste($email, $id = null) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
    $params = [$email];
    
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
    
    // Criar novo usuário
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $tipo = $_POST['tipo'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome do usuário é obrigatório';
        } elseif (empty($email)) {
            $response['message'] = 'O email do usuário é obrigatório';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'O email informado é inválido';
        } elseif (empty($senha)) {
            $response['message'] = 'A senha é obrigatória';
        } elseif (strlen($senha) < 6) {
            $response['message'] = 'A senha deve ter pelo menos 6 caracteres';
        } elseif (emailExiste($email)) {
            $response['message'] = 'Este email já está em uso';
        } else {
            try {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $senhaHash, $tipo]);
                $response['success'] = true;
                $response['message'] = 'Usuário criado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar usuário: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Atualizar usuário existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        $tipo = $_POST['tipo'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome do usuário é obrigatório';
        } elseif (empty($email)) {
            $response['message'] = 'O email do usuário é obrigatório';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'O email informado é inválido';
        } elseif (emailExiste($email, $id)) {
            $response['message'] = 'Este email já está em uso';
        } else {
            try {
                // Se a senha foi fornecida, atualiza a senha
                if (!empty($senha)) {
                    if (strlen($senha) < 6) {
                        $response['message'] = 'A senha deve ter pelo menos 6 caracteres';
                        responderJSON($response);
                    }
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id = ?");
                    $stmt->execute([$nome, $email, $senhaHash, $tipo, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?");
                    $stmt->execute([$nome, $email, $tipo, $id]);
                }
                
                $response['success'] = true;
                $response['message'] = 'Usuário atualizado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar usuário: ' . $e->getMessage();
            }
        }
        
        responderJSON($response);
    }
    
    // Excluir usuário
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        // Não permitir que o usuário exclua a si mesmo
        if ($_SESSION['usuario']['id'] == $id) {
            $response['message'] = 'Você não pode excluir seu próprio usuário!';
            responderJSON($response);
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Usuário excluído com sucesso!';
            $response['redirect'] = 'index.php';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir usuário: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
    
    // Buscar dados de um usuário para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $usuario = obterUsuarioPorId($id);
            
            if ($usuario) {
                $response['success'] = true;
                $response['data'] = $usuario;
            } else {
                $response['message'] = 'Usuário não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados do usuário: ' . $e->getMessage();
        }
        
        responderJSON($response);
    }
}

// Buscar dados para exibição na página principal
$usuarios = obterUsuarios();