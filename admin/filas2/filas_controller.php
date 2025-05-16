<?php
// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Criar nova fila
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $tipo = $_POST['tipo'];
        $local_id = (int)$_POST['local_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da fila é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO filas (nome, tipo, local_id) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $tipo, $local_id]);
                $response['success'] = true;
                $response['message'] = 'Fila criada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar fila: ' . $e->getMessage();
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Atualizar fila existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $tipo = $_POST['tipo'];
        $local_id = (int)$_POST['local_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome da fila é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE filas SET nome = ?, tipo = ?, local_id = ? WHERE id = ?");
                $stmt->execute([$nome, $tipo, $local_id, $id]);
                $response['success'] = true;
                $response['message'] = 'Fila atualizada com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar fila: ' . $e->getMessage();
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
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
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Buscar dados de uma fila para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare("SELECT id, nome, tipo, local_id FROM filas WHERE id = ?");
            $stmt->execute([$id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($fila) {
                $response['success'] = true;
                $response['data'] = $fila;
            } else {
                $response['message'] = 'Fila não encontrada';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados da fila: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>