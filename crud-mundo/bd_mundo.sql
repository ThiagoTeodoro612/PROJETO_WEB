-- Criação do banco de dados
CREATE DATABASE bd_mundo;
USE bd_mundo;

-- Tabela Continentes
CREATE TABLE tb_continentes (
    id_continente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    populacao BIGINT NOT NULL DEFAULT 0,
    area DECIMAL(15,2) NOT NULL,
    total_paises INT DEFAULT 0
);

-- Tabela Paises
CREATE TABLE tb_paises (
    id_pais INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    populacao BIGINT NOT NULL,
    area DECIMAL(15,2) NOT NULL,
    idioma VARCHAR(100) NOT NULL,
    clima CHAR(1) NOT NULL,
    -- 0 - Equatorial
    -- 1 - Tropical
    -- 2 - Temperado
    -- 3 - Polar
    -- 4 - Subequatorial
    -- 5 - Subtropical
    -- 6 - Subpolar
    regime_politico VARCHAR(100),
    moeda VARCHAR(50),
    id_continente INT,
    FOREIGN KEY (id_continente) REFERENCES tb_continentes(id_continente)
    -- 1 - Ásia
    -- 2 - África
    -- 3 - América do Norte
    -- 4 - América do Sul
    -- 5 - Antártida
    -- 6 - Europa
    -- 7 - Oceania
);

-- Tabela Governantes
CREATE TABLE tb_governantes (
    id_governante INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    partido_politico VARCHAR(100),
    data_nascimento DATE,
    idade INT,
    data_inicio_mandato DATE,
    data_fim_mandato DATE
);

-- Adicionando chave estrangeira de governante em Paises
ALTER TABLE tb_paises
ADD COLUMN id_governante INT,
ADD FOREIGN KEY (id_governante) REFERENCES tb_governantes(id_governante);

-- Tabela Cidades
CREATE TABLE tb_cidades (
    id_cidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    populacao BIGINT,
    area DECIMAL(15,2) NOT NULL,
    clima CHAR(1),
    -- 0 - Equatorial
    -- 1 - Tropical
    -- 2 - Temperado
    -- 3 - Polar
    -- 4 - Subequatorial
    -- 5 - Subtropical
    -- 6 - Subpolar
    data_fundacao DATE,
    id_pais INT,
    FOREIGN KEY (id_pais) REFERENCES tb_paises(id_pais)
);

-- Adicionando governante em Cidades
ALTER TABLE tb_cidades
ADD COLUMN id_governante INT,
ADD FOREIGN KEY (id_governante) REFERENCES tb_governantes(id_governante);

INSERT INTO tb_continentes(nome, area) VALUES 
('Ásia', 44579000),
('África', 30370000),
('América do Norte', 24709000),
('América do Sul', 17840000),
('Antártida', 14200000),
('Europa', 10180000),
('Oceania', 8526000);

INSERT INTO tb_governantes (nome, partido_politico, data_nascimento, idade, data_inicio_mandato, data_fim_mandato) VALUES
('Fumio Kishida', 'Partido Liberal Democrata', '1957-07-29', 69, '2021-10-04', '2024-10-01'),
('Justin Trudeau', 'Partido Liberal', '1971-12-25', 54, '2015-11-04', NULL),
('Bola Tinubu', 'All Progressives Congress', '1952-03-29', 74, '2023-05-29', NULL),
('Anthony Albanese', 'Partido Trabalhista', '1963-03-02', 63, '2022-05-23', NULL),
('Xi Jinping', 'Partido Comunista da China', '1953-06-15', 73, '2013-03-14', NULL),
('Ulf Kristersson', 'Partido Moderado', '1963-12-29', 62, '2022-10-18', NULL);

INSERT INTO tb_paises(nome, populacao, area, idioma, clima, regime_politico, moeda, id_continente, id_governante) VALUES 
('Japão', 125700000, 377975.00, 'Japonês', '1', 'Monarquia constitucional unitária parlamentarista de partido dominante', 'Iene', 1, 1),
('Canadá', 38781291, 9984670.00, 'Inglês/Francês', '3', 'Monarquia constitucional federal parlamentarista', 'Dólar Canadense', 3, 2),
('Nigéria', 223804632, 923768.00, 'Inglês', '2', 'República presidencialista federal', 'Naira', 2, 3),
('Austrália', 26439112, 7692024.00, 'Inglês', '7', 'Monarquia constitucional e democracia federal parlamentar', 'Dólar Australiano', 7, 4),
('China', 1411750000, 9596961.00, 'Mandarim Padrão', '1', 'Estado comunista unitário', 'Yuan', 1, 5),
('Suécia', 10549347, 450295.00, 'Sueco', '6', '	Monarquia constitucional parlamentarista unitária', 'Coroa Sueca', 6, 6);

-- Inserindo cidades para os países cadastrados

-- Cidades do Japão (id_pais = 1)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Tóquio', 13960000, 2194.07, '1', '1457-01-01', 1, 1),
('Osaka', 2691000, 225.21, '1', '1496-01-01', 1, 1),
('Nagoya', 2333000, 326.45, '1', '1610-01-01', 1, 1),
('Sapporo', 1974000, 1121.12, '6', '1868-01-01', 1, 1),
('Fukuoka', 1613000, 343.39, '1', '1889-04-01', 1, 1);

-- Cidades do Canadá (id_pais = 2)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Ottawa', 934243, 2790.30, '2', '1826-01-01', 2, 2),
('Toronto', 2930000, 630.21, '2', '1834-03-06', 2, 2),
('Vancouver', 662248, 115.18, '1', '1886-04-06', 2, 2),
('Montreal', 1762949, 431.50, '2', '1642-05-17', 2, 2),
('Calgary', 1239220, 825.29, '2', '1875-01-01', 2, 2);

-- Cidades da Nigéria (id_pais = 3)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Lagos', 15000000, 1171.28, '2', '1901-01-01', 3, 3),
('Abuja', 1236000, 713.00, '2', '1976-01-01', 3, 3),
('Kano', 3600000, 499.00, '5', '1903-01-01', 3, 3),
('Ibadan', 3560000, 3080.00, '2', '1829-01-01', 3, 3),
('Benin City', 1494000, 1204.00, '2', '1899-01-01', 3, 3);

-- Cidades da Austrália (id_pais = 4)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Canberra', 403468, 814.20, '5', '1913-03-12', 4, 4),
('Sydney', 5312163, 12145.00, '5', '1788-01-26', 4, 4),
('Melbourne', 5023170, 9992.50, '5', '1835-08-30', 4, 4),
('Brisbane', 2461637, 15826.00, '5', '1824-01-01', 4, 4),
('Perth', 2115000, 6418.00, '5', '1829-06-01', 4, 4);

-- Cidades da China (id_pais = 5)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Pequim', 21890000, 16410.54, '2', '1949-10-01', 5, 5),
('Xangai', 24870000, 6340.50, '5', '1927-07-01', 5, 5),
('Shenzhen', 17600000, 1997.47, '1', '1980-08-26', 5, 5),
('Cantão', 14000000, 7434.40, '5', '214-01-01', 5, 5),
('Chengdu', 16330000, 14378.00, '5', '316-01-01', 5, 5);

-- Cidades da Suécia (id_pais = 6)
INSERT INTO tb_cidades (nome, populacao, area, clima, data_fundacao, id_pais, id_governante) VALUES
('Estocolmo', 984748, 188.00, '6', '1252-01-01', 6, 6),
('Gotemburgo', 596842, 447.76, '6', '1621-01-01', 6, 6),
('Malmö', 351749, 335.25, '6', '1250-01-01', 6, 6),
('Uppsala', 180216, 48.77, '6', '1164-01-01', 6, 6),
('Västerås', 131456, 99.12, '6', '990-01-01', 6, 6);
