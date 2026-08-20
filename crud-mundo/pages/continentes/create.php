<?php
include '../../include/header.php';
include '../../config/select.php';

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/insert.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : 0;
    $area = floatval($_POST['area']);

    $query_insert = "INSERT INTO tb_continentes (
        nome, 
        populacao, 
        area,
        total_paises
    ) VALUES (
        '$nome',
        $populacao,
        $area,
        0
    )";

    // Usando a função insert()
    ob_start();
    insert($query_insert);
    $output = ob_get_clean();
    
    // Verifica se houve erro na inserção
    if (strpos($output, 'Erro:') !== false) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao cadastrar o continente! Verifique os dados.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Continente cadastrado com sucesso!',
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
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar Continente</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarContinente">
            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Continente *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           placeholder="Digite o nome do continente" required>
                </div>

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           placeholder="Ex: 44579000" min="0">
                    <small class="text-muted">Deixe em branco se não souber</small>
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           placeholder="Ex: 44579000.00" required min="0">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-save me-1"></i>Cadastrar Continente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formCadastrarContinente').addEventListener('submit', function(e) {
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
        title: 'Confirmar cadastro',
        text: `Deseja realmente cadastrar o continente "${nome}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, cadastrar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCadastrarContinente').submit();
        }
    });
});

// Máscara para formatação de população (apenas números)
document.getElementById('populacao').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Máscara para área (apenas números e ponto)
document.getElementById('area').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9.]/g, '');
});
</script>

<?php
include '../../include/footer.php';
?>