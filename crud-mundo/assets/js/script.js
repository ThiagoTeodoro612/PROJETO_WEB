// assets/js/script.js

// Função para formatar números
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Função para validação de formulários
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Confirmação de exclusão genérica
function confirmarExclusao(url, nome, tipo) {
    Swal.fire({
        title: `Excluir ${tipo}`,
        text: `Tem certeza que deseja excluir "${nome}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Máscara para números
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar máscara para campos numéricos
    const numeros = document.querySelectorAll('input[type="number"]');
    numeros.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    // Adicionar tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
});

// Função de pesquisa dinâmica
function pesquisarTabela(inputId, tableId, colunaIndex) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    input.addEventListener('keyup', function() {
        const filtro = this.value.toLowerCase();
        const linhas = document.querySelectorAll(`#${tableId} tbody tr`);
        linhas.forEach(linha => {
            const celula = linha.querySelectorAll('td')[colunaIndex];
            if (celula) {
                const texto = celula.textContent.toLowerCase();
                linha.style.display = texto.includes(filtro) ? '' : 'none';
            }
        });
    });
}

// Função para carregar cidades por país (AJAX)
function carregarCidadesPorPais(paisId, selectElement) {
    if (!paisId) {
        selectElement.innerHTML = '<option value="">Selecione um país</option>';
        return;
    }
    
    fetch(`../../api/cidades.php?pais_id=${paisId}`)
        .then(response => response.json())
        .then(data => {
            selectElement.innerHTML = '<option value="">Selecione uma cidade</option>';
            data.forEach(cidade => {
                selectElement.innerHTML += `<option value="${cidade.id}">${cidade.nome}</option>`;
            });
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

// Inicializar tooltips e outros componentes quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});