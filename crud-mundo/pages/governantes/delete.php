<?php
include '../../config/delete.php'; 
include '../../config/select.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Verifica se existem associações
    $query_check_paises = "SELECT COUNT(*) as total FROM tb_paises WHERE id_governante = $id";
    $result_paises = select($query_check_paises);
    
    $query_check_cidades = "SELECT COUNT(*) as total FROM tb_cidades WHERE id_governante = $id";
    $result_cidades = select($query_check_cidades);
    
    $total_associacoes = ($result_paises[0]['total'] ?? 0) + ($result_cidades[0]['total'] ?? 0);
    
    if ($total_associacoes > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Não é possível excluir este governante pois existem ' . $total_associacoes . ' associação(ões) (países/cidades) vinculadas a ele.'
        ]);
        exit;
    }
    
    $query_delete = "DELETE FROM tb_governantes WHERE id_governante = $id";
    $result = delete($query_delete);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Governante excluído com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir governante!']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>