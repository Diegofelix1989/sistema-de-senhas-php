<?php
// Incluir o controlador que contém toda a lógica de backend
require_once 'usuarios_controller.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gerenciamento de Usuários</title>
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
            <h2>Gerenciamento de Usuários</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Novo Usuário
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <!-- Alertas para feedback -->
        <div id="alertArea"></div>
        
        <!-- Tabela de Usuários -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Cadastrado em</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3">Nenhum usuário cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['id'] ?></td>
                                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td>
                                            <?php if ($usuario['tipo'] == 'admin'): ?>
                                                <span class="badge bg-danger">Administrador</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Atendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $usuario['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $usuario['id'] ?>" 
                                                    data-nome="<?= htmlspecialchars($usuario['nome']) ?>"
                                                    <?= ($_SESSION['usuario']['id'] == $usuario['id']) ? 'disabled' : '' ?>>
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

    <!-- Modal para Criar Usuário -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createModalLabel">Novo Usuário</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create-nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="create-nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="create-email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="create-senha" name="senha" required minlength="6">
                            <div class="form-text">A senha deve ter pelo menos 6 caracteres</div>
                        </div>
                        <div class="mb-3">
                            <label for="create-tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="create-tipo" name="tipo" required>
                                <option value="admin">Administrador</option>
                                <option value="atendente" selected>Atendente</option>
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

    <!-- Modal para Editar Usuário -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuário</h5>
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
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-senha" class="form-label">Nova Senha (opcional)</label>
                            <input type="password" class="form-control" id="edit-senha" name="senha" minlength="6">
                            <div class="form-text">Deixe em branco para manter a senha atual</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="edit-tipo" name="tipo" required>
                                <option value="admin">Administrador</option>
                                <option value="atendente">Atendente</option>
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
                    <p>Tem certeza que deseja excluir o usuário <strong id="delete-nome"></strong>?</p>
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
            
            // Criar novo usuário
            const createForm = document.getElementById('createForm');
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(createForm);
                formData.append('action', 'create');
                
                fetch('usuarios_controller.php', {
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
                    
                    fetch('usuarios_controller.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('edit-id').value = data.data.id;
                            document.getElementById('edit-nome').value = data.data.nome;
                            document.getElementById('edit-email').value = data.data.email;
                            document.getElementById('edit-tipo').value = data.data.tipo;
                            
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
            
            // Atualizar usuário
            const editForm = document.getElementById('editForm');
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(editForm);
                formData.append('action', 'update');
                
                fetch('usuarios_controller.php', {
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
            
            // Excluir usuário
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('delete-id').value;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('usuarios_controller.php', {
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
        });
    </script>
</body>
</html>