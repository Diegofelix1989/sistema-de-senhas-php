<?php
session_start();
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se um local foi selecionado
if (!isset($_SESSION['local_selecionado'])) {
    header('Location: selecao_locais.php');
    exit;
}

$local_id = $_SESSION['local_selecionado'];

// Obter informações do local
$sqlLocal = "SELECT nome FROM locais WHERE id = :local_id";
$stmtLocal = $pdo->prepare($sqlLocal);
$stmtLocal->execute(['local_id' => $local_id]);
$localNome = $stmtLocal->fetchColumn();

// Buscar filas disponíveis para o local selecionado e filtradas pelos locais selecionados
$sql = "SELECT f.* FROM filas f 
        INNER JOIN locais l ON f.local_id = l.id 
        WHERE f.local_id = :local_id 
        ORDER BY f.nome";
$stmt = $pdo->prepare($sql);
$stmt->execute(['local_id' => $local_id]);
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar outros locais disponíveis baseados no filtro da sessão
$sqlOutrosLocais = "SELECT * FROM locais WHERE id != :local_id ORDER BY nome";
$stmtOutrosLocais = $pdo->prepare($sqlOutrosLocais);
$stmtOutrosLocais->execute(['local_id' => $local_id]);
$outrosLocais = $stmtOutrosLocais->fetchAll(PDO::FETCH_ASSOC);

// Filtrar apenas os locais que estão no filtro da sessão
$locaisFiltrados = array_filter($outrosLocais, function($local) {
    return isset($_SESSION['filtro_locais'][$local['id']]) && $_SESSION['filtro_locais'][$local['id']] === true;
});

$senhaFormatada = null;
$nomeFila = null;
$dataHora = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fila_id'])) {
        $fila_id = (int)$_POST['fila_id'];

        $sqlUltima = "SELECT MAX(numero) AS ultima FROM senhas WHERE fila_id = :fila_id";
        $stmtUltima = $pdo->prepare($sqlUltima);
        $stmtUltima->execute(['fila_id' => $fila_id]);
        $ultima = $stmtUltima->fetch()['ultima'] ?? 0;

        $novaSenha = $ultima + 1;

        $sqlInsere = "INSERT INTO senhas (numero, fila_id) VALUES (:numero, :fila_id)";
        $stmtInsere = $pdo->prepare($sqlInsere);
        $stmtInsere->execute([
            'numero' => $novaSenha,
            'fila_id' => $fila_id
        ]);

        $sqlNomeFila = "SELECT nome FROM filas WHERE id = :fila_id";
        $stmtNomeFila = $pdo->prepare($sqlNomeFila);
        $stmtNomeFila->execute(['fila_id' => $fila_id]);
        $nomeFila = $stmtNomeFila->fetchColumn();

        $sigla = strtoupper(substr($nomeFila, 0, 1));
        $senhaFormatada = $sigla . str_pad($novaSenha, 3, '0', STR_PAD_LEFT);
        $dataHora = date('d/m/Y H:i:s');
    } elseif (isset($_POST['trocar_local']) && isset($_POST['local_selecionado'])) {
        // Trocar para outro local disponível
        $_SESSION['local_selecionado'] = (int)$_POST['local_selecionado'];
        header('Location: emissao.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissão de Senhas - Sistema de Gerenciamento de Filas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cupom {
            width: 300px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            text-align: center;
            border: 1px dashed #000;
            font-family: 'Courier New', monospace;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .cupom h2 {
            margin-bottom: 10px;
            font-size: 1.4em;
            font-weight: bold;
        }
        .cupom .senha {
            font-size: 3.5em;
            font-weight: bold;
            margin: 20px 0;
            color: #0d6efd;
        }
        .cupom .fila {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .cupom .data {
            font-size: 0.9em;
            margin-top: 20px;
            color: #6c757d;
        }
        .cupom hr {
            border-top: 1px dashed #000;
            margin: 15px 0;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        .fila-btn {
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .fila-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .fila-comum {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .fila-prioritaria {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-voltar {
            margin-top: 20px;
        }
        .local-selector {
            margin-bottom: 20px;
        }
        .local-badge {
            background-color: #e9ecef;
            color: #495057;
            font-weight: normal;
            cursor: pointer;
            transition: all 0.2s;
        }
        .local-badge:hover {
            background-color: #ced4da;
        }
        .local-badge.active {
            background-color: #0d6efd;
            color: white;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .cupom, .cupom * {
                visibility: visible;
            }
            .cupom {
                position: absolute;
                top: 20px;
                left: 0;
                right: 0;
                margin: auto;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1><i class="bi bi-ticket-perforated"></i> Emissão de Senhas</h1>
        <p class="lead">Local: <?= htmlspecialchars($localNome) ?></p>
    </div>

    <div class="main-container container">
        <?php if ($senhaFormatada): ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="cupom">
                        <h2><?= htmlspecialchars($localNome) ?></h2>
                        <hr>
                        <div class="fila">Fila: <strong><?= htmlspecialchars($nomeFila) ?></strong></div>
                        <div class="senha"><?= $senhaFormatada ?></div>
                        <hr>
                        <div class="data">Emitida em: <?= $dataHora ?></div>
                    </div>

                    <div class="text-center mt-4 no-print">
                        <a href="emissao.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Emitir outra senha
                        </a>
                        <a href="selecao_locais.php" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-building"></i> Trocar local
                        </a>
                        <button onclick="window.print()" class="btn btn-success ms-2">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>

            <script>
                window.onload = function () {
                    setTimeout(() => {
                        window.print();
                    }, 500);
                };
            </script>
        <?php else: ?>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <?php if (!empty($locaisFiltrados)): ?>
                        <div class="local-selector card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bi bi-buildings"></i> Alternar entre locais selecionados</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge local-badge active"><?= htmlspecialchars($localNome) ?></span>
                                    
                                    <?php foreach ($locaisFiltrados as $outroLocal): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="local_selecionado" value="<?= $outroLocal['id'] ?>">
                                        <button type="submit" name="trocar_local" class="badge local-badge border-0">
                                            <?= htmlspecialchars($outroLocal['nome']) ?>
                                        </button>
                                    </form>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h3 class="mb-0"><i class="bi bi-list-ul"></i> Selecione a Fila</h3>
                            </div>
                            <div class="card-body">
                                <?php if (empty($filas)): ?>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Não há filas cadastradas para este local.
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($filas as $fila): ?>
                                            <div class="col-md-6">
                                                <form method="POST">
                                                    <input type="hidden" name="fila_id" value="<?= $fila['id'] ?>">
                                                    <button type="submit" class="btn btn-lg w-100 fila-btn <?= $fila['tipo'] === 'prioritaria' ? 'fila-prioritaria' : 'fila-comum' ?>">
                                                        <?php if ($fila['tipo'] === 'prioritaria'): ?>
                                                            <i class="bi bi-star-fill me-2"></i>
                                                        <?php else: ?>
                                                            <i class="bi bi-people-fill me-2"></i>
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($fila['nome']) ?>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="selecao_locais.php" class="btn btn-secondary btn-voltar">
                                <i class="bi bi-arrow-left"></i> Voltar para Seleção de Locais
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>