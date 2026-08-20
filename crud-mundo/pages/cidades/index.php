<?php
include '../../include/header.php';

// Buscar todas as cidades com seus países e governantes
$query = "
    SELECT c.*, p.nome as pais_nome, g.nome as governante_nome,
           c.nome as cidade_nome
    FROM tb_cidades c
    LEFT JOIN tb_paises p ON c.id_pais = p.id_pais
    LEFT JOIN tb_governantes g ON c.id_governante = g.id_governante
    ORDER BY c.nome;
";

include '../../config/select.php';

$cidades = select($query);

// Buscar países para filtro
$paises = select("SELECT id_pais, nome FROM tb_paises ORDER BY nome");
?>

<div class="card shadow">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-city me-2"></i>Gerenciamento de Cidades</h4>
        <a href="create.php" class="btn btn-light">
            <i class="fas fa-plus me-1"></i>Nova Cidade
        </a>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="pesquisarCidades" class="form-control" placeholder="Pesquisar cidade...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-flag"></i></span>
                    <select id="filtroPais" class="form-control">
                        <option value="">Todos os países</option>
                        <?php foreach ($paises as $pais) { ?>
                            <option value="<?php echo $pais['id_pais']; ?>">
                                <?php echo htmlspecialchars($pais['nome']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tabelaCidades">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>País</th>
                        <th>População</th>
                        <th>Área (km²)</th>
                        <th>Clima</th>
                        <th>Governante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cidades as $cidade) { ?>
                        <tr data-pais="<?php echo $cidade['id_pais']; ?>">
                            <td><?php echo $cidade['id_cidade']; ?></td>
                            <td><?php echo htmlspecialchars($cidade['cidade_nome']); ?></td>
                            <td><?php echo htmlspecialchars($cidade['pais_nome'] ?? 'Não definido'); ?></td>
                            <td><?php echo number_format($cidade['populacao'], 0, ',', '.'); ?></td>
                            <td><?php echo number_format($cidade['area'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                $climas = [
                                    0 => 'Equatorial',
                                    1 => 'Tropical',
                                    2 => 'Temperado',
                                    3 => 'Polar',
                                    4 => 'Subequatorial',
                                    5 => 'Subtropical',
                                    6 => 'Subpolar'
                                ];
                                echo $climas[$cidade['clima']] ?? 'Não definido';
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($cidade['governante_nome'] ?? 'Não definido'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $cidade['id_cidade']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger btn-excluir" 
                                        data-id="<?php echo $cidade['id_cidade']; ?>" 
                                        data-nome="<?php echo htmlspecialchars($cidade['cidade_nome']); ?>">
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
    document.getElementById('pesquisarCidades').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let linhas = document.querySelectorAll('#tabelaCidades tbody tr');
        linhas.forEach(linha => {
            let nome = linha.querySelector('td:nth-child(2)').textContent.toLowerCase();
            linha.style.display = nome.includes(filtro) ? '' : 'none';
        });
    });

    // Filtro por país
    document.getElementById('filtroPais').addEventListener('change', function() {
        let paisId = this.value;
        let linhas = document.querySelectorAll('#tabelaCidades tbody tr');
        linhas.forEach(linha => {
            if (paisId === '' || linha.dataset.pais == paisId) {
                linha.style.display = '';
            } else {
                linha.style.display = 'none';
            }
        });
    });

    // Exclusão com SweetAlert2
    document.querySelectorAll('.btn-excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            let nome = this.dataset.nome;

            Swal.fire({
                title: 'Confirmar exclusão',
                text: `Tem certeza que deseja excluir a cidade "${nome}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
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