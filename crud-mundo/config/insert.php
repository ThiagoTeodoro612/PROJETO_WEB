<?php
function insert(string $comando)
{
    include('database.php');
    $sql = $comando;
    if (mysqli_query($conexao, $sql)) {
        echo 'Registro inserido com sucesso!!!';
    } else {
        echo 'Erro: ' . $sql . '<br>' . mysqli_error($conexao);
    }
    mysqli_close($conexao);
}
