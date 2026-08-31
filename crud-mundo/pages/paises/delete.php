<?php
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM tb_paises WHERE id_pais = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        
        if (!$dados) {
            echo json_encode(['success' => false, 'message' => 'País não encontrado!']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tb_cidades WHERE id_pais = :id");
        $stmt->execute([':id' => $id]);
        $total = $stmt->fetch();
        
        if ($total['total'] > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Não é possível excluir! Existem ' . $total['total'] . ' cidades associadas.'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM tb_paises WHERE id_pais = :id");
        $stmt->execute([':id' => $id]);
        
        registrarLog(
            $_SESSION['usuario_id'],
            'Excluiu país: ' . $dados['nome'],
            'tb_paises',
            $id,
            $dados,
            null
        );
        
        echo json_encode(['success' => true, 'message' => 'País excluído com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>
