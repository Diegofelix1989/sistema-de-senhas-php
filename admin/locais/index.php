<?php
// Incluir o controlador que contém toda a lógica de backend
require_once 'locais_controller.php';
include '../header.php';
include '../sidebar.php';
?>
<!-- Conteúdo Principal -->
<div class="flex-grow-1">
    
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciamento de Locais</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Novo Local
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        
        <!-- Alertas para feedback -->
        <div id="alertArea"></div>
        
        <!-- Tabela de Locais -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Criado em</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($locais)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">Nenhum local cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($locais as $local): ?>
                                    <tr>
                                        <td><?= $local['id'] ?></td>
                                        <td><?= htmlspecialchars($local['nome']) ?></td>
                                        <td class="description-cell" title="<?= htmlspecialchars($local['descricao']) ?>">
                                            <?= htmlspecialchars($local['descricao'] ?: 'Sem descrição') ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($local['criado_em'])) ?></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $local['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $local['id'] ?>" 
                                                    data-nome="<?= htmlspecialchars($local['nome']) ?>">
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

<!-- Modal para Criar Local -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createModalLabel">Novo Local</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create-nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create-nome" name="nome" maxlength="100" required>
                        <div class="form-text">Nome identificador do local (ex: Unidade Centro, Filial Norte)</div>
                    </div>
                    <div class="mb-3">
                        <label for="create-descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="create-descricao" name="descricao" rows="3" placeholder="Descrição opcional do local"></textarea>
                        <div class="form-text">Informações adicionais sobre o local</div>
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

<!-- Modal para Editar Local -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editModalLabel">Editar Local</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-nome" name="nome" maxlength="100" required>
                        <div class="form-text">Nome identificador do local (ex: Unidade Centro, Filial Norte)</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit-descricao" name="descricao" rows="3" placeholder="Descrição opcional do local"></textarea>
                        <div class="form-text">Informações adicionais sobre o local</div>
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
                <p>Tem certeza que deseja excluir o local <strong id="delete-nome"></strong>?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita. O local só pode ser excluído se não possuir filas associadas.</small></p>
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
        
        // Criar novo local
        const createForm = document.getElementById('createForm');
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(createForm);
            formData.append('action', 'create');
            
            fetch('locais_controller.php', {
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
                
                fetch('locais_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit-id').value = data.data.id;
                        document.getElementById('edit-nome').value = data.data.nome;
                        document.getElementById('edit-descricao').value = data.data.descricao || '';
                        
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
        
        // Atualizar local
        const editForm = document.getElementById('editForm');
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editForm);
            formData.append('action', 'update');
            
            fetch('locais_controller.php', {
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
        
        // Excluir local
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('delete-id').value;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch('locais_controller.php', {
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