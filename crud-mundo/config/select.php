<?php
function select(string $comando)
{
    include('database.php');
    $sql = $comando;
    $resultado = mysqli_query($conexao, $sql);

    if (!$resultado) {
        error_log("Erro na consulta: " . mysqli_error($conexao));
        return false;
    }

    $dados = [];

    while ($linha = mysqli_fetch_assoc($resultado)) {
        $dados[] = $linha;
    }

    mysqli_free_result($resultado);
    return $dados;
}
