-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS gerenciador_filas;
USE gerenciador_filas;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'atendente') NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- Tabela de locais
CREATE TABLE locais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- Tabela de tipos de ticket
CREATE TABLE tipos_ticket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    prioridade INT DEFAULT 0,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de filas
CREATE TABLE filas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo_ticket_id INT NOT NULL,
    prefixo VARCHAR(5) NOT NULL,
    tamanho_ticket INT DEFAULT 3,
    local_id INT NOT NULL,
    reset_ticket ENUM('nunca', 'diario', 'semanal', 'mensal', 'anual', 'manual') DEFAULT 'nunca',
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_ticket_id) REFERENCES tipos_ticket(id) ON DELETE RESTRICT,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE,
    UNIQUE (prefixo, local_id),
    INDEX idx_local_status (local_id, status),
    INDEX idx_tipo_ticket (tipo_ticket_id)
);

-- Tabela de controle de tickets
CREATE TABLE controle_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fila_id INT NOT NULL,
    referencia VARCHAR(20) NOT NULL,
    ultimo_numero INT DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (fila_id, referencia),
    FOREIGN KEY (fila_id) REFERENCES filas(id) ON DELETE CASCADE
);

-- Tabela de guichês
CREATE TABLE guiches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    status_uso ENUM('disponivel', 'em_uso') NOT NULL DEFAULT 'disponivel',
    status_ativo ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE,
    INDEX idx_local_status (local_id, status_ativo),
    INDEX idx_status_uso (status_uso)
);

-- Tabela de telas
CREATE TABLE telas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    tipo_exibicao ENUM('tickets', 'publicidade', 'ambos') NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE,
    INDEX idx_local_status (local_id, status),
    INDEX idx_tipo_exibicao (tipo_exibicao)
);

-- Tabela de publicidades
CREATE TABLE publicidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo_midia ENUM('imagem', 'video', 'texto', 'url') NOT NULL,
    media_path VARCHAR(255) DEFAULT NULL,
    duracao INT NOT NULL,
    tela_id INT NOT NULL,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_inicio DATE NULL,
    data_fim DATE NULL,
    FOREIGN KEY (tela_id) REFERENCES telas(id) ON DELETE CASCADE,
    INDEX idx_tela_status (tela_id, status),
    INDEX idx_datas (data_inicio, data_fim),
    INDEX idx_tipo_midia (tipo_midia)
);

-- Tabela de tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    fila_id INT NOT NULL,
    status ENUM('aguardando', 'em_atendimento', 'atendido', 'cancelado') DEFAULT 'aguardando',
    observacao TEXT NULL,
    chamado_por INT NULL,
    chamado_em TIMESTAMP NULL,
    atendimento_iniciado_em TIMESTAMP NULL,
    atendimento_finalizado_em TIMESTAMP NULL,
    guiche_id INT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fila_id) REFERENCES filas(id) ON DELETE CASCADE,
    FOREIGN KEY (chamado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE SET NULL,
    INDEX idx_fila_status (fila_id, status),
    INDEX idx_status_criado (status, criado_em),
    INDEX idx_numero_fila (numero, fila_id),
    INDEX idx_chamado_em (chamado_em),
    INDEX idx_guiche (guiche_id)
);

-- Tabela de log de atendimentos
CREATE TABLE log_atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ticket_id INT NOT NULL,
    guiche_id INT NULL,
    acao VARCHAR(50) NOT NULL,
    detalhes TEXT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (guiche_id) REFERENCES guiches(id) ON DELETE SET NULL,
    INDEX idx_usuario_timestamp (usuario_id, timestamp),
    INDEX idx_ticket_timestamp (ticket_id, timestamp),
    INDEX idx_guiche_timestamp (guiche_id, timestamp),
    INDEX idx_acao (acao),
    INDEX idx_timestamp (timestamp)
);

-- Tabela de impressoras térmicas
CREATE TABLE impressoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    modelo VARCHAR(100),
    ip VARCHAR(45),
    porta INT DEFAULT 9100,
    local_id INT NOT NULL,
    tipo ENUM('ticket', 'relatorio', 'geral') DEFAULT 'ticket',
    largura_colunas INT DEFAULT 42,
    cabecalho TEXT,
    rodape TEXT,
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (local_id) REFERENCES locais(id) ON DELETE CASCADE,
    INDEX idx_local_status (local_id, status),
    INDEX idx_ip (ip)
);

-- Inserir tipos de ticket iniciais
INSERT INTO tipos_ticket (nome, descricao, prioridade)
VALUES 
    ('comum', 'Ticket comum sem prioridade especial', 10),
    ('prioritario', 'Atendimento prioritário (idosos, gestantes, etc.)', 1);

-- Procedimento para resetar tickets
DELIMITER $$
CREATE PROCEDURE resetar_tickets()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE filaId INT;
    DECLARE resetTipo VARCHAR(10);
    DECLARE referencia VARCHAR(20);

    DECLARE filas_cursor CURSOR FOR 
        SELECT id, reset_ticket FROM filas WHERE status = 'ativo';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN filas_cursor;

    read_loop: LOOP
        FETCH filas_cursor INTO filaId, resetTipo;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET referencia = 
            CASE resetTipo
                WHEN 'diario' THEN DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d')
                WHEN 'semanal' THEN CONCAT(YEAR(CURRENT_DATE), '-W', WEEK(CURRENT_DATE, 1))
                WHEN 'mensal' THEN DATE_FORMAT(CURRENT_DATE, '%Y-%m')
                WHEN 'anual' THEN DATE_FORMAT(CURRENT_DATE, '%Y')
                ELSE NULL
            END;

        IF referencia IS NOT NULL THEN
            IF EXISTS (SELECT 1 FROM controle_tickets WHERE fila_id = filaId AND referencia = referencia) THEN
                UPDATE controle_tickets SET ultimo_numero = 0 WHERE fila_id = filaId AND referencia = referencia;
            ELSE
                INSERT INTO controle_tickets (fila_id, referencia, ultimo_numero) VALUES (filaId, referencia, 0);
            END IF;
        END IF;
    END LOOP;

    CLOSE filas_cursor;
END $$
DELIMITER ;


-- Ativar o agendador de eventos (executar manualmente uma vez)
SET GLOBAL event_scheduler = ON;

-- Evento para reset diário
CREATE EVENT IF NOT EXISTS evento_reset_diario
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
  CALL resetar_tickets();

-- Evento para reset semanal
CREATE EVENT IF NOT EXISTS evento_reset_semanal
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_DATE + INTERVAL 1 WEEK
DO
  CALL resetar_tickets();

-- Evento para reset mensal
CREATE EVENT IF NOT EXISTS evento_reset_mensal
ON SCHEDULE EVERY 1 MONTH
STARTS DATE_ADD(LAST_DAY(CURRENT_DATE), INTERVAL 1 DAY)
DO
  CALL resetar_tickets();

-- Evento para reset anual
CREATE EVENT IF NOT EXISTS evento_reset_anual
ON SCHEDULE EVERY 1 YEAR
STARTS MAKEDATE(YEAR(CURRENT_DATE) + 1, 1)
DO
  CALL resetar_tickets();
