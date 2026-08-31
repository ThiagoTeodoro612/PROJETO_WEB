<?php
require_once '../../include/header.php';
require_once '../../config/log.php';

$logs = listarLogs(100);
?>

<div class="card shadow">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-history me-2"></i>Logs do Sistema</h4>
        <div>
            <button onclick="location.reload()" class="btn btn-light me-2">
                <i class="fas fa-sync me-1"></i>Atualizar
            </button>
            <button onclick="window.location.href='limpar.php'" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Limpar Logs
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Tabela</th>
                        <th>Registro</th>
                        <th>Data/Hora</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum log registrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['id_log']; ?></td>
                                <td><?php echo htmlspecialchars($log['usuario_nome'] ?? 'Sistema'); ?></td>
                                <td><?php echo htmlspecialchars($log['acao']); ?></td>
                                <td><?php echo htmlspecialchars($log['tabela'] ?? '-'); ?></td>
                                <td><?php echo $log['registro_id'] ?? '-'; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($log['data_hora'])); ?></td>
                                <td><?php echo htmlspecialchars($log['ip']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../include/footer.php'; ?>
