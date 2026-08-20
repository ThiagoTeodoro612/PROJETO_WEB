<?php
include '../../include/header.php';

// Buscar todos os governantes
$query = "SELECT * FROM tb_governantes ORDER BY nome;";
include '../../config/select.php';
$governantes = select($query);

// Buscar informações adicionais para cada governante
foreach ($governantes as &$governante) {
    // Contar países onde é governante
    $query_paises = "SELECT COUNT(*) as total FROM tb_paises WHERE id_governante = " . $governante['id_governante'];
    $result = select($query_paises);
    $governante['total_paises'] = $result[0]['total'] ?? 0;
    
    // Contar cidades onde é governante
    $query_cidades = "SELECT COUNT(*) as total FROM tb_cidades WHERE id_governante = " . $governante['id_governante'];
    $result = select($query_cidades);
    $governante['total_cidades'] = $result[0]['total'] ?? 0;
}
?>

<div class="card shadow">
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Gerenciamento de Governantes</h4>
        <a href="create.php" class="btn btn-dark">
            <i class="fas fa-plus me-1"></i>Novo Governante
        </a>
    </div>
    <div class="card-body">
        <!-- Campo de pesquisa -->
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="pesquisarGovernantes" class="form-control" placeholder="Pesquisar governante...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tabelaGovernantes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Partido Político</th>
                        <th>Idade</th>
                        <th>Início Mandato</th>
                        <th>Fim Mandato</th>
                        <th>Países</th>
                        <th>Cidades</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($governantes as $governante) { ?>
                        <tr>
                            <td><?php echo $governante['id_governante']; ?></td>
                            <td><?php echo htmlspecialchars($governante['nome']); ?></td>
                            <td><?php echo htmlspecialchars($governante['partido_politico'] ?? 'Não definido'); ?></td>
                            <td><?php echo $governante['idade'] ?? 'N/A'; ?></td>
                            <td><?php echo $governante['data_inicio_mandato'] ? date('d/m/Y', strtotime($governante['data_inicio_mandato'])) : 'N/A'; ?></td>
                            <td><?php echo $governante['data_fim_mandato'] ? date('d/m/Y', strtotime($governante['data_fim_mandato'])) : 'Em exercício'; ?></td>
                            <td><?php echo $governante['total_paises']; ?></td>
                            <td><?php echo $governante['total_cidades']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $governante['id_governante']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger btn-excluir" 
                                        data-id="<?php echo $governante['id_governante']; ?>" 
                                        data-nome="<?php echo htmlspecialchars($governante['nome']); ?>"
                                        data-total="<?php echo $governante['total_paises'] + $governante['total_cidades']; ?>">
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
    document.getElementById('pesquisarGovernantes').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let linhas = document.querySelectorAll('#tabelaGovernantes tbody tr');
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
            let total = parseInt(this.dataset.total);

            let mensagem = `Tem certeza que deseja excluir o governante "${nome}"?`;
            if (total > 0) {
                mensagem += `\n\n⚠️ Atenção: Este governante está associado a ${total} registro(s) (países/cidades). A exclusão não será permitida enquanto houver associações.`;
            }

            Swal.fire({
                title: 'Confirmar exclusão',
                text: mensagem,
                icon: total > 0 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: total > 0 ? 'OK, entendi' : 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && total === 0) {
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