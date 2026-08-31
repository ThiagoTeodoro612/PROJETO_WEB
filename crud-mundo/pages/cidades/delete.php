<?php
// pages/cidades/delete.php
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM tb_cidades WHERE id_cidade = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        
        if (!$dados) {
            echo json_encode(['success' => false, 'message' => 'Cidade não encontrada!']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM tb_cidades WHERE id_cidade = :id");
        $stmt->execute([':id' => $id]);
        
        registrarLog(
            $_SESSION['usuario_id'],
            'Excluiu cidade: ' . $dados['nome'],
            'tb_cidades',
            $id,
            $dados,
            null
        );
        
        echo json_encode(['success' => true, 'message' => 'Cidade excluída com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>
