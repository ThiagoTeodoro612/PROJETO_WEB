<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_pais = intval($_GET['id']);

$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM tb_paises WHERE id_pais = :id");
$stmt->execute([':id' => $id_pais]);
$pais = $stmt->fetch();

if (!$pais) {
    header('Location: index.php');
    exit;
}

$continentes = $pdo->query("SELECT id_continente, nome FROM tb_continentes ORDER BY nome")->fetchAll();
$governantes = $pdo->query("SELECT id_governante, nome FROM tb_governantes ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $populacao = intval($_POST['populacao'] ?? 0);
    $area = floatval($_POST['area'] ?? 0);
    $idioma = trim($_POST['idioma'] ?? '');
    $clima = intval($_POST['clima'] ?? 0);
    $regime_politico = trim($_POST['regime_politico'] ?? '');
    $moeda = trim($_POST['moeda'] ?? '');
    $id_continente = intval($_POST['id_continente'] ?? 0);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : null;
    
    if (empty($nome) || $populacao <= 0 || $area <= 0 || empty($idioma) || $id_continente <= 0) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Preencha todos os campos obrigatórios.',
                icon: 'error'
            });
        </script>";
    } else {
        try {
            $dados_antigos = $pais;
            
            $sql = "UPDATE tb_paises SET 
                    nome = :nome,
                    populacao = :populacao,
                    area = :area,
                    idioma = :idioma,
                    clima = :clima,
                    regime_politico = :regime_politico,
                    moeda = :moeda,
                    id_continente = :id_continente,
                    id_governante = :id_governante
                    WHERE id_pais = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':populacao' => $populacao,
                ':area' => $area,
                ':idioma' => $idioma,
                ':clima' => $clima,
                ':regime_politico' => $regime_politico,
                ':moeda' => $moeda,
                ':id_continente' => $id_continente,
                ':id_governante' => $id_governante,
                ':id' => $id_pais
            ]);
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Editou país: ' . $nome,
                'tb_paises',
                $id_pais,
                $dados_antigos,
                ['nome' => $nome, 'populacao' => $populacao, 'area' => $area]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'País atualizado com sucesso!',
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
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar País</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarPais">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do País *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($pais['nome']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_continente" class="form-label">Continente *</label>
                    <select class="form-control" id="id_continente" name="id_continente" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($continentes as $continente) { ?>
                            <option value="<?php echo $continente['id_continente']; ?>" 
                                <?php echo ($continente['id_continente'] == $pais['id_continente']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($continente['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População *</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $pais['populacao']; ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $pais['area']; ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma *</label>
                    <input type="text" class="form-control" id="idioma" name="idioma" 
                           value="<?php echo htmlspecialchars($pais['idioma']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="clima" class="form-label">Clima *</label>
                    <select class="form-control" id="clima" name="clima" required>
                        <option value="">Selecione...</option>
                        <option value="0" <?php echo ($pais['clima'] == '0') ? 'selected' : ''; ?>>Equatorial</option>
                        <option value="1" <?php echo ($pais['clima'] == '1') ? 'selected' : ''; ?>>Tropical</option>
                        <option value="2" <?php echo ($pais['clima'] == '2') ? 'selected' : ''; ?>>Temperado</option>
                        <option value="3" <?php echo ($pais['clima'] == '3') ? 'selected' : ''; ?>>Polar</option>
                        <option value="4" <?php echo ($pais['clima'] == '4') ? 'selected' : ''; ?>>Subequatorial</option>
                        <option value="5" <?php echo ($pais['clima'] == '5') ? 'selected' : ''; ?>>Subtropical</option>
                        <option value="6" <?php echo ($pais['clima'] == '6') ? 'selected' : ''; ?>>Subpolar</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="regime_politico" class="form-label">Regime Político</label>
                    <input type="text" class="form-control" id="regime_politico" name="regime_politico" 
                           value="<?php echo htmlspecialchars($pais['regime_politico'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="moeda" class="form-label">Moeda</label>
                    <input type="text" class="form-control" id="moeda" name="moeda" 
                           value="<?php echo htmlspecialchars($pais['moeda'] ?? ''); ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="id_governante" class="form-label">Governante</label>
                    <select class="form-control" id="id_governante" name="id_governante">
                        <option value="">Selecione...</option>
                        <?php foreach ($governantes as $governante) { ?>
                            <option value="<?php echo $governante['id_governante']; ?>" 
                                <?php echo ($governante['id_governante'] == $pais['id_governante']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($governante['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formEditarPais').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const populacao = document.getElementById('populacao').value;
    const area = document.getElementById('area').value;
    const idioma = document.getElementById('idioma').value.trim();
    const id_continente = document.getElementById('id_continente').value;
    const clima = document.getElementById('clima').value;
    
    if (!nome || !populacao || !area || !idioma || !id_continente || !clima) {
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
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, salvar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditarPais').submit();
        }
    });
});
</script>

<?php require_once '../../include/footer.php'; ?>
