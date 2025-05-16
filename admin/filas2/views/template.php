<!DOCTYPE html>
<html>
<head>
    <title>Gerenciamento de Filas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
                                <th>Tipo</th>
                                <th>Local</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filas)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">Nenhuma fila cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filas as $fila): ?>
                                    <tr>
                                        <td><?= $fila['id'] ?></td>
                                        <td><?= htmlspecialchars($fila['nome']) ?></td>
                                        <td>
                                            <?php if ($fila['tipo'] == 'comum'): ?>
                                                <span class="badge bg-primary">Comum</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Prioritária</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($fila['local']) ?></td>
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

    <!-- Incluir os modais -->
    <?php include_once 'views/modals.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/filas.js"></script>
</body>
</html>