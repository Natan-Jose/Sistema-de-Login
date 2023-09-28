<?php
ob_start();
session_start();
require("conexao.php");

if(isset($_SESSION['user_id'])) {
    // Obtém o ID do usuário da sessão
    $userId = $_SESSION['user_id'];

    // Limpa todas as variáveis de sessão
    session_unset();

    // Destrói a sessão
    session_destroy();

    // Redireciona com uma mensagem de sucesso
    header("Location: index.php?logout=success");
} else {
    // Usuário não está logado, apenas redireciona
    header("Location: index.php");
}
?>
