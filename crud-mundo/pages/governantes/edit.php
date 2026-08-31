<?php
require_once '../../include/header.php';
require_once '../../config/database_pdo.php';
require_once '../../config/log.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_governante = intval($_GET['id']);

$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM tb_governantes WHERE id_governante = :id");
$stmt->execute([':id' => $id_governante]);
$governante = $stmt->fetch();

if (!$governante) {
    header('Location: index.php');
    exit;
}

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
            $dados_antigos = $governante;
            
            $sql = "UPDATE tb_governantes SET 
                    nome = :nome,
                    partido_politico = :partido_politico,
                    data_nascimento = :data_nascimento,
                    idade = :idade,
                    data_inicio_mandato = :data_inicio_mandato,
                    data_fim_mandato = :data_fim_mandato
                    WHERE id_governante = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':partido_politico' => $partido_politico,
                ':data_nascimento' => $data_nascimento,
                ':idade' => $idade,
                ':data_inicio_mandato' => $data_inicio_mandato,
                ':data_fim_mandato' => $data_fim_mandato,
                ':id' => $id_governante
            ]);
            
            registrarLog(
                $_SESSION['usuario_id'],
                'Editou governante: ' . $nome,
                'tb_governantes',
                $id_governante,
                $dados_antigos,
                ['nome' => $nome]
            );
            
            echo "<script>
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Governante atualizado com sucesso!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao atualizar: " . addslashes($e->getMessage()) . "',
                    icon: 'error'
                });
            </script>";
        }
    }
}
?>

<div class="card shadow">
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Governante</h4>
        <a href="index.php" class="btn btn-dark">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" id="formEditarGovernante">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Governante *</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?php echo htmlspecialchars($governante['nome']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="partido_politico" class="form-label">Partido Político</label>
                    <input type="text" class="form-control" id="partido_politico" name="partido_politico" 
                           value="<?php echo htmlspecialchars($governante['partido_politico'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" 
                           value="<?php echo $governante['data_nascimento'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="idade" class="form-label">Idade</label>
                    <input type="number" class="form-control" id="idade" name="idade" 
                           value="<?php echo $governante['idade'] ?? ''; ?>" min="0" max="120">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_inicio_mandato" class="form-label">Data de Início do Mandato</label>
                    <input type="date" class="form-control" id="data_inicio_mandato" name="data_inicio_mandato" 
                           value="<?php echo $governante['data_inicio_mandato'] ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="data_fim_mandato" class="form-label">Data de Fim do Mandato</label>
                    <input type="date" class="form-control" id="data_fim_mandato" name="data_fim_mandato" 
                           value="<?php echo $governante['data_fim_mandato'] ?? ''; ?>">
                    <small class="text-muted">Deixe em branco se ainda estiver em exercício</small>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-warning">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formEditarGovernante').addEventListener('submit', function(e) {
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
        title: 'Confirmar alterações',
        text: 'Deseja realmente salvar as alterações?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, salvar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditarGovernante').submit();
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
