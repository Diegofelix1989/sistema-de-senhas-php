<?php
// Incluir o controlador que contém toda a lógica de backend
require_once 'publicidades_controller.php';
include '../header.php';
include '../sidebar.php';
?>
<!-- Conteúdo Principal -->
<div class="flex-grow-1">
    
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciamento de Publicidades</h2>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Nova Publicidade
                </button>
                <a href="../index.php" class="btn btn-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div id="alertArea"></div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Tipo de Mídia</th>
                                <th>Mídia</th>
                                <th>Duração (seg)</th>
                                <th>Tela</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($publicidades)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-3">Nenhuma publicidade cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($publicidades as $publicidade): ?>
                                    <tr>
                                        <td><?= $publicidade['id'] ?></td>
                                        <td><?= htmlspecialchars($publicidade['titulo']) ?></td>
                                        <td>
                                            <?php if ($publicidade['tipo_midia'] == 'imagem'): ?>
                                                <span class="badge bg-primary">Imagem</span>
                                            <?php elseif ($publicidade['tipo_midia'] == 'video'): ?>
                                                <span class="badge bg-danger">Vídeo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Texto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($publicidade['tipo_midia'] == 'imagem' && !empty($publicidade['media_path'])): ?>
                                                <img src="<?= htmlspecialchars($publicidade['media_path']) ?>" alt="Thumbnail" class="media-thumbnail">
                                            <?php elseif ($publicidade['tipo_midia'] == 'video' && !empty($publicidade['media_path'])): ?>
                                                <video class="media-thumbnail" controls muted>
                                                    <source src="<?= htmlspecialchars($publicidade['media_path']) ?>" type="video/mp4">
                                                    Seu navegador não suporta vídeos.
                                                </video>
                                            <?php elseif ($publicidade['tipo_midia'] == 'texto' && !empty($publicidade['media_path'])): ?>
                                                <span class="media-text-preview" title="<?= htmlspecialchars($publicidade['media_path']) ?>">
                                                    <?= htmlspecialchars($publicidade['media_path']) ?>
                                                </span>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-info"><?= $publicidade['duracao'] ?></span></td>
                                        <td><?= htmlspecialchars($publicidade['tela']) ?></td>
                                        <td class="text-end table-actions">
                                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                                    data-id="<?= $publicidade['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-id="<?= $publicidade['id'] ?>" 
                                                    data-titulo="<?= htmlspecialchars($publicidade['titulo']) ?>">
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
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="createModalLabel">Nova Publicidade</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="createForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create-titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="create-titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-tipo-midia" class="form-label">Tipo de Mídia</label>
                            <select class="form-select" id="create-tipo-midia" name="tipo_midia" required>
                                <option value="imagem">Imagem</option>
                                <option value="video">Vídeo</option>
                                <option value="texto">Texto</option>
                            </select>
                        </div>
                        <div id="create-media-upload-area" class="mb-3">
                            <label for="create-media-file" class="form-label">Arquivo de Mídia (Imagem/Vídeo)</label>
                            <input type="file" class="form-control" id="create-media-file" name="media_file" accept="image/*,video/*">
                        </div>
                        <div id="create-media-text-area" class="mb-3" style="display: none;">
                            <label for="create-media-text" class="form-label">Conteúdo do Texto</label>
                            <textarea class="form-control" id="create-media-text" name="media_text" rows="3"></textarea>
                            <div class="form-text">Digite o texto da publicidade.</div>
                        </div>
                        <div class="mb-3">
                            <label for="create-duracao" class="form-label">Duração (segundos)</label>
                            <input type="number" class="form-control" id="create-duracao" name="duracao" min="1" value="5" required>
                            <div class="form-text">Duração em segundos que a publicidade será exibida</div>
                        </div>
                        <div class="mb-3">
                            <label for="create-id-tela" class="form-label">Tela</label>
                            <select class="form-select" id="create-id-tela" name="id_tela" required>
                                <option value="">Selecione uma tela</option>
                                <?php foreach ($telas as $tela): ?>
                                    <option value="<?= $tela['id'] ?>"><?= htmlspecialchars($tela['nome']) ?></option>
                                <?php endforeach; ?>
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

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editModalLabel">Editar Publicidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <input type="hidden" id="edit-current-media-path" name="current_media_path">
                        <div class="mb-3">
                            <label for="edit-titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="edit-titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-tipo-midia" class="form-label">Tipo de Mídia</label>
                            <select class="form-select" id="edit-tipo-midia" name="tipo_midia" required>
                                <option value="imagem">Imagem</option>
                                <option value="video">Vídeo</option>
                                <option value="texto">Texto</option>
                            </select>
                        </div>
                        <div id="edit-media-preview-area" class="mb-3" style="display: none;">
                            <label class="form-label">Mídia Atual:</label><br>
                            <div id="edit-current-media-display"></div>
                        </div>
                        <div id="edit-media-upload-area" class="mb-3">
                            <label for="edit-media-file" class="form-label">Novo Arquivo de Mídia (Opcional)</label>
                            <input type="file" class="form-control" id="edit-media-file" name="media_file" accept="image/*,video/*">
                            <div class="form-text">Deixe em branco para manter a mídia atual.</div>
                        </div>
                         <div id="edit-media-text-area" class="mb-3" style="display: none;">
                            <label for="edit-media-text" class="form-label">Conteúdo do Texto</label>
                            <textarea class="form-control" id="edit-media-text" name="media_text" rows="3"></textarea>
                            <div class="form-text">Digite o texto da publicidade.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-duracao" class="form-label">Duração (segundos)</label>
                            <input type="number" class="form-control" id="edit-duracao" name="duracao" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-id-tela" class="form-label">Tela</label>
                            <select class="form-select" id="edit-id-tela" name="id_tela" required>
                                <option value="">Selecione uma tela</option>
                                <?php foreach ($telas as $tela): ?>
                                    <option value="<?= $tela['id'] ?>"><?= htmlspecialchars($tela['nome']) ?></option>
                                <?php endforeach; ?>
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

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a publicidade <strong id="delete-titulo"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita e a mídia associada será removida.</small></p>
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
    </div>
</div>
<?php include '../footer.php'; ?>

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

            // Função para alternar campos de mídia
            function toggleMediaFields(modalPrefix, tipoMidia, mediaPath = '') {
                const uploadArea = document.getElementById(`${modalPrefix}-media-upload-area`);
                const fileInput = document.getElementById(`${modalPrefix}-media-file`);
                const textArea = document.getElementById(`${modalPrefix}-media-text-area`);
                const textInput = document.getElementById(`${modalPrefix}-media-text`);
                const previewArea = document.getElementById(`${modalPrefix}-media-preview-area`);
                const currentMediaDisplay = document.getElementById(`${modalPrefix}-current-media-display`);
                const currentMediaPathHidden = document.getElementById(`${modalPrefix}-current-media-path`);

                if (tipoMidia === 'imagem' || tipoMidia === 'video') {
                    uploadArea.style.display = 'block';
                    fileInput.required = true; // Torna o upload obrigatório na criação, opcional na edição
                    textArea.style.display = 'none';
                    textInput.removeAttribute('required');
                    textInput.value = ''; // Limpa o texto se mudar para imagem/video

                    if (modalPrefix === 'edit') {
                        previewArea.style.display = 'block';
                        currentMediaDisplay.innerHTML = ''; // Limpa o display anterior
                        if (mediaPath) {
                            if (tipoMidia === 'imagem') {
                                const img = document.createElement('img');
                                img.src = mediaPath;
                                img.alt = 'Mídia Atual';
                                img.className = 'media-thumbnail';
                                currentMediaDisplay.appendChild(img);
                            } else if (tipoMidia === 'video') {
                                const video = document.createElement('video');
                                video.src = mediaPath;
                                video.controls = true;
                                video.muted = true;
                                video.className = 'media-thumbnail';
                                currentMediaDisplay.appendChild(video);
                            }
                        } else {
                            currentMediaDisplay.innerHTML = '<small>Nenhuma mídia atual.</small>';
                        }
                        currentMediaPathHidden.value = mediaPath; // Armazena o caminho atual
                    } else {
                         if (fileInput) fileInput.required = true; // Só required na criação
                    }

                } else if (tipoMidia === 'texto') {
                    uploadArea.style.display = 'none';
                    fileInput.removeAttribute('required');
                    fileInput.value = ''; // Limpa o campo de arquivo
                    textArea.style.display = 'block';
                    textInput.required = true; // Torna o texto obrigatório

                    if (modalPrefix === 'edit') {
                        previewArea.style.display = 'none';
                        currentMediaDisplay.innerHTML = '';
                        textInput.value = mediaPath; // Define o texto como mídia atual
                        currentMediaPathHidden.value = ''; // Limpa o caminho do arquivo
                    } else {
                        textInput.required = true; // Só required na criação
                    }
                }
            }

            // Event Listeners para a mudança de tipo de mídia nos modais de Criar e Editar
            document.getElementById('create-tipo-midia').addEventListener('change', function() {
                toggleMediaFields('create', this.value);
            });
            document.getElementById('edit-tipo-midia').addEventListener('change', function() {
                // Ao mudar o tipo de mídia no modal de edição, não temos o mediaPath inicial
                toggleMediaFields('edit', this.value);
            });

            // Inicializa os campos ao abrir o modal de criação
            document.getElementById('createModal').addEventListener('show.bs.modal', function() {
                toggleMediaFields('create', document.getElementById('create-tipo-midia').value);
            });
            
            // Criar nova publicidade
            const createForm = document.getElementById('createForm');
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(createForm);
                formData.append('action', 'create');
                
                fetch('publicidades_controller.php', {
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
                    
                    fetch('publicidades_controller.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('edit-id').value = data.data.id;
                            document.getElementById('edit-titulo').value = data.data.titulo;
                            document.getElementById('edit-tipo-midia').value = data.data.tipo_midia;
                            document.getElementById('edit-duracao').value = data.data.duracao;
                            document.getElementById('edit-id-tela').value = data.data.id_tela;
                            
                            // Chama a função para alternar os campos e exibir a mídia atual
                            toggleMediaFields('edit', data.data.tipo_midia, data.data.media_path);

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
            
            // Atualizar publicidade
            const editForm = document.getElementById('editForm');
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(editForm);
                formData.append('action', 'update');
                
                fetch('publicidades_controller.php', {
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
                    const titulo = this.dataset.titulo;
                    
                    document.getElementById('delete-id').value = id;
                    document.getElementById('delete-titulo').textContent = titulo;
                    
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });
            
            // Excluir publicidade
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('delete-id').value;
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('publicidades_controller.php', {
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