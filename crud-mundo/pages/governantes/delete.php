<?php
// pages/governantes/delete.php
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM tb_governantes WHERE id_governante = :id");
        $stmt->execute([':id' => $id]);
        $dados = $stmt->fetch();
        
        if (!$dados) {
            echo json_encode(['success' => false, 'message' => 'Governante não encontrado!']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tb_paises WHERE id_governante = :id");
        $stmt->execute([':id' => $id]);
        $total_paises = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tb_cidades WHERE id_governante = :id");
        $stmt->execute([':id' => $id]);
        $total_cidades = $stmt->fetch();
        
        $total = $total_paises['total'] + $total_cidades['total'];
        
        if ($total > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Não é possível excluir! Existem ' . $total . ' associações (países/cidades).'
            ]);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM tb_governantes WHERE id_governante = :id");
        $stmt->execute([':id' => $id]);
        
        registrarLog(
            $_SESSION['usuario_id'],
            'Excluiu governante: ' . $dados['nome'],
            'tb_governantes',
            $id,
            $dados,
            null
        );
        
        echo json_encode(['success' => true, 'message' => 'Governante excluído com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido!']);
}
exit;
?>
