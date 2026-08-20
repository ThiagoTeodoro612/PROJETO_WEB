-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS bd_mundo;
USE bd_mundo;

-- Tabela Continentes
CREATE TABLE continentes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    populacao BIGINT,
    area_km2 DECIMAL(15,2),
    total_paises INT DEFAULT 0
);

-- Tabela Governantes
CREATE TABLE governantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    partido_politico VARCHAR(100),
    data_nascimento DATE,
    idade INT,
    data_inicio_mandato DATE,
    data_fim_mandato DATE
);

-- Tabela Países
CREATE TABLE paises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    continente_id INT,
    populacao BIGINT,
    area_km2 DECIMAL(15,2),
    idioma VARCHAR(50),
    governante_id INT,
    clima VARCHAR(50),
    regime_politico VARCHAR(50),
    moeda VARCHAR(50),
    FOREIGN KEY (continente_id) REFERENCES continentes(id) ON DELETE SET NULL,
    FOREIGN KEY (governante_id) REFERENCES governantes(id) ON DELETE SET NULL
);

-- Tabela Cidades
CREATE TABLE cidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    pais_id INT,
    populacao BIGINT,
    area_km2 DECIMAL(15,2),
    clima VARCHAR(50),
    governante_id INT,
    data_fundacao DATE,
    FOREIGN KEY (pais_id) REFERENCES paises(id) ON DELETE CASCADE,
    FOREIGN KEY (governante_id) REFERENCES governantes(id) ON DELETE SET NULL
);

-- Índices para otimização
CREATE INDEX idx_pais_continente ON paises(continente_id);
CREATE INDEX idx_cidade_pais ON cidades(pais_id);