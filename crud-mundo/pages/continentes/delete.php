<?php
include '../../config/delete.php'; 
include '../../config/select.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Verifica se existem países associados
    $query_check = "SELECT COUNT(*) as total FROM tb_paises WHERE id_continente = $id";
    $result_check = select($query_check);
    
    if ($result_check && $result_check[0]['total'] > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Não é possível excluir este continente pois existem países associados a ele.'
        ]);
        exit;
    }
    
    $query_delete = "DELETE FROM tb_continentes WHERE id_continente = $id";
    $result = delete($query_delete);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Continente excluído com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir continente!']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>