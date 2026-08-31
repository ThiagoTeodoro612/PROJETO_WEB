<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_cidade = intval($_GET['id']);

$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM tb_cidades WHERE id_cidade = :id");
$stmt->execute([':id' => $id_cidade]);
$cidade = $stmt->fetch();

if (!$cidade) {
    header('Location: index.php');
    exit;
}

$paises = $pdo->query("SELECT id_pais, nome FROM tb_paises ORDER BY nome")->fetchAll();
$governantes = $pdo->query("SELECT id_governante, nome FROM tb_governantes ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : null;
    $area = floatval($_POST['area'] ?? 0);
    $clima = intval($_POST['clima'] ?? 0);
    $data_fundacao = !empty($_POST['data_fundacao']) ? $_POST['data_fundacao'] : null;
    $id_pais = intval($_POST['id_pais'] ?? 0);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : null;
    
    if (empty($nome) || $area <= 0 || $id_pais <= 0) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Preencha todos os campos obrigatórios.',
                icon: 'error'
            });
        </script>";
    } else {
        try {
            $dados_antigos = $cidade;
            
            $sql = "UPDATE tb_cidades SET 
                    nome = :nome,
                    populacao = :populacao,
                    area = :area,
                    clima = :clima,
                    data_fundacao = :data_fundacao,
                    id_pais = :id_pais,
                    id_governante = :id_governante
                    WHERE id_cidade = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':populacao' => $populacao,
                ':area' => $area,
                ':clima' => $clima,
                ':data_fundacao' => $data_fundacao,
                ':id_pais' => $id_pais,
                ':id_governante' => $id_governante,
                ':id' => $id_cidade
            ]);
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Editou cidade: ' . $nome,
                'tb_cidades',
                $id_cidade,
                $dados_antigos,
                ['nome' => $nome, 'area' => $area, 'clima' => $clima]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Cidade atualizada com sucesso!',
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
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Cidade</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarCidade">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome da Cidade *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($cidade['nome']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_pais" class="form-label">País *</label>
                    <select class="form-control" id="id_pais" name="id_pais" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($paises as $pais) { ?>
                            <option value="<?php echo $pais['id_pais']; ?>" 
                                <?php echo ($pais['id_pais'] == $cidade['id_pais']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pais['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $cidade['populacao'] ?: ''; ?>" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $cidade['area']; ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="clima" class="form-label">Clima *</label>
                    <select class="form-control" id="clima" name="clima" required>
                        <option value="">Selecione...</option>
                        <option value="0" <?php echo ($cidade['clima'] == '0') ? 'selected' : ''; ?>>Equatorial</option>
                        <option value="1" <?php echo ($cidade['clima'] == '1') ? 'selected' : ''; ?>>Tropical</option>
                        <option value="2" <?php echo ($cidade['clima'] == '2') ? 'selected' : ''; ?>>Temperado</option>
                        <option value="3" <?php echo ($cidade['clima'] == '3') ? 'selected' : ''; ?>>Polar</option>
                        <option value="4" <?php echo ($cidade['clima'] == '4') ? 'selected' : ''; ?>>Subequatorial</option>
                        <option value="5" <?php echo ($cidade['clima'] == '5') ? 'selected' : ''; ?>>Subtropical</option>
                        <option value="6" <?php echo ($cidade['clima'] == '6') ? 'selected' : ''; ?>>Subpolar</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_fundacao" class="form-label">Data de Fundação</label>
                    <input type="date" class="form-control" id="data_fundacao" name="data_fundacao" 
                           value="<?php echo $cidade['data_fundacao'] ?? ''; ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="id_governante" class="form-label">Governante</label>
                    <select class="form-control" id="id_governante" name="id_governante">
                        <option value="">Selecione...</option>
                        <?php foreach ($governantes as $governante) { ?>
                            <option value="<?php echo $governante['id_governante']; ?>" 
                                <?php echo ($governante['id_governante'] == $cidade['id_governante']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($governante['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formEditarCidade').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const area = document.getElementById('area').value;
    const id_pais = document.getElementById('id_pais').value;
    const clima = document.getElementById('clima').value;
    
    if (!nome || !area || !id_pais || !clima) {
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
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, salvar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditarCidade').submit();
        }
    });
});
</script>

<?php require_once '../../include/footer.php'; ?>
