<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once 'header.php';
require_once 'sidebar.php';

// Buscar todos os locais
$stmt = $pdo->prepare("SELECT id, nome FROM locais");
$stmt->execute();
$locais = $stmt->fetchAll();

$hoje = date('Y-m-d');
$localId = isset($_GET['local_id']) ? intval($_GET['local_id']) : null;

// Senhas atendidas hoje
$query = "SELECT COUNT(*) as total FROM senhas s
          JOIN guiches g ON s.guiche_id = g.id
          WHERE DATE(s.atendimento_finalizado_em) = ?";
$params = [$hoje];
if ($localId) {
    $query .= " AND g.local_id = ?";
    $params[] = $localId;
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$senhasAtendidasHoje = $stmt->fetch()['total'];

// Senhas em espera
$query = "SELECT COUNT(*) as total FROM senhas s
          JOIN filas f ON s.fila_id = f.id
          WHERE s.status = 'aguardando'";
$params = [];
if ($localId) {
    $query .= " AND f.local_id = ?";
    $params[] = $localId;
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$senhasEmEspera = $stmt->fetch()['total'];

// Guichês ativos
$query = "SELECT COUNT(*) as total FROM guiches WHERE status_ativo = 'ativo'";
$params = [];
if ($localId) {
    $query .= " AND local_id = ?";
    $params[] = $localId;
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$guichesAtivos = $stmt->fetch()['total'];

// Guichês em uso
$query = "SELECT COUNT(*) as total FROM guiches WHERE status_uso = 'em_uso'";
$params = [];
if ($localId) {
    $query .= " AND local_id = ?";
    $params[] = $localId;
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$guichesEmUso = $stmt->fetch()['total'];

// Últimas senhas chamadas
$query = "
    SELECT s.numero, f.prefixo, f.nome as fila, g.nome as guiche, s.chamada_em 
    FROM senhas s
    JOIN filas f ON s.fila_id = f.id
    LEFT JOIN guiches g ON s.guiche_id = g.id
    WHERE s.status IN ('em_atendimento', 'atendida')";
$params = [];
if ($localId) {
    $query .= " AND f.local_id = ?";
    $params[] = $localId;
}
$query .= " ORDER BY s.chamada_em DESC LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$ultimasSenhas = $stmt->fetchAll();

// Filas ativas
$query = "SELECT f.nome, f.tipo, COUNT(s.id) as senhas_em_espera 
          FROM filas f 
          LEFT JOIN senhas s ON f.id = s.fila_id AND s.status = 'aguardando'";
$params = [];
if ($localId) {
    $query .= " WHERE f.local_id = ?";
    $params[] = $localId;
}
$query .= " GROUP BY f.id";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$filasAtivas = $stmt->fetchAll();
?>

<div class="container-fluid p-4">
    <h2 class="mb-4">
  Dashboard
  <?php
    if ($localId) {
      $locaisMap = array_column($locais, 'nome', 'id');
      echo isset($locaisMap[$localId]) ? ' - ' . $locaisMap[$localId] : '';
    }
  ?>
</h2>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="local_id" class="form-label">Filtrar por Local</label>
                <select name="local_id" id="local_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos os locais</option>
                    <?php foreach ($locais as $local): ?>
                        <option value="<?= $local['id'] ?>" <?= ($localId == $local['id']) ? 'selected' : '' ?>>
                            <?= $local['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Senhas Atendidas Hoje</h5>
                    <h2 class="card-text"><?= $senhasAtendidasHoje ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Senhas em Espera</h5>
                    <h2 class="card-text"><?= $senhasEmEspera ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Guichês Ativos</h5>
                    <h2 class="card-text"><?= $guichesAtivos ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Guichês em Uso</h5>
                    <h2 class="card-text"><?= $guichesEmUso ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Últimas Senhas Chamadas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Senha</th>
                                    <th>Fila</th>
                                    <th>Guichê</th>
                                    <th>Hora da Chamada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimasSenhas as $senha): ?>
                                <tr>
                                    <td><?= $senha['prefixo'] . str_pad($senha['numero'], 3, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= $senha['fila'] ?></td>
                                    <td><?= $senha['guiche'] ?? '--' ?></td>
                                    <td><?= $senha['chamada_em'] ? date('H:i', strtotime($senha['chamada_em'])) : '--' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Filas Ativas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Senhas em Espera</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($filasAtivas as $fila): ?>
                                <tr>
                                    <td><?= $fila['nome'] ?></td>
                                    <td>
                                        <span class="badge <?= $fila['tipo'] == 'prioritaria' ? 'bg-warning' : 'bg-primary' ?>">
                                            <?= $fila['tipo'] == 'prioritaria' ? 'Prioritária' : 'Comum' ?>
                                        </span>
                                    </td>
                                    <td><?= $fila['senhas_em_espera'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
