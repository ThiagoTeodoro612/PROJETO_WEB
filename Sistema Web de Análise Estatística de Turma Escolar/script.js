function gerarCampos() {
    const qtd = document.getElementById('qtdAlunos').value;
    const container = document.getElementById('camposAlunos');
    const submitDiv = document.getElementById('submitButton');

    if (!qtd || qtd < 1) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Por favor, informe a quantidade de alunos!',
            confirmButtonColor: '#667eea'
        });
        return;
    }

    let html = `
                <div class="mt-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>${qtd} aluno(s)</strong> para cadastrar. Preencha todos os campos abaixo.
                    </div>
                    <h4 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Dados dos Alunos</h4>
            `;

    for (let i = 1; i <= qtd; i++) {
        html += `
                    <div class="card aluno-card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Aluno ${i}
                            </h5>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome do Aluno</label>
                                <input type="text" class="form-control" name="aluno[${i}][nome]" required>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-pen me-1"></i>Nota da Prova 1
                                    </label>
                                    <input type="number" step="0.1" class="form-control" name="aluno[${i}][nota1]" 
                                           min="0" max="10" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-pen me-1"></i>Nota da Prova 2
                                    </label>
                                    <input type="number" step="0.1" class="form-control" name="aluno[${i}][nota2]" 
                                           min="0" max="10" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-tasks me-1"></i>Nota do Trabalho
                                    </label>
                                    <input type="number" step="0.1" class="form-control" name="aluno[${i}][trabalho]" 
                                           min="0" max="10" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
    }

    html += `</div>`;
    container.innerHTML = html;
    container.style.display = 'block';
    submitDiv.style.display = 'block';

    // Scroll suave até os campos
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
