<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $pdo->query("TRUNCATE TABLE tb_logs");
        
        registrarLog($_SESSION['usuario_id'], 'Limpeza de logs realizada', 'tb_logs', null);
        
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Todos os logs foram removidos!',
                icon: 'success'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao limpar logs: " . addslashes($e->getMessage()) . "',
                icon: 'error'
            });
        </script>";
    }
}
?>

<div class="card shadow">
    <div class="card-header bg-danger text-white">
        <h4><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Limpeza de Logs</h4>
    </div>
    <div class="card-body text-center">
        <p class="fs-5">Tem certeza que deseja remover todos os registros de logs?</p>
        <p class="text-muted">Esta ação não pode ser desfeita!</p>
        <form method="POST">
            <a href="index.php" class="btn btn-secondary me-3">
                <i class="fas fa-times me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Sim, limpar todos os logs
            </button>
        </form>
    </div>
</div>

<?php require_once '../../include/footer.php'; ?>
