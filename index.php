<?php
session_start();
require("conexao.php");

// Verifica se o usuário está logado
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepara e executa a consulta SQL
    $stmt = $conn->prepare("SELECT users FROM credenciais WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Exibe as informações do usuário logado
    if ($user) {
        $userName = $user['users'];
        $WelcomeMessage = "<strong>Bem Vindo!</strong>  Você está conectado como <strong>$userName</strong>";
        $logoutLink = "<br><br><a href='logout.php'>Sair</a>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="index.css">
    <title>Login e Registro</title>
    <script type="" src=""></script>
</head>

<body>

    <h2>Sistema de Login</h2>

    <br>
    <br>
   
      
            <?php if (isset($WelcomeMessage)) {

                echo $WelcomeMessage;
                echo $logoutLink;
                
            } else { ?>
            
                <li><a href="login.php">Login</a></li>
                <li><a href="registration.php">Registro</a></li>
            <?php } ?>
        
    

    
</body>

</html>