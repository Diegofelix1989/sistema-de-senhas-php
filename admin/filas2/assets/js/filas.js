/**
 * Script para gerenciamento de filas
 * Controla as operações CRUD via AJAX
 */
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
    
    // Criar nova fila
    const createForm = document.getElementById('createForm');
    if (createForm) {
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
    }
    
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
                    document.getElementById('edit-id').value = data.data.id;
                    document.getElementById('edit-nome').value = data.data.nome;
                    document.getElementById('edit-tipo').value = data.data.tipo;
                    document.getElementById('edit-local').value = data.data.local_id;
                    
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
    if (editForm) {
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
    }
    
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
    if (deleteForm) {
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
    }
});