<?php
// Incluir o controlador que contém toda a lógica de backend
require_once 'filas_controller.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gerenciamento de Filas</title>
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
            <h2>Gerenciamento de Filas</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Nova Fila
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <!-- Alertas para feedback -->
        <div id="alertArea"></div>
        
        <!-- Tabela de Filas -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Prefixo</th>
                                <th>Tipo</th>
                                <th>Local</th>
                                <th>Dígitos</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3">Nenhuma fila cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filas as $fila): ?>
                                    <tr>
                                        <td><?= $fila['id'] ?></td>
                                        <td><?= htmlspecialchars($fila['nome']) ?></td>
                                        <td><span class="badge bg-dark"><?= htmlspecialchars($fila['prefixo']) ?></span></td>
                                        <td>
                                            <?php if ($fila['tipo'] == 'comum'): ?>
                                                <span class="badge bg-primary">Comum</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Prioritária</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($fila['local']) ?></td>
                                        <td><span class="badge bg-info"><?= $fila['tamanho_senha'] ?></span></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $fila['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $fila['id'] ?>" 
                                                    data-nome="<?= htmlspecialchars($fila['nome']) ?>">
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

    <!-- Modal para Criar Fila -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createModalLabel">Nova Fila</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create-nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="create-nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-prefixo" class="form-label">Prefixo (máx. 5 caracteres)</label>
                            <input type="text" class="form-control" id="create-prefixo" name="prefixo" maxlength="5" required>
                            <div class="form-text">Código usado para identificar a fila nas senhas (ex: SG, AT)</div>
                        </div>
                        <div class="mb-3">
                            <label for="create-tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="create-tipo" name="tipo" required>
                                <option value="comum">Comum</option>
                                <option value="prioritaria">Prioritária</option>
                            </select>
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
                            <label for="create-tamanho-senha" class="form-label">Tamanho da Senha (dígitos)</label>
                            <input type="number" class="form-control" id="create-tamanho-senha" name="tamanho_senha" min="1" max="6" value="3" required>
                            <div class="form-text">Quantidade de dígitos numéricos nas senhas (ex: 3 = 001, 4 = 0001)</div>
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

    <!-- Modal para Editar Fila -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editModalLabel">Editar Fila</h5>
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
                            <label for="edit-prefixo" class="form-label">Prefixo (máx. 5 caracteres)</label>
                            <input type="text" class="form-control" id="edit-prefixo" name="prefixo" maxlength="5" required>
                            <div class="form-text">Código usado para identificar a fila nas senhas (ex: SG, AT)</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="edit-tipo" name="tipo" required>
                                <option value="comum">Comum</option>
                                <option value="prioritaria">Prioritária</option>
                            </select>
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
                            <label for="edit-tamanho-senha" class="form-label">Tamanho da Senha (dígitos)</label>
                            <input type="number" class="form-control" id="edit-tamanho-senha" name="tamanho_senha" min="1" max="6" required>
                            <div class="form-text">Quantidade de dígitos numéricos nas senhas (ex: 3 = 001, 4 = 0001)</div>
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
                    <p>Tem certeza que deseja excluir a fila <strong id="delete-nome"></strong>?</p>
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
            
            // Normalizar prefixos (converter para maiúsculas)
            function normalizarPrefixo(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
            
            // Aplicar normalização aos campos de prefixo
            normalizarPrefixo(document.getElementById('create-prefixo'));
            normalizarPrefixo(document.getElementById('edit-prefixo'));
            
            // Criar nova fila
            const createForm = document.getElementById('createForm');
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(createForm);
                formData.append('action', 'create');
                
                fetch('filas_controller.php', {
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
                    
                    fetch('filas_controller.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('edit-id').value = data.data.id;
                            document.getElementById('edit-nome').value = data.data.nome;
                            document.getElementById('edit-prefixo').value = data.data.prefixo;
                            document.getElementById('edit-tipo').value = data.data.tipo;
                            document.getElementById('edit-local').value = data.data.local_id;
                            document.getElementById('edit-tamanho-senha').value = data.data.tamanho_senha || 3;
                            
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
            
            // Atualizar fila
            const editForm = document.getElementById('editForm');
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(editForm);
                formData.append('action', 'update');
                
                fetch('filas_controller.php', {
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
            
            // Excluir fila
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('delete-id').value;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('filas_controller.php', {
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
