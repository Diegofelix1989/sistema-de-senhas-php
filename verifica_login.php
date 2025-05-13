<?php
session_start();
include 'includes/conexao.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario'] = $usuario;
    if ($usuario['tipo'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: atendente/index.php");
    }
} else {
    echo "<script>alert('Credenciais inválidas'); window.location.href='index.php';</script>";
}
?>
