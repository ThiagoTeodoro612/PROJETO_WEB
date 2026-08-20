<?php
include '../../config/delete.php'; 
include '../../config/select.php';

// DEBUG - ver o que está chegando
error_log('POST recebido: ' . print_r($_POST, true));

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    error_log('ID processado: ' . $id);  // DEBUG
    

    $query_select = select("SELECT * FROM tb_cidades WHERE id_pais = $id");
    if($query_select) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'A exclusão das cidade devem ser feitas antes da exclusão de seu país!']);
    } else {
        $query_delete = "DELETE FROM tb_paises WHERE id_pais = $id";  // ou id, dependendo da sua tabela
        
        $result = delete($query_delete);
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'País excluído com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir país!']);
        }
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID não fornecido! POST: ' . print_r($_POST, true)]);
}
exit;
?>