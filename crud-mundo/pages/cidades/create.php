<?php
include '../../include/header.php';
include '../../config/select.php';

// Busca países para o select
$paises = select("SELECT id_pais, nome FROM tb_paises ORDER BY nome");

// Busca governantes para o select
$governantes = select("SELECT id_governante, nome FROM tb_governantes ORDER BY nome");

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/insert.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = !empty($_POST['populacao']) ? intval($_POST['populacao']) : 'NULL';
    $area = floatval($_POST['area']);
    $clima = intval($_POST['clima']);
    $data_fundacao = !empty($_POST['data_fundacao']) ? "'" . addslashes($_POST['data_fundacao']) . "'" : 'NULL';
    $id_pais = intval($_POST['id_pais']);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : 'NULL';

    $query_insert = "INSERT INTO tb_cidades (
        nome, 
        populacao, 
        area, 
        clima, 
        data_fundacao,
        id_pais, 
        id_governante
    ) VALUES (
        '$nome',
        $populacao,
        $area,
        $clima,
        $data_fundacao,
        $id_pais,
        $id_governante
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
                text: 'Erro ao cadastrar a cidade! Verifique os dados.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Cidade cadastrada com sucesso!',
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
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar Cidade</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarCidade">
            <div class="row">
                <!-- Nome da Cidade -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome da Cidade *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           placeholder="Digite o nome da cidade" required>
                </div>

                <!-- País -->
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

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           placeholder="Ex: 13960000" min="0">
                    <small class="text-muted">Deixe em branco se não souber</small>
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           placeholder="Ex: 2194.07" required min="0">
                </div>

                <!-- Clima -->
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

                <!-- Data de Fundação -->
                <div class="col-md-6 mb-3">
                    <label for="data_fundacao" class="form-label">Data de Fundação</label>
                    <input type="date" class="form-control" id="data_fundacao" name="data_fundacao">
                </div>

                <!-- Governante -->
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
                    <small class="text-muted">O governante pode ser cadastrado separadamente na tabela de governantes.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Cadastrar Cidade
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formCadastrarCidade').addEventListener('submit', function(e) {
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

    // Valida população se for preenchida
    const populacao = document.getElementById('populacao').value;
    if (populacao && parseInt(populacao) < 0) {
        e.preventDefault();
        Swal.fire({
            title: 'Valores inválidos',
            text: 'População não pode ser negativa',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Confirmação antes de salvar
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