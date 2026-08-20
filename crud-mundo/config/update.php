<?php
function update(string $comando)
{
    include('database.php');
    $sql = $comando;
    if (mysqli_query($conexao, $sql)) {
        echo 'Registro atualizado com sucesso!!!';
    } else {
        echo 'Erro na atualização do(s) registro(s): ' . mysqli_error($conexao);
    }
    mysqli_close($conexao);
}
