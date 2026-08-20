<?php
    function delete(string $comando)
    {
        include('database.php');
        $sql = $comando;
        $query = mysqli_query($conexao, $sql);
        mysqli_close($conexao);
        return $query;
    }