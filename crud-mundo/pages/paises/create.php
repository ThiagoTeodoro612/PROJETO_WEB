<?php
include '../../include/header.php';
include '../../config/select.php';

// Busca continentes para o select
$continentes = select("SELECT id_continente, nome FROM tb_continentes ORDER BY nome");

// Busca governantes para o select
$governantes = select("SELECT id_governante, nome FROM tb_governantes ORDER BY nome");

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/insert.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $populacao = intval($_POST['populacao']);
    $area = floatval($_POST['area']);
    $idioma = addslashes($_POST['idioma']);
    $clima = intval($_POST['clima']);
    $regime_politico = addslashes($_POST['regime_politico'] ?? '');
    $moeda = addslashes($_POST['moeda'] ?? '');
    $id_continente = intval($_POST['id_continente']);
    $id_governante = !empty($_POST['id_governante']) ? intval($_POST['id_governante']) : 'NULL';

    $query_insert = "INSERT INTO tb_paises (
        nome, 
        populacao, 
        area, 
        idioma, 
        clima, 
        regime_politico, 
        moeda, 
        id_continente, 
        id_governante
    ) VALUES (
        '$nome',
        $populacao,
        $area,
        '$idioma',
        $clima,
        '$regime_politico',
        '$moeda',
        $id_continente,
        $id_governante
    )";

    // Usando a função insert() - ela já tem sua própria conexão e exibe mensagens
    ob_start();
    insert($query_insert);
    $output = ob_get_clean();
    
    // Verifica se houve erro na inserção
    if (strpos($output, 'Erro:') !== false) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Erro ao cadastrar o país! Verifique os dados.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'País cadastrado com sucesso!',
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
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar País</h4>
        <a href="index.php" class="btn btn-light">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarPais">
            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do País *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           placeholder="Digite o nome do país" required>
                </div>

                <!-- Continente -->
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

                <!-- População -->
                <div class="col-md-6 mb-3">
                    <label for="populacao" class="form-label">População *</label>
                    <input type="number" class="form-control" id="populacao" name="populacao" 
                           placeholder="Ex: 125700000" required min="0">
                </div>

                <!-- Área -->
                <div class="col-md-6 mb-3">
                    <label for="area" class="form-label">Área (km²) *</label>
                    <input type="number" step="0.01" class="form-control" id="area" name="area" 
                           placeholder="Ex: 377975.00" required min="0">
                </div>

                <!-- Idioma -->
                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma *</label>
                    <input type="text" class="form-control" id="idioma" name="idioma" 
                           placeholder="Ex: Japonês" required>
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

                <!-- Regime Político -->
                <div class="col-md-6 mb-3">
                    <label for="regime_politico" class="form-label">Regime Político</label>
                    <input type="text" class="form-control" id="regime_politico" name="regime_politico" 
                           placeholder="Ex: Monarquia constitucional unitária parlamentarista">
                </div>

                <!-- Moeda -->
                <div class="col-md-6 mb-3">
                    <label for="moeda" class="form-label">Moeda</label>
                    <input type="text" class="form-control" id="moeda" name="moeda" 
                           placeholder="Ex: Iene">
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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Cadastrar País
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formCadastrarPais').addEventListener('submit', function(e) {
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

    // Valida população (máximo 2.5 bilhões)
    if (parseInt(populacao) > 2500000000) {
        e.preventDefault();
        Swal.fire({
            title: 'Valor inválido',
            text: 'População não pode ultrapassar 2.5 bilhões',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Confirmação antes de salvar
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

// Máscara para formatação de população (apenas números)
document.getElementById('populacao').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Máscara para área (apenas números e ponto)
document.getElementById('area').addEventListener('input', function() {
    // Permite apenas números e ponto decimal
    this.value = this.value.replace(/[^0-9.]/g, '');
});

// Auto completar para o campo de idioma com sugestões comuns
const idiomasComuns = ['Português', 'Inglês', 'Espanhol', 'Francês', 'Alemão', 'Italiano', 'Japonês', 'Mandarim', 'Árabe', 'Russo', 'Hindi', 'Coreano'];
const inputIdioma = document.getElementById('idioma');
inputIdioma.addEventListener('input', function() {
    const valor = this.value.toLowerCase();
    if (valor.length > 1) {
        const sugestoes = idiomasComuns.filter(i => i.toLowerCase().includes(valor));
        if (sugestoes.length === 1 && sugestoes[0].toLowerCase() === valor) {
            // Não faz nada se já está completo
        }
    }
});
</script>

<?php
include '../../include/footer.php';
?>