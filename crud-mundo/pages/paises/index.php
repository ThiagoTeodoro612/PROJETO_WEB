<?php
include '../../include/header.php';

// Buscar todos os países com seus continentes e governantes
$query = "
    SELECT p.*, c.nome as continente_nome, g.nome as governante_nome,
           (SELECT COUNT(*) FROM tb_cidades WHERE id_pais = p.id_pais) as total_cidades
    FROM tb_paises p
    LEFT JOIN tb_continentes c ON p.id_continente = c.id_continente
    LEFT JOIN tb_governantes g ON p.id_governante = g.id_governante
    ORDER BY p.nome;
";

include '../../config/select.php';

$paises = select($query);
?>

<div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-flag me-2"></i>Gerenciamento de Países</h4>
        <a href="create.php" class="btn btn-light">
            <i class="fas fa-plus me-1"></i>Novo País
        </a>
    </div>
    <div class="card-body">
        <!-- Campo de pesquisa -->
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="pesquisarPaises" class="form-control" placeholder="Pesquisar país...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tabelaPaises">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Continente</th>
                        <th>População</th>
                        <th>Área (km²)</th>
                        <th>Idioma</th>
                        <th>Governante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paises as $pais) { ?>
                        <tr>
                            <td><?php echo $pais['id_pais']; ?></td>
                            <td><?php echo htmlspecialchars($pais['nome']); ?></td>
                            <td><?php echo htmlspecialchars($pais['continente_nome'] ?? 'Não definido'); ?></td>
                            <td><?php echo number_format($pais['populacao'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($pais['area'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($pais['idioma']); ?></td>
                            <td><?php echo htmlspecialchars($pais['governante_nome'] ?? 'Não definido'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $pais['id_pais']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger btn-excluir"
                                    data-id="<?php echo $pais['id_pais']; ?>"
                                    data-nome="<?php echo htmlspecialchars($pais['nome']); ?>"
                                    data-total-cidades="<?php echo $pais['total_cidades'] ?? 0; ?>">
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
    document.getElementById('pesquisarPaises').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let linhas = document.querySelectorAll('#tabelaPaises tbody tr');
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
            let totalCidades = parseInt(this.dataset.totalCidades) || 0;

            let mensagem = `Tem certeza que deseja excluir o país "${nome}"?`;
            if (totalCidades > 0) {
                mensagem += `\n\n⚠️ Atenção: Este país possui ${totalCidades} cidade(s) associada(s). A exclusão não será permitida enquanto houver cidades vinculadas.`;
            }

            Swal.fire({
                title: 'Confirmar exclusão',
                text: mensagem,
                icon: totalCidades > 0 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: totalCidades > 0 ? 'OK, entendi' : 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && totalCidades === 0) {
                    $.post('delete.php', {
                            id: id
                        })
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