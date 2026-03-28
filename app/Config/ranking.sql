CREATE TABLE IF NOT EXISTS ranking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    pista VARCHAR(100) NOT NULL,
    tiempo VARCHAR(20) NOT NULL, -- Formato MM:SS.CCC
    tiempo_ms INT NOT NULL, -- Tiempo en milisegundos para ordenamiento preciso
    foto_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tiempo (tiempo_ms),
    INDEX idx_pista (pista),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);
