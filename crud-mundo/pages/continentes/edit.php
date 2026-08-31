<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_continente = intval($_GET['id']);

$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM tb_continentes WHERE id_continente = :id");
$stmt->execute([':id' => $id_continente]);
$continente = $stmt->fetch();

if (!$continente) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : 0;
    $area = floatval($_POST['area'] ?? 0);
    
    if (empty($nome) || $area <= 0) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Preencha todos os campos obrigatórios.',
                icon: 'error'
            });
        </script>";
    } else {
        try {
            $dados_antigos = $continente;
            
            $sql = "UPDATE tb_continentes SET 
                    nome = :nome,
                    populacao = :populacao,
                    area = :area
                    WHERE id_continente = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':populacao' => $populacao,
                ':area' => $area,
                ':id' => $id_continente
            ]);
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Editou continente: ' . $nome,
                'tb_continentes',
                $id_continente,
                $dados_antigos,
                ['nome' => $nome, 'populacao' => $populacao, 'area' => $area]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Continente atualizado com sucesso!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao atualizar: " . addslashes($e->getMessage()) . "',
                    icon: 'error'
                });
            </script>";
        }
    }
}
?>

<div class="card shadow">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Continente</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarContinente">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Continente *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($continente['nome']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $continente['populacao'] ?: ''; ?>" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $continente['area']; ?>" required min="0">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-info">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formEditarContinente').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const area = document.getElementById('area').value;
    
    if (!nome || !area) {
        e.preventDefault();
        Swal.fire('Atenção!', 'Preencha todos os campos com *', 'warning');
        return false;
    }
    
    e.preventDefault();
    Swal.fire({
        title: 'Confirmar alterações',
        text: 'Deseja realmente salvar as alterações?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, salvar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditarContinente').submit();
        }
    });
});
</script>

<?php require_once '../../include/footer.php'; ?>
