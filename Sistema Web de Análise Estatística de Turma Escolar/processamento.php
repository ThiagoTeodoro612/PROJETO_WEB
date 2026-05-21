<?php
// Funções próprias para cálculos
function calcularMedia($nota1, $nota2, $trabalho) {
    return ($nota1 + $nota2 + $trabalho) / 3;
}

function calcularRaizQuadradaSoma($nota1, $nota2, $trabalho) {
    $soma = $nota1 + $nota2 + $trabalho;
    return sqrt($soma);
}

function calcularDiferencaAbsoluta($nota1, $nota2, $trabalho) {
    $notas = [$nota1, $nota2, $trabalho];
    return abs(max($notas) - min($notas));
}

function determinarSituacao($media) {
    if ($media >= 7.0) {
        return ['status' => 'Aprovado', 'classe' => 'success', 'icon' => 'fa-check-circle'];
    } elseif ($media >= 5.0) {
        return ['status' => 'Recuperação', 'classe' => 'warning', 'icon' => 'fa-clock'];
    } else {
        return ['status' => 'Reprovado', 'classe' => 'danger', 'icon' => 'fa-times-circle'];
    }
}

function gerarMensagemDesempenho($percentualAprovacao, $mediaGeral) {
    if ($percentualAprovacao >= 70 && $mediaGeral >= 7.0) {
        return ['mensagem' => 'Excelente! A turma apresentou desempenho excepcional!', 'tipo' => 'success'];
    } elseif ($percentualAprovacao >= 50 && $mediaGeral >= 6.0) {
        return ['mensagem' => 'Bom desempenho! A turma está no caminho certo, mas pode melhorar.', 'tipo' => 'info'];
    } elseif ($percentualAprovacao >= 30) {
        return ['mensagem' => 'Desempenho regular. É necessário reforço em alguns pontos.', 'tipo' => 'warning'];
    } else {
        return ['mensagem' => 'Desempenho crítico. Recomenda-se revisão das metodologias e reforço intensivo.', 'tipo' => 'danger'];
    }
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Receber dados do formulário
$nomeTurma = $_POST['nomeTurma'] ?? 'Turma não identificada';
$alunos = $_POST['aluno'] ?? [];
$totalAlunos = count($alunos);

// Arrays para armazenar dados processados
$dadosAlunos = [];
$todasMedias = [];
$somaTotalNotas = 0;
$totalAprovados = 0;
$totalRecuperacao = 0;
$totalReprovados = 0;

// Processar cada aluno
foreach ($alunos as $index => $dados) {
    $nota1 = floatval($dados['nota1']);
    $nota2 = floatval($dados['nota2']);
    $trabalho = floatval($dados['trabalho']);
    
    // Cálculos individuais
    $media = calcularMedia($nota1, $nota2, $trabalho);
    $raizQuadrada = calcularRaizQuadradaSoma($nota1, $nota2, $trabalho);
    $diferencaAbs = calcularDiferencaAbsoluta($nota1, $nota2, $trabalho);
    $situacao = determinarSituacao($media);
    
    // Contabilizar para estatísticas da turma
    $todasMedias[] = $media;
    $somaTotalNotas += ($nota1 + $nota2 + $trabalho);
    
    if ($situacao['status'] == 'Aprovado') {
        $totalAprovados++;
    } elseif ($situacao['status'] == 'Recuperação') {
        $totalRecuperacao++;
    } else {
        $totalReprovados++;
    }
    
    // Armazenar dados do aluno
    $dadosAlunos[] = [
        'nome' => htmlspecialchars($dados['nome']),
        'nota1' => $nota1,
        'nota2' => $nota2,
        'trabalho' => $trabalho,
        'media' => $media,
        'raizQuadrada' => $raizQuadrada,
        'diferencaAbs' => $diferencaAbs,
        'situacao' => $situacao['status'],
        'classe' => $situacao['classe'],
        'icon' => $situacao['icon']
    ];
}

// Cálculos da turma
$mediaGeral = $totalAlunos > 0 ? array_sum($todasMedias) / $totalAlunos : 0;
$maiorMedia = $totalAlunos > 0 ? max($todasMedias) : 0;
$menorMedia = $totalAlunos > 0 ? min($todasMedias) : 0;
$percentualAprovacao = $totalAlunos > 0 ? ($totalAprovados / $totalAlunos) * 100 : 0;
$mensagemDesempenho = gerarMensagemDesempenho($percentualAprovacao, $mediaGeral);

?>

<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório da Turma - Análise Estatística</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  
    <!-- Icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
    <!-- SweetAlert2  -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container py-4 fade-in">
        <!-- Botões de ação -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <a href="index.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Cadastro
                </a>
                <button onclick="window.print()" class="btn btn-info text-white">
                    <i class="fas fa-print me-2"></i>Imprimir Relatório
                </button>
            </div>
        </div>
        
        <!-- Cabeçalho do Relatório -->
        <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-header gradient-bg text-white rounded-top-4 p-4">
                <div class="text-center">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <h1 class="display-5 fw-bold mb-2">Relatório Estatístico</h1>
                    <div class="h4 mb-2">
                        <i class="fas fa-school me-2"></i><?php echo htmlspecialchars($nomeTurma); ?>
                    </div>
                    <p class="mb-0">
                        <i class="fas fa-users me-2"></i>Total de Alunos: <strong><?php echo $totalAlunos; ?></strong>
                    </p>
                </div>
            </div>
            
            <!-- Cards de Estatísticas -->
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                                <h6 class="text-muted mb-1">Média Geral</h6>
                                <h3 class="text-primary mb-0"><?php echo number_format($mediaGeral, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                <h6 class="text-muted mb-1">Maior Média</h6>
                                <h3 class="text-success mb-0"><?php echo number_format($maiorMedia, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                                <h6 class="text-muted mb-1">Menor Média</h6>
                                <h3 class="text-danger mb-0"><?php echo number_format($menorMedia, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                <h6 class="text-muted mb-1">Soma Total das Notas</h6>
                                <h3 class="text-info mb-0"><?php echo number_format($somaTotalNotas, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cards de Status dos Alunos -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                <h5 class="mb-1">Aprovados</h5>
                                <h2 class="text-success mb-0"><?php echo $totalAprovados; ?></h2>
                                <small class="text-muted"><?php echo number_format($percentualAprovacao, 1); ?>% da turma</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-3x text-warning mb-2"></i>
                                <h5 class="mb-1">Recuperação</h5>
                                <h2 class="text-warning mb-0"><?php echo $totalRecuperacao; ?></h2>
                                <small class="text-muted"><?php echo number_format(($totalRecuperacao / $totalAlunos) * 100, 1); ?>% da turma</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                            <div class="card-body text-center">
                                <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                                <h5 class="mb-1">Reprovados</h5>
                                <h2 class="text-danger mb-0"><?php echo $totalReprovados; ?></h2>
                                <small class="text-muted"><?php echo number_format(($totalReprovados / $totalAlunos) * 100, 1); ?>% da turma</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mensagem de Desempenho -->
                <div class="alert alert-<?php echo $mensagemDesempenho['tipo']; ?> border-0 shadow-sm" role="alert">
                    <i class="fas fa-<?php echo $mensagemDesempenho['tipo'] == 'success' ? 'smile' : ($mensagemDesempenho['tipo'] == 'warning' ? 'exclamation-triangle' : 'frown'); ?> fa-2x me-3 float-start"></i>
                    <div class="ms-5">
                        <h5 class="alert-heading">Análise de Desempenho</h5>
                        <p class="mb-0"><?php echo $mensagemDesempenho['mensagem']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabela de Alunos -->
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header gradient-bg text-white rounded-top-4 p-3">
                <h4 class="mb-0">
                    <i class="fas fa-table me-2"></i>Detalhamento Individual dos Alunos
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3"><i class="fas fa-user me-2"></i>Aluno</th>
                                <th><i class="fas fa-pen me-2"></i>P1</th>
                                <th><i class="fas fa-pen me-2"></i>P2</th>
                                <th><i class="fas fa-tasks me-2"></i>Trabalho</th>
                                <th><i class="fas fa-calculator me-2"></i>Média</th>
                                <th><i class="fas fa-square-root-alt me-2"></i>√(Soma)</th>
                                <th><i class="fas fa-exchange-alt me-2"></i>|Maior-Menor|</th>
                                <th><i class="fas fa-graduation-cap me-2"></i>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dadosAlunos as $aluno): ?>
                            <tr>
                                <td class="ps-3 fw-bold"><?php echo $aluno['nome']; ?></td>
                                <td><?php echo number_format($aluno['nota1'], 1); ?></td>
                                <td><?php echo number_format($aluno['nota2'], 1); ?></td>
                                <td><?php echo number_format($aluno['trabalho'], 1); ?></td>
                                <td class="fw-bold"><?php echo number_format($aluno['media'], 2); ?></td>
                                <td><?php echo number_format($aluno['raizQuadrada'], 3); ?></td>
                                <td><?php echo number_format($aluno['diferencaAbs'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $aluno['classe']; ?> badge-status">
                                        <i class="fas <?php echo $aluno['icon']; ?> me-1"></i>
                                        <?php echo $aluno['situacao']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Rodapé -->
        <div class="text-center mt-4 text-muted no-print">
            <small>
                <i class="fas fa-chart-line me-1"></i>
                Relatório gerado em <?php echo date('d/m/Y H:i:s'); ?>
            </small>
        </div>
    </div>
</body>
</html>
