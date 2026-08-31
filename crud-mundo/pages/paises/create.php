<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

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
            $sql = "INSERT INTO tb_paises (nome, populacao, area, idioma, clima, regime_politico, moeda, id_continente, id_governante) 
                    VALUES (:nome, :populacao, :area, :idioma, :clima, :regime_politico, :moeda, :id_continente, :id_governante)";
            
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
                ':id_governante' => $id_governante
            ]);
            
            $id = $pdo->lastInsertId();
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Cadastrou país: ' . $nome,
                'tb_paises',
                $id,
                null,
                ['nome' => $nome, 'populacao' => $populacao, 'area' => $area, 'idioma' => $idioma]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'País cadastrado com sucesso!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao cadastrar: " . addslashes($e->getMessage()) . "',
                    icon: 'error'
                });
            </script>";
        }
    }
}
?>

<div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar País</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarPais">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do País *</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_continente" class="form-label">Continente *</label>
                    <select class="form-control" id="id_continente" name="id_continente" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($continentes as $continente) { ?>
                            <option value="<?php echo $continente['id_continente']; ?>">
                                <?php echo htmlspecialchars($continente['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População *</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma *</label>
                    <input type="text" class="form-control" id="idioma" name="idioma" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="clima" class="form-label">Clima *</label>
                    <select class="form-control" id="clima" name="clima" required>
                        <option value="">Selecione...</option>
                        <option value="0">Equatorial</option>
                        <option value="1">Tropical</option>
                        <option value="2">Temperado</option>
                        <option value="3">Polar</option>
                        <option value="4">Subequatorial</option>
                        <option value="5">Subtropical</option>
                        <option value="6">Subpolar</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="regime_politico" class="form-label">Regime Político</label>
                    <input type="text" class="form-control" id="regime_politico" name="regime_politico">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="moeda" class="form-label">Moeda</label>
                    <input type="text" class="form-control" id="moeda" name="moeda">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="id_governante" class="form-label">Governante</label>
                    <select class="form-control" id="id_governante" name="id_governante">
                        <option value="">Selecione...</option>
                        <?php foreach ($governantes as $governante) { ?>
                            <option value="<?php echo $governante['id_governante']; ?>">
                                <?php echo htmlspecialchars($governante['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formCadastrarPais').addEventListener('submit', function(e) {
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
        title: 'Confirmar cadastro',
        text: `Deseja realmente cadastrar o país "${nome}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, cadastrar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCadastrarPais').submit();
        }
    });
});
</script>

<?php require_once '../../include/footer.php'; ?>
