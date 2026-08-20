<?php
$servername = '127.0.0.1';
$database = 'bd_mundo';
$username = 'root';
$password = '123mudar'; // ''
// Criar a conexão
$conexao = mysqli_connect($servername, $username, $password, $database);

if (!$conexao) {
    die('Error na conexão: ' . mysqli_connect_error());
}
//echo 'Conexão realizada com sucesso!!!'; // Mensagem de confirmação para testes
mysqli_select_db($conexao, $database);
