<?php

//inicio da conexão com PDO

$host = "localhost";
$user = "natan";
$db_password = "123123";
$db_name = "login";


try {
    $conn = new PDO("mysql:host=$host; dbname=" . $db_name, $user, $db_password);
    //echo "conectou";
} catch (PDOException $err) {
    echo "Erro conexão" . $err->getMessage();
}


?>