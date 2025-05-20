
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
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('comum', 'prioritaria') NOT NULL,
    prefixo VARCHAR(5) NOT NULL,
    local_id INT NOT NULL,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE,
    UNIQUE (prefixo, local_id)
);
 
ALTER TABLE filas ADD COLUMN tamanho_senha INT DEFAULT 3;

CREATE TABLE guiches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    fila_id INT NOT NULL,
    FOREIGN KEY (local_id) REFERENCES locais(id),
    FOREIGN KEY (fila_id) REFERENCES filas(id)
);
ALTER TABLE guiches
ADD COLUMN status_uso ENUM('disponivel', 'em_uso') NOT NULL DEFAULT 'disponivel',
ADD COLUMN status_ativo ENUM('ativo', 'desativado') NOT NULL DEFAULT 'ativo';

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

CREATE TABLE log_atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    senha_id INT NOT NULL,
    guiche_id INT,
    acao VARCHAR(50) NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (senha_id) REFERENCES senhas(id) ON DELETE CASCADE,
    FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE SET NULL
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
ADD COLUMN observacao TEXT NULL AFTER status;



ALTER TABLE senhas
ADD COLUMN atendimento_iniciado_em TIMESTAMP NULL,
ADD COLUMN atendimento_finalizado_em TIMESTAMP NULL;

ALTER TABLE senhas
ADD COLUMN guiche_id INT AFTER chamada_por,
ADD CONSTRAINT fk_guiche_id FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE SET NULL;

ALTER TABLE senhas
ADD COLUMN guiche_id INT NULL;

ALTER TABLE senhas
ADD CONSTRAINT fk_guiche
FOREIGN KEY (guiche_id) REFERENCES guiches(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

