<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Análise Estatística - Turma Escolar</title>
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
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Card Principal -->
                <div class="card shadow-lg border-0 rounded-4 fade-in">
                    <div class="card-header gradient-bg text-white rounded-top-4 p-4">
                        <div class="text-center">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <h1 class="h2 mb-2">Sistema de Análise Estatística</h1>
                            <p class="mb-0 opacity-75">Cadastre os dados da turma e realize análises completas de desempenho</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <form method="POST" action="processamento.php" id="formTurma">
                            <!-- Dados da Turma -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label for="nomeTurma" class="form-label fw-bold">
                                        <i class="fas fa-school me-2"></i>Nome da Turma
                                    </label>
                                    <input type="text" class="form-control form-control-lg" name="nomeTurma" id="nomeTurma" 
                                           required placeholder="Ex: 3º Ano A - Ensino Médio">
                                </div>
                                <div class="col-md-4">
                                    <label for="qtdAlunos" class="form-label fw-bold">
                                        <i class="fas fa-users me-2"></i>Quantidade de Alunos
                                    </label>
                                    <input type="number" class="form-control form-control-lg" name="qtdAlunos" id="qtdAlunos" 
                                           min="1" max="50" required>
                                </div>
                            </div>
                            
                            <!-- Botão Gerar Campos -->
                            <div class="text-center mb-4">
                                <button type="button" class="btn btn-primary btn-lg px-5" onclick="gerarCampos()">
                                    <i class="fas fa-plus-circle me-2"></i>Gerar Campos dos Alunos
                                </button>
                            </div>
                            
                            <!-- Container para os campos dos alunos -->
                            <div id="camposAlunos" style="display: none;"></div>
                            
                            <!-- Botão de Envio -->
                            <div id="submitButton" style="display: none;" class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-calculator me-2"></i>Processar Dados da Turma
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
