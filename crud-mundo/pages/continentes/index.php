<?php
include '../../include/header.php';

// Buscar todos os continentes
$query = "SELECT * FROM tb_continentes ORDER BY nome;";
include '../../config/select.php';
$continentes = select($query);

// Buscar total de países por continente
foreach ($continentes as &$continente) {
    $query_paises = "SELECT COUNT(*) as total FROM tb_paises WHERE id_continente = " . $continente['id_continente'];
    $result = select($query_paises);
    $continente['total_paises'] = $result[0]['total'] ?? 0;
}
?>

<div class="card shadow">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-globe-americas me-2"></i>Gerenciamento de Continentes</h4>
        <a href="create.php" class="btn btn-light">
            <i class="fas fa-plus me-1"></i>Novo Continente
        </a>
    </div>
    <div class="card-body">
        <!-- Campo de pesquisa -->
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="pesquisarContinentes" class="form-control" placeholder="Pesquisar continente...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tabelaContinentes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>População</th>
                        <th>Área (km²)</th>
                        <th>Total de Países</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($continentes as $continente) { ?>
                        <tr>
                            <td><?php echo $continente['id_continente']; ?></td>
                            <td><?php echo htmlspecialchars($continente['nome']); ?></td>
                            <td><?php echo number_format($continente['populacao'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($continente['area'], 2, ',', '.'); ?></td>
                            <td><?php echo $continente['total_paises']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $continente['id_continente']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger btn-excluir" 
                                        data-id="<?php echo $continente['id_continente']; ?>" 
                                        data-nome="<?php echo htmlspecialchars($continente['nome']); ?>"
                                        data-total-paises="<?php echo $continente['total_paises']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php }; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Pesquisa dinâmica
    document.getElementById('pesquisarContinentes').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let linhas = document.querySelectorAll('#tabelaContinentes tbody tr');
        linhas.forEach(linha => {
            let nome = linha.querySelector('td:nth-child(2)').textContent.toLowerCase();
            linha.style.display = nome.includes(filtro) ? '' : 'none';
        });
    });

    // Exclusão com SweetAlert2
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            let nome = this.dataset.nome;
            let totalPaises = parseInt(this.dataset.totalPaises);

            let mensagem = `Tem certeza que deseja excluir o continente "${nome}"?`;
            if (totalPaises > 0) {
                mensagem += `\n\n⚠️ Atenção: Este continente possui ${totalPaises} país(es) associado(s). A exclusão não será permitida enquanto houver países vinculados.`;
            }

            Swal.fire({
                title: 'Confirmar exclusão',
                text: mensagem,
                icon: totalPaises > 0 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: totalPaises > 0 ? 'OK, entendi' : 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                showConfirmButton: totalPaises > 0 ? true : true
            }).then((result) => {
                if (result.isConfirmed && totalPaises === 0) {
                    $.post('delete.php', { id: id })
                        .done(function(response) {
                            if (response.success) {
                                Swal.fire('Excluído!', response.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Erro!', response.message, 'error');
                            }
                        })
                        .fail(function() {
                            Swal.fire('Erro!', 'Erro ao conectar ao servidor', 'error');
                        });
                }
            });
        });
    });
</script>

<?php
include '../../include/footer.php';
?>