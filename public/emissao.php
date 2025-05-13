<?php
session_start();
include_once __DIR__ . '/../includes/conexao.php';

$sql = "SELECT * FROM filas ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$senhaFormatada = null;
$nomeFila = null;
$dataHora = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fila_id'])) {
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
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Emissão de Senhas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .cupom {
            width: 300px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            text-align: center;
            border: 1px dashed #000;
            font-family: monospace;
        }

        .cupom h2 {
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .cupom .senha {
            font-size: 3em;
            font-weight: bold;
            margin: 20px 0;
        }

        .cupom .fila {
            font-size: 1.1em;
        }

        .cupom .data {
            font-size: 0.9em;
            margin-top: 10px;
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
    <div class="container mt-5">
        <h2 class="mb-4">Emissão de Senhas</h2>

        <?php if ($senhaFormatada): ?>
            <div class="cupom">
                <h2>Posto de Atendimento</h2>
                <div class="fila">Fila: <strong><?= htmlspecialchars($nomeFila) ?></strong></div>
                <div class="senha"><?= $senhaFormatada ?></div>
                <div class="data">Emitida em: <?= $dataHora ?></div>
            </div>

            <div class="text-center mt-4 no-print">
                <a href="emissao.php" class="btn btn-primary">Emitir outra senha</a>
                
            </div>

            <script>
                window.onload = function () {
                    setTimeout(() => {
                        window.print();
                    }, 500);
                };
            </script>
        <?php else: ?>
            <div class="row">
                <?php foreach ($filas as $fila): ?>
                    <div class="col-md-4 mb-3">
                        <form method="POST">
                            <input type="hidden" name="fila_id" value="<?= $fila['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Emitir senha - <?= htmlspecialchars($fila['nome']) ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <a href="index.php" class="btn btn-secondary mt-4">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>
