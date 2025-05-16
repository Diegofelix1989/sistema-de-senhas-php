<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
include_once __DIR__ . '/../../includes/conexao.php';

// Buscar todos os locais para os dropdowns dos modais
$stmtLocais = $pdo->query("SELECT id, nome FROM locais ORDER BY nome");
$locais = $stmtLocais->fetchAll();

// Buscar todas as filas para os dropdowns dos modais
$stmtFilas = $pdo->query("SELECT id, nome, local_id FROM filas ORDER BY nome");
$filas = $stmtFilas->fetchAll();

// Buscar todos os guichês com informações de local e fila
$stmt = $pdo->query("SELECT g.id, g.nome, g.local_id, g.fila_id, 
                     l.nome AS local_nome, f.nome AS fila_nome 
                     FROM guiches g 
                     JOIN locais l ON g.local_id = l.id
                     JOIN filas f ON g.fila_id = f.id
                     ORDER BY g.nome");
$guiches = $stmt->fetchAll();

// Processar operações CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    // Criar novo guichê
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = trim($_POST['nome']);
        $local_id = (int)$_POST['local_id'];
        $fila_id = (int)$_POST['fila_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome do guichê é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO guiches (nome, local_id, fila_id) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $local_id, $fila_id]);
                $response['success'] = true;
                $response['message'] = 'Guichê criado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao criar guichê: ' . $e->getMessage();
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Atualizar guichê existente
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $nome = trim($_POST['nome']);
        $local_id = (int)$_POST['local_id'];
        $fila_id = (int)$_POST['fila_id'];
        
        if (empty($nome)) {
            $response['message'] = 'O nome do guichê é obrigatório';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE guiches SET nome = ?, local_id = ?, fila_id = ? WHERE id = ?");
                $stmt->execute([$nome, $local_id, $fila_id, $id]);
                $response['success'] = true;
                $response['message'] = 'Guichê atualizado com sucesso!';
                $response['redirect'] = 'index.php';
            } catch (PDOException $e) {
                $response['message'] = 'Erro ao atualizar guichê: ' . $e->getMessage();
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Excluir guichê
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM guiches WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Guichê excluído com sucesso!';
            $response['redirect'] = 'index.php';
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir guichê: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Buscar dados de um guichê para edição
    if (isset($_POST['action']) && $_POST['action'] === 'get') {
        $id = (int)$_POST['id'];
        
        try {
            $stmt = $pdo->prepare("SELECT id, nome, local_id, fila_id FROM guiches WHERE id = ?");
            $stmt->execute([$id]);
            $guiche = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($guiche) {
                $response['success'] = true;
                $response['data'] = $guiche;
            } else {
                $response['message'] = 'Guichê não encontrado';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao buscar dados do guichê: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Buscar filas por local
    if (isset($_POST['action']) && $_POST['action'] === 'get_filas_by_local') {
        $local_id = (int)$_POST['local_id'];
        
        try {
            $stmt = $pdo->prepare("SELECT id, nome FROM filas WHERE local_id = ? ORDER BY nome");
            $stmt->execute([$local_id]);
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response['success'] = true;
            $response['data'] = $filas;
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = 'Erro ao buscar filas: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gerenciamento de Guichês</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .table-actions {
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciamento de Guichês</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Novo Guichê
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <!-- Alertas para feedback -->
        <div id="alertArea"></div>
        
        <!-- Tabela de Guichês -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Local</th>
                                <th>Fila</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($guiches)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">Nenhum guichê cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($guiches as $guiche): ?>
                                    <tr>
                                        <td><?= $guiche['id'] ?></td>
                                        <td><?= htmlspecialchars($guiche['nome']) ?></td>
                                        <td><?= htmlspecialchars($guiche['local_nome']) ?></td>
                                        <td><?= htmlspecialchars($guiche['fila_nome']) ?></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $guiche['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $guiche['id'] ?>" 
                                                    data-nome="<?= htmlspecialchars($guiche['nome']) ?>">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar Guichê -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createModalLabel">Novo Guichê</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create-nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="create-nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-local" class="form-label">Local</label>
                            <select class="form-select" id="create-local" name="local_id" required>
                                <option value="">Selecione um local</option>
                                <?php foreach ($locais as $local): ?>
                                    <option value="<?= $local['id'] ?>"><?= htmlspecialchars($local['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="create-fila" class="form-label">Fila</label>
                            <select class="form-select" id="create-fila" name="fila_id" required disabled>
                                <option value="">Selecione um local primeiro</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Guichê -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editModalLabel">Editar Guichê</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label for="edit-nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="edit-nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-local" class="form-label">Local</label>
                            <select class="form-select" id="edit-local" name="local_id" required>
                                <option value="">Selecione um local</option>
                                <?php foreach ($locais as $local): ?>
                                    <option value="<?= $local['id'] ?>"><?= htmlspecialchars($local['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-fila" class="form-label">Fila</label>
                            <select class="form-select" id="edit-fila" name="fila_id" required>
                                <option value="">Selecione um local primeiro</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmar Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o guichê <strong id="delete-nome"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm">
                        <input type="hidden" id="delete-id" name="id">
                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Função para mostrar alertas
            function showAlert(message, type = 'success') {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                `;
                document.getElementById('alertArea').appendChild(alertDiv);
                
                // Auto-fechar após 5 segundos
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                }, 5000);
            }
            
            // Função para carregar filas por local selecionado
            function carregarFilasPorLocal(localId, elementoFila, filaId = null) {
                if (!localId) {
                    elementoFila.innerHTML = '<option value="">Selecione um local primeiro</option>';
                    elementoFila.disabled = true;
                    return;
                }
                
                const formData = new FormData();
                formData.append('action', 'get_filas_by_local');
                formData.append('local_id', localId);
                
                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        elementoFila.disabled = false;
                        
                        let options = '<option value="">Selecione uma fila</option>';
                        if (data.data.length === 0) {
                            options = '<option value="">Nenhuma fila disponível para este local</option>';
                            elementoFila.disabled = true;
                        } else {
                            data.data.forEach(fila => {
                                const selected = filaId && filaId == fila.id ? 'selected' : '';
                                options += `<option value="${fila.id}" ${selected}>${fila.nome}</option>`;
                            });
                        }
                        elementoFila.innerHTML = options;
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Erro ao carregar filas: ' + error, 'danger');
                });
            }
            
            // Atualizar filas quando local é alterado (criar)
            document.getElementById('create-local').addEventListener('change', function() {
                carregarFilasPorLocal(this.value, document.getElementById('create-fila'));
            });
            
            // Atualizar filas quando local é alterado (editar)
            document.getElementById('edit-local').addEventListener('change', function() {
                carregarFilasPorLocal(this.value, document.getElementById('edit-fila'));
            });
            
            // Criar novo guichê
            const createForm = document.getElementById('createForm');
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(createForm);
                formData.append('action', 'create');
                
                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
                        modal.hide();
                        createForm.reset();
                        showAlert(data.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Erro ao processar solicitação: ' + error, 'danger');
                });
            });
            
            // Carregar dados para edição
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const formData = new FormData();
                    formData.append('action', 'get');
                    formData.append('id', id);
                    
                    fetch('index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const guiche = data.data;
                            document.getElementById('edit-id').value = guiche.id;
                            document.getElementById('edit-nome').value = guiche.nome;
                            document.getElementById('edit-local').value = guiche.local_id;
                            
                            // Carregar filas para o local selecionado e selecionar a fila atual
                            carregarFilasPorLocal(guiche.local_id, document.getElementById('edit-fila'), guiche.fila_id);
                            
                            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                            editModal.show();
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showAlert('Erro ao carregar dados: ' + error, 'danger');
                    });
                });
            });
            
            // Atualizar guichê
            const editForm = document.getElementById('editForm');
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(editForm);
                formData.append('action', 'update');
                
                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                        modal.hide();
                        showAlert(data.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Erro ao processar solicitação: ' + error, 'danger');
                });
            });
            
            // Preparar confirmação de exclusão
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nome = this.dataset.nome;
                    
                    document.getElementById('delete-id').value = id;
                    document.getElementById('delete-nome').textContent = nome;
                    
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });
            
            // Excluir guichê
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('delete-id').value;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        modal.hide();
                        showAlert(data.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Erro ao processar solicitação: ' + error, 'danger');
                });
            });
            
            // Pré-selecionar filas para os guichês existentes
            const filas = <?= json_encode($filas) ?>;
            
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Este código será executado quando o botão for clicado
                    // Já tratado no evento de carregamento de dados para edição
                });
            });
        });
    </script>
</body>
</html>
