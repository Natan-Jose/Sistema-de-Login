<?php
ob_start();
require("conexao.php");
session_start();

if(isset($_SESSION['user_id'])) {
    // Usuário já está logado, redireciona para a página inicial
    header("Location: index.php");
    exit();
}

$error = false;

if (isset($_POST['enviar'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validações
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $error = true;
        $uname_error = "O nome deve conter apenas letras e espaços";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Por favor, insira um endereço de e-mail válido";
    }
    if (strlen($password) < 6) {
        $error = true;
        $password_error = "A senha deve ter pelo menos 6 caracteres";
    }
    if ($password != $cpassword) {
        $error = true;
        $cpassword_error = "A senha e a confirmação de senha não coincidem";
    }

    if (!$error) {
        // Verificar se o email já está em uso
        $stmt = $conn->prepare("SELECT COUNT(*) FROM credenciais WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            $error_message = "Este endereço de e-mail já está em uso.";
        } else {
            // Continuar com o registro do novo usuário
            // Antes de inserir a senha no banco de dados, gere o hash da senha
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO credenciais(users, email, password) VALUES(:name, :email, :password)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Redirecionar para a página de login após o registro bem-sucedido
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Erro no registro. Por favor, tente novamente mais tarde!";
            }
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

    <h2>Registre-se</h2>
  
    <form method="POST" action="registration.php">

                <fieldset>
                  
                        <label for="name">Nome</label>
                        <input type="text" name="name" id="name" placeholder="Digite o nome completo" required
                            value="<?php if ($error)
                                echo $name; ?>"/>
                      
                            <?php if (isset($uname_error))
                                echo $uname_error; ?>
                        
                    
                        <label for="name">Email</label>
                        <input type="text" name="email" placeholder="Email" required
                            value="<?php if ($error)
                                echo $email; ?>"/>
                        
                            <?php if (isset($email_error))
                                echo $email_error; ?>
                        
                    
                        <label for="name">Senha</label>
                        <input type="password" name="password" placeholder="senha" required/>
                        
                            <?php if (isset($password_error))
                                echo $password_error; ?>
                        

                    
                        <label for="name">Confirmar Senha</label>
                        <input type="password" name="cpassword" placeholder="Confirma senha" required/>
                        
                            <?php if (isset($cpassword_error))
                                echo $cpassword_error; ?>
                        
                    

         
                        <input type="submit" name="enviar" value="enviar"/>
                    
                </fieldset>
            </form>

            
    <script>
      // Referencia o campo de nome
            var nomeInput = document.getElementsByName('name')[0];

            // Adiciona um listener para o evento 'input' (quando o usuário digita algo)
            nomeInput.addEventListener('input', function () {
                // Obtém o valor atual do campo de nome
                var nomeValue = nomeInput.value;

                // Remove os espaços em branco no início e no fim do valor
                var nomeTrimmed = nomeValue.trim();

                // Verifica se o nome possui pelo menos um espaço em branco, indicando que é um nome completo
                var nomeValido = nomeTrimmed.includes(' ');

                // Define a validade do campo
                nomeInput.setCustomValidity(nomeValido ? '' : 'Digite seu nome completo (Nome e Sobrenome).');
                // nomeInput.setCustomValidity(nomeValido ? '' : 'Digite seu nome completo');

                // Atualiza a aparência do campo de acordo com a validade
                nomeInput.reportValidity();
            });



</script>

                <?php if (isset($success_message)) {
                    echo $success_message;
                } ?>
           
           
                <?php if (isset($error_message)) {
                    echo $error_message;
                } ?>
        
    Já registrado? <a href="login.php">Entre aqui</a>


</body>
</html>
