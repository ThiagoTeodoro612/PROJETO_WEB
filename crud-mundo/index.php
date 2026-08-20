<?php
include 'include/header.php'
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h2 class="mb-0"><i class="fas fa-globe-americas me-2"></i>Dashboard - CRUD Mundo</h2>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-flag fa-3x mb-3"></i>
                                <h4>Países</h4>
                                <a href="/crud-mundo/pages/paises/index.php" class="btn btn-light">Gerenciar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-city fa-3x mb-3"></i>
                                <h4>Cidades</h4>
                                <a href="/crud-mundo/pages/cidades/index.php" class="btn btn-light">Gerenciar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-globe fa-3x mb-3"></i>
                                <h4>Continentes</h4>
                                <a href="/crud-mundo/pages/continentes/index.php" class="btn btn-light">Gerenciar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tie fa-3x mb-3"></i>
                                <h4>Governantes</h4>
                                <a href="/crud-mundo/pages/governantes/index.php" class="btn btn-light">Gerenciar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // Estatísticas
                include 'config/select.php';

                $stats = [];
                $queries = [
                    'total_paises' => "SELECT COUNT(*) as total FROM tb_paises",
                    'total_cidades' => "SELECT COUNT(*) as total FROM tb_cidades",
                    'total_continentes' => "SELECT COUNT(*) as total FROM tb_continentes",
                    'total_governantes' => "SELECT COUNT(*) as total FROM tb_governantes"
                ];

                foreach ($queries as $key => $sql) {
                    $stats[$key] = select($sql)[0]['total'];
                };
                ?>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estatísticas Gerais</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h3><?php echo $stats['total_paises']; ?></h3>
                                        <p>Países Cadastrados</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3><?php echo $stats['total_cidades']; ?></h3>
                                        <p>Cidades Cadastradas</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3><?php echo $stats['total_continentes']; ?></h3>
                                        <p>Continentes Cadastrados</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3><?php echo $stats['total_governantes']; ?></h3>
                                        <p>Governantes Cadastrados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'include/footer.php'
?>