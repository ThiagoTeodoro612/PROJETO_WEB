<?php
include '../../include/header.php';
include '../../config/select.php';

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_continente = intval($_GET['id']);

// Busca os dados do continente
$query_continente = "SELECT * FROM tb_continentes WHERE id_continente = $id_continente";
$continente = select($query_continente);

if (!$continente || empty($continente)) {
    header('Location: index.php');
    exit;
}

$continente = $continente[0];

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/update.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : 0;
    $area = floatval($_POST['area']);

    $query_update = "UPDATE tb_continentes SET 
        nome = '$nome',
        populacao = $populacao,
        area = $area
    WHERE id_continente = $id_continente";

    // Usando a função update()
    ob_start();
    update($query_update);
    $output = ob_get_clean();
    
    // Verifica se houve erro na atualização
    if (strpos($output, 'Erro na atualização') !== false) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao atualizar o continente!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Continente atualizado com sucesso!',
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
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Continente</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarContinente">
            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Continente *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($continente['nome']); ?>" required>
                </div>

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           value="<?php echo $continente['populacao'] ?: ''; ?>" min="0">
                    <small class="text-muted">Deixe em branco se não souber</small>
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           value="<?php echo $continente['area']; ?>" required min="0">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formEditarContinente').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const area = document.getElementById('area').value;

    // Valida campos obrigatórios
    if (!nome || !area) {
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

<?php
include '../../include/footer.php';
?>