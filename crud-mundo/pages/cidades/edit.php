<?php
include '../../include/header.php';
include '../../config/select.php';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_cidade = intval($_GET['id']);

// Busca os dados da cidade
$query_cidade = "
    SELECT c.*, p.nome as pais_nome, g.nome as governante_nome 
    FROM tb_cidades c
    LEFT JOIN tb_paises p ON c.id_pais = p.id_pais
    LEFT JOIN tb_governantes g ON c.id_governante = g.id_governante
    WHERE c.id_cidade = $id_cidade
";

$cidade = select($query_cidade);

if (!$cidade || empty($cidade)) {
    header('Location: index.php');
    exit;
}

$cidade = $cidade[0];

// Busca países para o select
$paises = select("SELECT id_pais, nome FROM tb_paises ORDER BY nome");

// Busca governantes para o select
$governantes = select("SELECT id_governante, nome FROM tb_governantes ORDER BY nome");

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/update.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : 'NULL';
    $area = floatval($_POST['area']);
    $clima = intval($_POST['clima']);
    $data_fundacao = !empty($_POST['data_fundacao']) ? "'" . addslashes($_POST['data_fundacao']) . "'" : 'NULL';
    $id_pais = intval($_POST['id_pais']);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : 'NULL';

    $query_update = "UPDATE tb_cidades SET 
        nome = '$nome',
        populacao = $populacao,
        area = $area,
        clima = $clima,
        data_fundacao = $data_fundacao,
        id_pais = $id_pais,
        id_governante = $id_governante
    WHERE id_cidade = $id_cidade";

    // Usando a função update()
    ob_start();
    update($query_update);
    $output = ob_get_clean();
    
    // Verifica se houve erro na atualização
    if (strpos($output, 'Erro na atualização') !== false) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao atualizar a cidade!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Cidade atualizada com sucesso!',
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
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Cidade</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarCidade">
            <div class="row">
                <!-- Nome da Cidade -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome da Cidade *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($cidade['nome']); ?>" required>
                </div>

                <!-- País -->
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

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $cidade['populacao'] ?: ''; ?>" min="0">
                    <small class="text-muted">Deixe em branco se não souber</small>
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $cidade['area']; ?>" required min="0">
                </div>

                <!-- Clima -->
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

                <!-- Data de Fundação -->
                <div class="col-md-6 mb-3">
                    <label for="data_fundacao" class="form-label">Data de Fundação</label>
                    <input type="date" class="form-control" id="data_fundacao" name="data_fundacao" 
                           value="<?php echo $cidade['data_fundacao'] ?? ''; ?>">
                </div>

                <!-- Governante -->
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
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formEditarCidade').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const area = document.getElementById('area').value;
    const id_pais = document.getElementById('id_pais').value;
    const clima = document.getElementById('clima').value;

    // Valida campos obrigatórios
    if (!nome || !area || !id_pais || !clima) {
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
    if (parseFloat(area) < 0) {
        e.preventDefault();
        Swal.fire({
            title: 'Valores inválidos',
            text: 'Área não pode ser negativa',
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

<?php
include '../../include/footer.php';
?>