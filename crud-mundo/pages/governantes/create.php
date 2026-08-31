<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $partido_politico = !empty($_POST['partido_politico']) ? trim($_POST['partido_politico']) : null;
    $data_nascimento = !empty($_POST['data_nascimento']) ? $_POST['data_nascimento'] : null;
    $idade = !empty($_POST['idade']) ? intval($_POST['idade']) : null;
    $data_inicio_mandato = !empty($_POST['data_inicio_mandato']) ? $_POST['data_inicio_mandato'] : null;
    $data_fim_mandato = !empty($_POST['data_fim_mandato']) ? $_POST['data_fim_mandato'] : null;
    
    if (empty($nome)) {
        echo "<script>
            Swal.fire({
                title: 'Erro!',
                text: 'Preencha o nome do governante.',
                icon: 'error'
            });
        </script>";
    } else {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $sql = "INSERT INTO tb_governantes (nome, partido_politico, data_nascimento, idade, data_inicio_mandato, data_fim_mandato) 
                    VALUES (:nome, :partido_politico, :data_nascimento, :idade, :data_inicio_mandato, :data_fim_mandato)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':partido_politico' => $partido_politico,
                ':data_nascimento' => $data_nascimento,
                ':idade' => $idade,
                ':data_inicio_mandato' => $data_inicio_mandato,
                ':data_fim_mandato' => $data_fim_mandato
            ]);
            
            $id = $pdo->lastInsertId();
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Cadastrou governante: ' . $nome,
                'tb_governantes',
                $id,
                null,
                ['nome' => $nome]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Governante cadastrado com sucesso!',
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
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Cadastrar Governante</h4>
        <a href="index.php" class="btn btn-dark">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formCadastrarGovernante">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Governante *</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="partido_politico" class="form-label">Partido Político</label>
                    <input type="text" class="form-control" id="partido_politico" name="partido_politico">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="idade" class="form-label">Idade</label>
                    <input type="number" class="form-control" id="idade" name="idade" min="0" max="120">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_inicio_mandato" class="form-label">Data de Início do Mandato</label>
                    <input type="date" class="form-control" id="data_inicio_mandato" name="data_inicio_mandato">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_fim_mandato" class="form-label">Data de Fim do Mandato</label>
                    <input type="date" class="form-control" id="data_fim_mandato" name="data_fim_mandato">
                    <small class="text-muted">Deixe em branco se ainda estiver em exercício</small>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-warning">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formCadastrarGovernante').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const idade = document.getElementById('idade').value;
    
    if (!nome) {
        e.preventDefault();
        Swal.fire('Atenção!', 'Preencha o nome do governante', 'warning');
        return false;
    }
    
    if (idade && (parseInt(idade) < 0 || parseInt(idade) > 120)) {
        e.preventDefault();
        Swal.fire('Atenção!', 'Idade deve estar entre 0 e 120 anos', 'warning');
        return false;
    }
    
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
</script>

<?php require_once '../../include/footer.php'; ?>
