# Sistema Web de Análise Estatística de Turma Escolar

## Sobre o projeto
Uma aplicação web destinada a professores e instituições de ensino para o lançamento de notas de alunos e geração de relatórios estatísticos. O sistema calcula médias individuais, estatísticas gerais da turma (maior/menor média, percentual de aprovação) e fornece uma análise qualitativa do desempenho da turma.

## Funcionalidades
- Cadastro dinâmico de alunos (nome, notas de provas e trabalho).
- Cálculo de média individual e situação do aluno (Aprovado, Recuperação, Reprovado).
- Cálculo de estatísticas da turma: Média Geral, Maior Média, Menor Média e Soma Total.
- Geração de relatório detalhado com tabela de alunos.
- Opção de imprimir o relatório final.
- Validação de dados de entrada.

## Tecnologias utilizadas
- HTML5
- CSS3 (Bootstrap 5)
- JavaScript (Vanilla)
- PHP (Back-end para processamento dos dados)
- FontAwesome (Ícones)
- SweetAlert2 (Alertas)

## Requisitos
- Servidor web com suporte a PHP (ex: XAMPP, WAMP, Laragon).
- Navegador web moderno.

## Estrutura do projeto
- `index.php`: Formulário de entrada de dados e interface inicial.
- `processamento.php`: Script PHP responsável por receber os dados, realizar os cálculos estatísticos e renderizar o relatório.
- `script.js`: Lógica de front-end para gerar dinamicamente os campos de entrada dos alunos.

## Como executar
1. Clone o repositório e mova a pasta do projeto para o diretório raiz do seu servidor local (ex: `htdocs` no XAMPP).
2. Inicie o servidor Apache.
3. Abra o navegador e acesse `http://localhost/Sistema Web de Análise Estatística de Turma Escolar/index.php`.
4. Preencha os dados da turma e clique em "Processar Dados".

## Autor
Thiago Gouveia Faria Teodoro
