<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

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
            $sql = "INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) 
                    VALUES (:nome, :populacao, :area, :clima, :data_fundacao, :id_pais, :id_governante)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':populacao' => $populacao,
                ':area' => $area,
                ':clima' => $clima,
                ':data_fundacao' => $data_fundacao,
                ':id_pais' => $id_pais,
                ':id_governante' => $id_governante
            ]);
            
            $id = $pdo->lastInsertId();
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Cadastrou cidade: ' . $nome,
                'tb_cidades',
                $id,
                null,
                ['nome' => $nome, 'area' => $area, 'clima' => $clima]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Cidade cadastrada com sucesso!',
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
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar Cidade</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarCidade">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome da Cidade *</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_pais" class="form-label">País *</label>
                    <select class="form-control" id="id_pais" name="id_pais" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($paises as $pais) { ?>
                            <option value="<?php echo $pais['id_pais']; ?>">
                                <?php echo htmlspecialchars($pais['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" required min="0">
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
                    <label for="data_fundacao" class="form-label">Data de Fundação</label>
                    <input type="date" class="form-control" id="data_fundacao" name="data_fundacao">
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
                <button type="submit" class="btn btn-success">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formCadastrarCidade').addEventListener('submit', function(e) {
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
        title: 'Confirmar cadastro',
        text: `Deseja realmente cadastrar a cidade "${nome}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, cadastrar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCadastrarCidade').submit();
        }
    });
});
</script>

<?php require_once '../../include/footer.php'; ?>
