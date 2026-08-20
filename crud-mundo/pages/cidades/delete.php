<?php
include '../../config/delete.php'; 
include '../../config/select.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $query_delete = "DELETE FROM tb_cidades WHERE id_cidade = $id";
    $result = delete($query_delete);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Cidade excluída com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir cidade!']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>