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