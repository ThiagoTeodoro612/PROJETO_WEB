<?php
include '../../include/header.php';
include '../../config/select.php';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_pais = intval($_GET['id']);

// Busca os dados do país
$query_pais = "
    SELECT p.*, c.nome as continente_nome, g.nome as governante_nome 
    FROM tb_paises p
    LEFT JOIN tb_continentes c ON p.id_continente = c.id_continente
    LEFT JOIN tb_governantes g ON p.id_governante = g.id_governante
    WHERE p.id_pais = $id_pais
";

$pais = select($query_pais);

if (!$pais || empty($pais)) {
    header('Location: index.php');
    exit;
}

$pais = $pais[0];

// Busca continentes para o select
$continentes = select("SELECT id_continente, nome FROM tb_continentes ORDER BY nome");

// Busca governantes para o select
$governantes = select("SELECT id_governante, nome FROM tb_governantes ORDER BY nome");

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/update.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = intval($_POST['populacao']);
    $area = floatval($_POST['area']);
    $idioma = addslashes($_POST['idioma']);
    $clima = intval($_POST['clima']);
    $regime_politico = addslashes($_POST['regime_politico']);
    $moeda = addslashes($_POST['moeda']);
    $id_continente = intval($_POST['id_continente']);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : 'NULL';

    $query_update = "UPDATE tb_paises SET 
        nome = '$nome',
        populacao = $populacao,
        area = $area,
        idioma = '$idioma',
        clima = $clima,
        regime_politico = '$regime_politico',
        moeda = '$moeda',
        id_continente = $id_continente,
        id_governante = $id_governante
    WHERE id_pais = $id_pais";

    // Usando a função update() - ela já tem sua própria conexão
    ob_start();
    update($query_update);
    $output = ob_get_clean();
    
    // Verifica se houve erro na atualização
    if (strpos($output, 'Erro na atualização') !== false) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao atualizar o país!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'País atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
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
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do País *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($pais['nome']); ?>" required>
                </div>

                <!-- Continente -->
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

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População *</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $pais['populacao']; ?>" required min="0">
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $pais['area']; ?>" required min="0">
                </div>

                <!-- Idioma -->
                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma *</label>
                    <input type="text" class="form-control" id="idioma" name="idioma" 
                           value="<?php echo htmlspecialchars($pais['idioma']); ?>" required>
                </div>

                <!-- Clima -->
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

                <!-- Regime Político -->
                <div class="col-md-6 mb-3">
                    <label for="regime_politico" class="form-label">Regime Político</label>
                    <input type="text" class="form-control" id="regime_politico" name="regime_politico" 
                           value="<?php echo htmlspecialchars($pais['regime_politico'] ?? ''); ?>">
                </div>

                <!-- Moeda -->
                <div class="col-md-6 mb-3">
                    <label for="moeda" class="form-label">Moeda</label>
                    <input type="text" class="form-control" id="moeda" name="moeda" 
                           value="<?php echo htmlspecialchars($pais['moeda'] ?? ''); ?>">
                </div>

                <!-- Governante -->
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
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formEditarPais').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const populacao = document.getElementById('populacao').value;
    const area = document.getElementById('area').value;
    const idioma = document.getElementById('idioma').value.trim();
    const id_continente = document.getElementById('id_continente').value;
    const clima = document.getElementById('clima').value;

    // Valida campos obrigatórios
    if (!nome || !populacao || !area || !idioma || !id_continente || !clima) {
        e.preventDefault();
        Swal.fire({
            title: 'Campos obrigatórios',
            text: 'Por favor, preencha todos os campos com *',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Valida valores numéricos
    if (parseInt(populacao) < 0 || parseFloat(area) < 0) {
        e.preventDefault();
        Swal.fire({
            title: 'Valores inválidos',
            text: 'População e área não podem ser negativas',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Confirmação antes de salvar
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

<?php
include '../../include/footer.php';
?>