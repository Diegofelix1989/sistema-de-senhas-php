-- Script para criar a tabela publicidades

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