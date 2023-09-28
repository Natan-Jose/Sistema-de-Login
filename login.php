<?php

ob_start();
require("conexao.php");
session_start();

// Defina o número máximo de tentativas de login malsucedidas permitidas antes do bloqueio
$maxFailedAttempts = 3; // 
$blockDurationDays = 30; // Duração do bloqueio em dias



if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
}

if (isset($_POST['enviar'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Limpe e valide o endereço de e-mail
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Verifique se o endereço de e-mail é válido após a limpeza
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Endereço de e-mail inválido.";
    } else {

        // Verifique se a conta está bloqueada
        $stmt = $conn->prepare("SELECT id, users, password, blocked, block_expiration, login_attempts FROM credenciais WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifique se a conta está bloqueada
            if ($user['blocked'] && strtotime($user['block_expiration']) > time()) {
                // A conta está bloqueada
                $error_message = "Sua conta está temporariamente bloqueada. Tente novamente mais tarde.";
            } else {
                // Verifique a senha usando password_verify
                if (password_verify($password, $user['password'])) {
                    // A senha está correta
                    // Faça o login do usuário
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['users'];

                    // Zere o contador de tentativas de login malsucedidas
                    $stmt = $conn->prepare("UPDATE credenciais SET login_attempts = 0 WHERE id = :id");
                    $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // A senha está incorreta, registre a tentativa de login malsucedida
                    $stmt = $conn->prepare("UPDATE credenciais SET login_attempts = login_attempts + 1 WHERE id = :id");
                    $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                    $stmt->execute();

                    if ($user['login_attempts'] >= $maxFailedAttempts) {
                        // Bloqueie a conta por 30 dias
                        $blockExpiration = date('Y-m-d H:i:s', strtotime("+$blockDurationDays days"));
                        $stmt = $conn->prepare("UPDATE credenciais SET blocked = 1, block_expiration = :block_expiration WHERE id = :id");
                        $stmt->bindParam(':block_expiration', $blockExpiration, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $error_message = "Credenciais inválidas. Sua conta foi temporariamente bloqueada devido a várias tentativas de login mal sucedidas.";

                        //$error_message = "Sua conta foi temporariamente bloqueada devido a várias tentativas de login malsucedidas. Tente novamente em " . date('Y-m-d H:i:s', strtotime($blockExpiration)) . ".";
                    } else {
                        $error_message = "Credenciais inválidas.";
                    }
                }
            }
        } else {
            $error_message = "Usuário não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="login.css">
    <title>Login e Registro</title>
    <script type="" src=""></script>
</head>

<body>

    <h2>Login</h2>


    <form method="POST" action="login.php">
        
        <fieldset>

            <label for="name">Email</label>
            <input type="text" name="email" placeholder="Seu Email" required />


            <label for="name">Senha</label>
            <input type="password" name="password" placeholder="Sua Senha" required />

            <input type="submit" name="enviar" value="enviar"/>

        </fieldset>
    </form>
    
    <?php if (isset($error_message)) { echo $error_message; } ?>

    Novo usuário? <a href="registration.php">Clique aqui</a>
     
</body>

</html>