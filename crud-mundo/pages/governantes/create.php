<?php
include '../../include/header.php';
include '../../config/select.php';

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../config/insert.php';
    
    // Sanitização dos dados
    $nome = addslashes($_POST['nome']);
    $partido_politico = !empty($_POST['partido_politico']) ? "'" . addslashes($_POST['partido_politico']) . "'" : 'NULL';
    $data_nascimento = !empty($_POST['data_nascimento']) ? "'" . addslashes($_POST['data_nascimento']) . "'" : 'NULL';
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : 'NULL';
    $data_inicio_mandato = !empty($_POST['data_inicio_mandato']) ? "'" . addslashes($_POST['data_inicio_mandato']) . "'" : 'NULL';
    $data_fim_mandato = !empty($_POST['data_fim_mandato']) ? "'" . addslashes($_POST['data_fim_mandato']) . "'" : 'NULL';

    $query_insert = "INSERT INTO tb_governantes (
        nome, 
        partido_politico, 
        data_nascimento, 
        idade,
        data_inicio_mandato, 
        data_fim_mandato
    ) VALUES (
        '$nome',
        $partido_politico,
        $data_nascimento,
        $idade,
        $data_inicio_mandato,
        $data_fim_mandato
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
                text: 'Erro ao cadastrar o governante! Verifique os dados.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Governante cadastrado com sucesso!',
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
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar Governante</h4>
        <a href="index.php" class="btn btn-dark">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarGovernante">
            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Governante *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           placeholder="Digite o nome completo" required>
                </div>

                <!-- Partido Político -->
                <div class="col-md-6 mb-3">
                    <label for="partido_politico" class="form-label">Partido Político</label>
                    <input type="text" class="form-control" id="partido_politico" name="partido_politico" 
                           placeholder="Ex: Partido Liberal">
                </div>

                <!-- Data de Nascimento -->
                <div class="col-md-6 mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                </div>

                <!-- Idade -->
                <div class="col-md-6 mb-3">
                    <label for="idade" class="form-label">Idade</label>
                    <input type="number" class="form-control" id="idade" name="idade" 
                           placeholder="Ex: 69" min="0" max="120">
                    <small class="text-muted">Pode ser calculada automaticamente pela data de nascimento</small>
                </div>

                <!-- Data Início Mandato -->
                <div class="col-md-6 mb-3">
                    <label for="data_inicio_mandato" class="form-label">Data de Início do Mandato</label>
                    <input type="date" class="form-control" id="data_inicio_mandato" name="data_inicio_mandato">
                </div>

                <!-- Data Fim Mandato -->
                <div class="col-md-6 mb-3">
                    <label for="data_fim_mandato" class="form-label">Data de Fim do Mandato</label>
                    <input type="date" class="form-control" id="data_fim_mandato" name="data_fim_mandato">
                    <small class="text-muted">Deixe em branco se ainda estiver em exercício</small>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save me-1"></i>Cadastrar Governante
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário antes de enviar
document.getElementById('formCadastrarGovernante').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const idade = document.getElementById('idade').value;

    // Valida campos obrigatórios
    if (!nome) {
        e.preventDefault();
        Swal.fire({
            title: 'Campo obrigatório',
            text: 'Por favor, preencha o nome do governante',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Valida idade se for preenchida
    if (idade && (parseInt(idade) < 0 || parseInt(idade) > 120)) {
        e.preventDefault();
        Swal.fire({
            title: 'Valores inválidos',
            text: 'Idade deve estar entre 0 e 120 anos',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return false;
    }

    // Confirmação antes de salvar
    e.preventDefault();
    Swal.fire({
        title: 'Confirmar cadastro',
        text: `Deseja realmente cadastrar o governante "${nome}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, cadastrar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formCadastrarGovernante').submit();
        }
    });
});

// Calcular idade automaticamente quando data de nascimento for preenchida
document.getElementById('data_nascimento').addEventListener('change', function() {
    if (this.value) {
        const nascimento = new Date(this.value);
        const hoje = new Date();
        let idade = hoje.getFullYear() - nascimento.getFullYear();
        const mes = hoje.getMonth() - nascimento.getMonth();
        if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
            idade--;
        }
        if (idade > 0 && idade <= 120) {
            document.getElementById('idade').value = idade;
        }
    }
});

// Máscara para idade (apenas números)
document.getElementById('idade').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<?php
include '../../include/footer.php';
?>