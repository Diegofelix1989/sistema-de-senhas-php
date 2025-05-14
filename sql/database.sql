
CREATE DATABASE IF NOT EXISTS gerenciador_filas;
USE gerenciador_filas;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255),
    tipo ENUM('admin', 'atendente') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE locais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE filas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    tipo ENUM('comum', 'prioritaria'),
    local_id INT,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE
);

CREATE TABLE guiches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    fila_id INT NOT NULL,
    FOREIGN KEY (local_id) REFERENCES locais(id),
    FOREIGN KEY (fila_id) REFERENCES filas(id)
);

CREATE TABLE telas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    fila_id INT NOT NULL,
    tipo_exibicao ENUM('senhas', 'publicidade', 'ambos') NOT NULL,
    FOREIGN KEY (local_id) REFERENCES locais(id),
    FOREIGN KEY (fila_id) REFERENCES filas(id)
);

CREATE TABLE `publicidades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `tipo_midia` ENUM('imagem', 'video', 'texto') NOT NULL,
    `media_path` VARCHAR(255) DEFAULT NULL,
    `duracao` INT NOT NULL,
    `id_tela` INT NOT NULL,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tela) REFERENCES telas(id) ON DELETE CASCADE
);

CREATE TABLE senhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    fila_id INT,
    status ENUM('aguardando', 'em_atendimento', 'atendida') DEFAULT 'aguardando',
    chamada_por INT,
    chamada_em TIMESTAMP NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fila_id) REFERENCES filas(id) ON DELETE CASCADE,
    FOREIGN KEY (chamada_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

ALTER TABLE senhas
ADD COLUMN atendimento_iniciado_em TIMESTAMP NULL,
ADD COLUMN atendimento_finalizado_em TIMESTAMP NULL;

ALTER TABLE senhas
ADD COLUMN guiche_id INT AFTER chamada_por,
ADD CONSTRAINT fk_guiche_id FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE SET NULL;
