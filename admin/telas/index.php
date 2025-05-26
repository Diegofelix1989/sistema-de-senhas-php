<?php
// Incluir o controlador que contém toda a lógica de backend
require_once 'telas_controller.php';
include '../header.php';
include '../sidebar.php';
?>
<!-- Conteúdo Principal -->
<div class="flex-grow-1">
    
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciamento de Telas</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Nova Tela
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <!-- Alertas para feedback -->
        <div id="alertArea"></div>
        
        <!-- Tabela de Telas -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo de Exibição</th>
                                <th>Local</th>
                                <th>Fila</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($telas)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3">Nenhuma tela cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($telas as $tela): ?>
                                    <tr>
                                        <td><?= $tela['id'] ?></td>
                                        <td><?= htmlspecialchars($tela['nome']) ?></td>
                                        <td>
                                            <?php if ($tela['tipo_exibicao'] == 'senhas'): ?>
                                                <span class="badge bg-primary">Senhas</span>
                                            <?php elseif ($tela['tipo_exibicao'] == 'publicidade'): ?>
                                                <span class="badge bg-success">Publicidade</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Ambos</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($tela['local']) ?></td>
                                        <td><?= htmlspecialchars($tela['fila']) ?></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $tela['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $tela['id'] ?>" 
                                                    data-nome="<?= htmlspecialchars($tela['nome']) ?>">
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
</div>

<!-- Modal para Criar Tela -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createModalLabel">Nova Tela</h5>
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
                        <select class="form-select" id="create-fila" name="fila_id" required>
                            <option value="">Selecione uma fila</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-tipo-exibicao" class="form-label">Tipo de Exibição</label>
                        <select class="form-select" id="create-tipo-exibicao" name="tipo_exibicao" required>
                            <option value="senhas">Somente Senhas</option>
                            <option value="publicidade">Somente Publicidade</option>
                            <option value="ambos">Senhas e Publicidade</option>
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

<!-- Modal para Editar Tela -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editModalLabel">Editar Tela</h5>
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
                            <option value="">Selecione uma fila</option>
                            <?php foreach ($filas as $fila): ?>
                                <option value="<?= $fila['id'] ?>"><?= htmlspecialchars($fila['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-tipo-exibicao" class="form-label">Tipo de Exibição</label>
                        <select class="form-select" id="edit-tipo-exibicao" name="tipo_exibicao" required>
                            <option value="senhas">Somente Senhas</option>
                            <option value="publicidade">Somente Publicidade</option>
                            <option value="ambos">Senhas e Publicidade</option>
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
                <p>Tem certeza que deseja excluir a tela <strong id="delete-nome"></strong>?</p>
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
        
        // Criar nova tela
        const createForm = document.getElementById('createForm');
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(createForm);
            formData.append('action', 'create');
            
            fetch('telas_controller.php', {
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
                
                fetch('telas_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit-id').value = data.data.id;
                        document.getElementById('edit-nome').value = data.data.nome;
                        document.getElementById('edit-local').value = data.data.local_id;
                        document.getElementById('edit-fila').value = data.data.fila_id;
                        document.getElementById('edit-tipo-exibicao').value = data.data.tipo_exibicao;
                        
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
        
        // Atualizar tela
        const editForm = document.getElementById('editForm');
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editForm);
            formData.append('action', 'update');
            
            fetch('telas_controller.php', {
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
        
        // Excluir tela
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('delete-id').value;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('telas_controller.php', {
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
<?php include '../footer.php'; ?>