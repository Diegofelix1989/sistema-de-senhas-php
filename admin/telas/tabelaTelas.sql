CREATE TABLE telas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    local_id INT NOT NULL,
    fila_id INT NOT NULL,
    tipo_exibicao ENUM('senhas', 'publicidade', 'ambos') NOT NULL,
    FOREIGN KEY (local_id) REFERENCES locais(id),
    FOREIGN KEY (fila_id) REFERENCES filas(id)
);
