//#region Loja

// Função que cria objetos dos produtos da loja
function Produto(nome, preco, desconto = 0) {
    this.nome = nome;
    this.preco = preco;
    this.desconto = desconto;
    loja.push(this);
}

// Array da loja
let loja = []

// Produtos da loja
Produto("Cesta de Café da Manhã", 89.90);
Produto("Fone de Ouvido com Fio", 29.99, 20);
Produto("Camiseta Básica Algodão", 49.90);
Produto("Garrafa Térmica 500ml", 45.00);
Produto("Carregador Portátil 10000mAh", 79.90);
Produto("Mochila Escolar Pequena", 69.99);
Produto("Filtro de Água de Barro", 95.00, 5);
Produto("Jogo de Lençóis Casal", 89.99);
Produto("Ventilador de Mesa 30cm", 99.90);
Produto("Caixa de Som Bluetooth", 65.00);
Produto("Kit de Panelas Antiaderente (3 peças)", 99.00);
Produto("Tênis Esportivo Adulto", 89.99);
Produto("Relógio Digital de Pulso", 49.90, 5);
Produto("Jogo de Talheres (24 peças)", 39.90, 5);
Produto("Ferro de Passar Roupa a Vapor", 89.99);
Produto("Manta Elétrica 1,5m x 1m", 79.90, 50);
Produto("Furadeira Elétrica 500W", 99.00);
Produto("Cadeira de Escritório Simples", 99.90, 15);
Produto("Vaso de Flores Cerâmica Grande", 45.00);
Produto("Tapete Borracha para Carro (4 peças)", 59.90, 2);

// Filtra um array de produtos pelo seu preco
function FiltrarPreco(array, preco, min = false) {
    let array_filtrado = []
    if (min) { // define se o filtro é para o valor minimo ou máximo
        array_filtrado = array.filter((item) => item.preco <= preco);
    }
    else {
        array_filtrado = array.filter((item) => item.preco > preco);
    }
    return array_filtrado;
}

// Define os filtros
const Filtro = {
    SEM: 0, //Sem filtro
    MQ50: 1, //Maior que 50 reais
    A50: 2 //Até 50 reais
};

// Atualiza os itens da loja na página de acordo com um filtro
function AtualizarLojaFiltro(filtro) {

    // Puxa o objeto da lista de objetos no html
    const lista_de_produtos = document.getElementById('lista de produtos');
    
    // Limpa o html interno da lista de produtos
    lista_de_produtos.innerHTML = "";

    // Puxa a div do filtro e seus elementos
    const filtro_selectionado = document.getElementById('filtro');
    let botoes = filtro_selectionado.getElementsByTagName("button");
    let texto = document.getElementById("filtro_texto");

    // Cria um array que irá guardar o que resta do array da loja após passar por um filtro
    let loja_filtrada = [];

    // Switch para realizar um filtro
    switch (filtro) {
        case Filtro.SEM: // Sem filtros
            loja_filtrada = loja;
            texto.innerHTML = "Nenhum";
            for (var i = 0; i < botoes.length; i++) {
                if (botoes[i].name == "nenhum") {
                    botoes[i].disabled = true;
                }
                else {
                    botoes[i].disabled = false;
                }
            }
            break;
        case Filtro.MQ50: // Produtos com preço maior que 50
            loja_filtrada = FiltrarPreco(loja, 50);
            texto.innerHTML = "Acima de R$50.00.";
            for (var i = 0; i < botoes.length; i++) {
                if (botoes[i].name == "maior que 50") {
                    botoes[i].disabled = true;
                }
                else {
                    botoes[i].disabled = false;
                }
            }
            break;
        case Filtro.A50: // Produtos com preço de até 50
            loja_filtrada = FiltrarPreco(loja, 50, true);
            texto.innerHTML = "Até R$50.00.";
            for (var i = 0; i < botoes.length; i++) {
                if (botoes[i].name == "ate 50") {
                    botoes[i].disabled = true;
                }
                else {
                    botoes[i].disabled = false;
                }
            }
            break;
    }

    // Ordena por preço
    loja_filtrada = loja_filtrada.sort(function (a, b) {
        if (a.preco > b.preco) {
            return 1;
        }
        if (a.preco < b.preco) {
            return -1;
        }
        return 0;
    })

    // Adiciona cada produto a lista da loja
    for (var i = 0; i < loja_filtrada.length; i++) { 

        // Puxa um produto da loja após o filtro
        let produto = loja_filtrada[i];

        // Puxa o preço e nome
        let nome = produto.nome;
        let preco = produto.preco;

        // Cria as variáveis que vão armazenar o HTML do preço e desconto do produto
        let desconto_html = "";
        let preco_html = "";
        if (produto.desconto > 0) {
            
            // Calcula o preço com desconto
            let preco_desconto = preco - (preco * (produto.desconto / 100));

            // Cria o HTML
            desconto_html = ` <span class="badge bg-success">-${produto.desconto}%</span>`;
            preco_html = `
                <p class="fs-6 text-muted lh-1">De: <strike>R$ ${preco.toFixed(2)}</strike>
                </p><p class="fs-5">Por: R$ ${preco_desconto.toFixed(2)}</p>
            `
        }
        else {

            // Cria o HTML
            desconto_html = "";
            preco_html = `<p class="fs-5">Por: R$ ${preco.toFixed(2)}</p>`;
        }

        // Cria o elemento que será adicionado ao HTML
        let novo_produto = document.createElement('div');
        
        // Define as classes do bootstrap
        novo_produto.className = "col-sm-12 col-md-8 col-lg-6 col-xxl-4 border rounded p-3 m-1";

        // Define o HTML interno do elemento
        novo_produto.innerHTML = `
            <div class="text-center">
                <h3 class="text-center">${nome}${desconto_html}</h3>
                <hr>
                ${preco_html}

                <button type="button" class="btn btn-primary btn-sm border border-dark"
                    onclick='AdicionarCarrinho("${nome}",1)'>
                    Adicionar ao <b>Carrinho</b>.
                </button>
            </div>
        `;

        // Adiciona o elemento ao HTML
        lista_de_produtos.appendChild(novo_produto);
    }
}

// Puxa um produto da loja com base em seu nome
function PegarProdutoLoja(nome) { 
    let produto = loja[
        loja.map( //Cria um array com apenas os nomes dos objetos
            function (produto) {
                return produto.nome;
            }
        ).indexOf(nome) //Pega o index do nome que foi inserido e o usa para pegar o objeto do produto na loja
    ]
    return produto;
}

//#endregion

//#region Carrinho

// Verifica se o carrinho existe no localStorage, se não existir, cria ele
if (localStorage.getItem("carrinho") === null) { 
    localStorage.setItem("carrinho", JSON.stringify({ array: [] }));
}

// Remove todos os itens do carrinho
function LimparCarrinho() {
    localStorage.setItem("carrinho", JSON.stringify({ array: [] }));

    AtualizarCarrinho();
}

// Retorna um carrinho com apenas a propriedade do nome dos produtos
function PegarNomesCarrinho() {
    let carrinho = JSON.parse(localStorage.getItem("carrinho"));
    let nomes = carrinho.array.map( // Cria um array com apenas os nomes dos objetos
        function (produto) {
            return produto.nome;
        })
    return nomes;
}

// Verifica se o produto existe no carrinho
function VerificarCarrinho(nome) {
    let nomes = PegarNomesCarrinho();

    if (nomes.includes(nome)) {
        return true;
    }
    else {
        return false;
    }
}

// Adiciona um produto ao carrinho
function AdicionarCarrinho(nome, quantidade) {
    let produto = PegarProdutoLoja(nome)
    let carrinho = JSON.parse(localStorage.getItem("carrinho"));
    if (VerificarCarrinho(nome)) {
        carrinho.array[PegarNomesCarrinho().indexOf(nome)].quantidade += quantidade;
    }
    else {
        produto.quantidade = quantidade;
        carrinho.array.push(produto);
    }
    localStorage.setItem("carrinho", JSON.stringify(carrinho));

    AtualizarCarrinho();
}

// Remove uma quantidade de produtos do carrinho
function RemoverCarrinhoQuantidade(nome, quantidade) {
    let carrinho = JSON.parse(localStorage.getItem("carrinho"));
    if (VerificarCarrinho(nome)) {
        carrinho.array[PegarNomesCarrinho().indexOf(nome)].quantidade -= quantidade;
    }

    if (carrinho.array[PegarNomesCarrinho().indexOf(nome)].quantidade <= 0) {
        RemoverCarrinho(nome);
    }
    else {
        localStorage.setItem("carrinho", JSON.stringify(carrinho));
        AtualizarCarrinho();
    }
}

// Remove um produto do carrinho independente da quantidade
function RemoverCarrinho(nome) { //remove um produto do carrinho
    if (VerificarCarrinho(nome)) {
        let carrinho = JSON.parse(localStorage.getItem("carrinho"));
        carrinho.array.splice(PegarNomesCarrinho().indexOf(nome), 1);
        localStorage.setItem("carrinho", JSON.stringify(carrinho));
    }

    AtualizarCarrinho();
}

// Atualiza o HTML do carrinho
function AtualizarCarrinho() {

    // Puxa o elemento do carrinho do HTML
    const lista_de_carrinho = document.getElementById('carrinho');
    const valor_total = document.getElementById('valor_total');

    // Apaga o carrinho anterior
    lista_de_carrinho.innerHTML = "";

    // Puxa o carrinho do localStorage
    let carrinho = JSON.parse(localStorage.getItem("carrinho"));

    // Variável para armazenar o preço da compra
    let preco_compra = 0;

    // Executa o código apenas se o carrinho não estiver vazio
    if (carrinho.array.length > 0) {

        // Ordena o carrinho por preço
        carrinho.array = carrinho.array.sort(function (a, b) {
        if (a.preco > b.preco) {
            return 1;
        }
        if (a.preco < b.preco) {
            return -1;
        }
            return 0;
        })

        // Adiciona todos os produtos do carrinho no HTML
        for (var i = 0; i < carrinho.array.length; i++) {
            let produto = carrinho.array[i];

            // Cria as variáveis que vão armazenar os dados do produto
            let nome = produto.nome;
            let preco = produto.preco;
            let quantidade = produto.quantidade;

            // Calcula o preço após o desconto e o adiciona ao preço da compra multiplicado pela quantidade do produto
            let preco_desconto = preco - (preco * (produto.desconto / 100));
            preco_compra += preco_desconto * quantidade

            // Cria as variáveis que vão armazenar o HTML do preço e desconto do produto
            let desconto_html = "";
            let preco_html = "";

            // Faz o HTML do produto, podendo variar se houver desconto
            if (produto.desconto > 0) {
                desconto_html = ` <span class="badge bg-success">-${produto.desconto}%</span>`;
                preco_html = `
                <p class="fs-6 text-muted lh-1">De: <strike>R$ ${(preco * quantidade * quantidade).toFixed(2)} (<i>R$ ${preco.toFixed(2)} x ${quantidade}</i>)</strike></p>
                <p class="fs-5">Por: <bold>R$ ${(preco_desconto * quantidade).toFixed(2)}<bold> <br> (<i>R$ ${preco_desconto.toFixed(2)} x ${quantidade}</i>)</p>
                `
            }
            else {
                desconto_html = "";
                preco_html = `<p class="fs-5">Por: <b>R$ ${(preco * quantidade).toFixed(2)}</b> <br> (<i>R$ ${preco.toFixed(2)} x ${quantidade}</i>)</p>`;
            }

            // Cria o elemento que será adicionado ao HTML
            let novo_produto = document.createElement('div');

            // Define as classes do bootstrap
            novo_produto.className = "col-sm-12 col-md-8 col-lg-6 col-xxl-4 border rounded p-3 m-1";

            // Define o HTML interno do elemento
            novo_produto.innerHTML = `
                <div class="text-center">
                    <h3 class="text-center">${nome}${desconto_html}</h3>
                    <hr>
                    <p>Quantidade: ${quantidade}</p>
                    ${preco_html}
                    <div class="btn-group-vertical" role="group">
                        <button type="button" class="btn btn-danger border border-dark border-top-0 border-left-0 border-right-0"
                            onclick='RemoverCarrinhoQuantidade("${nome}",1)'>
                            Remover 1
                        </button>
                        <button type="button" class="btn btn-danger border border-dark border-bottom-0 border-left-0 border-right-0"
                            onclick='RemoverCarrinho("${nome}")'>
                            Remover todos
                        </button>
                    </div>
                </div>
            `;

            // Adiciona o elemento ao HTML
            lista_de_carrinho.appendChild(novo_produto);
        }
        valor_total.innerHTML = `Valor Total: R$ ${(preco_compra).toFixed(2)}`;
    }
    // Executa se o carrinho estiver vazio
    else {

        // Cria o elemento que será adicionado ao HTML
        let vazio = document.createElement('div');

        // Define as classes do bootstrap
        vazio.className = "row mb-4 p-2 text-white rounded text-center";

        // Define o HTML interno do elemento
        vazio.innerHTML = `
                <h2><i>Carrinho Vazio.</i></h2>
            `;

        // Adiciona o elemento ao HTML
        lista_de_carrinho.appendChild(vazio);
        valor_total.innerHTML = `Total da compra: R$ 0.00`;
    }
}

//#endregion
