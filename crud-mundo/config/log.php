<?php
require_once 'database_pdo.php';

function registrarLog($id_usuario, $acao, $tabela = null, $registro_id = null, $dados_antigos = null, $dados_novos = null) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (is_array($dados_antigos)) {
            $dados_antigos = json_encode($dados_antigos, JSON_UNESCAPED_UNICODE);
        }
        if (is_array($dados_novos)) {
            $dados_novos = json_encode($dados_novos, JSON_UNESCAPED_UNICODE);
        }
        
        $sql = "INSERT INTO tb_logs (id_usuario, acao, tabela, registro_id, dados_antigos, dados_novos, ip, user_agent) 
                VALUES (:id_usuario, :acao, :tabela, :registro_id, :dados_antigos, :dados_novos, :ip, :user_agent)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':acao' => $acao,
            ':tabela' => $tabela,
            ':registro_id' => $registro_id,
            ':dados_antigos' => $dados_antigos,
            ':dados_novos' => $dados_novos,
            ':ip' => $ip,
            ':user_agent' => $user_agent
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao registrar log: " . $e->getMessage());
        return false;
    }
}

function listarLogs($limite = 100) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM tb_logs l
                LEFT JOIN tb_usuarios u ON l.id_usuario = u.id_usuario
                ORDER BY l.data_hora DESC
                LIMIT :limite";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao listar logs: " . $e->getMessage());
        return [];
    }
}
?>
