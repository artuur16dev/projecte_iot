-- ============================================
-- SmartSchool IoT – Esquema de base de dades
-- MySQL / MariaDB
-- ============================================

CREATE DATABASE IF NOT EXISTS smartschool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartschool;

-- Taula: aules
CREATE TABLE IF NOT EXISTS aules (
    id       VARCHAR(10)  PRIMARY KEY,
    nom      VARCHAR(100) NOT NULL,
    planta   INT          DEFAULT 0,
    capacitat INT         DEFAULT 30
);

INSERT INTO aules (id, nom, planta, capacitat) VALUES
    ('A01', 'Aula 1 – Planta baixa', 0, 28),
    ('A02', 'Aula 2 – Planta baixa', 0, 28),
    ('B01', 'Laboratori informàtica', 1, 20),
    ('B02', 'Aula 3 – Primera planta', 1, 25);

-- Taula: alumnes
CREATE TABLE IF NOT EXISTS alumnes (
    id       INT          AUTO_INCREMENT PRIMARY KEY,
    uid_rfid VARCHAR(20)  NOT NULL UNIQUE,
    nom      VARCHAR(100) NOT NULL,
    cognoms  VARCHAR(200) NOT NULL,
    curs     VARCHAR(20)  DEFAULT NULL
);

INSERT INTO alumnes (uid_rfid, nom, cognoms, curs) VALUES
    ('A1B2C3D4', 'Joan',  'García López',  '2nDAM'),
    ('E5F6A7B8', 'Marta', 'Puig Soler',    '2nDAM'),
    ('C9D0E1F2', 'Arnau', 'Vila Mas',      '2nDAM'),
    ('12345678', 'Laia',  'Casas Ferrer',  '2nDAW');

-- Taula: dades_ambientals
CREATE TABLE IF NOT EXISTS dades_ambientals (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    aula_id     VARCHAR(10)  NOT NULL,
    temperatura DECIMAL(5,2) NOT NULL,
    humitat     DECIMAL(5,2) NOT NULL,
    llum        INT          NOT NULL,
    co2         INT          NOT NULL,
    presencia   TINYINT(1)   DEFAULT 0,
    alerta      VARCHAR(50)  DEFAULT 'cap',
    timestamp   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aula_id) REFERENCES aules(id)
);

-- Taula: assistencia
CREATE TABLE IF NOT EXISTS assistencia (
    id          INT         AUTO_INCREMENT PRIMARY KEY,
    aula_id     VARCHAR(10) NOT NULL,
    uid_rfid    VARCHAR(20) NOT NULL,
    alumne_id   INT         DEFAULT NULL,
    timestamp   DATETIME    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aula_id)   REFERENCES aules(id),
    FOREIGN KEY (alumne_id) REFERENCES alumnes(id)
);

-- Vista: última lectura per aula
CREATE OR REPLACE VIEW ultima_lectura_aula AS
    SELECT d.*
    FROM dades_ambientals d
    INNER JOIN (
        SELECT aula_id, MAX(timestamp) AS max_ts
        FROM dades_ambientals
        GROUP BY aula_id
    ) m ON d.aula_id = m.aula_id AND d.timestamp = m.max_ts;

-- Vista: assistència del dia
CREATE OR REPLACE VIEW assistencia_avui AS
    SELECT a.id, a.aula_id, a.uid_rfid,
           CONCAT(al.nom, ' ', al.cognoms) AS alumne,
           al.curs, a.timestamp
    FROM assistencia a
    LEFT JOIN alumnes al ON a.uid_rfid = al.uid_rfid
    WHERE DATE(a.timestamp) = CURDATE()
    ORDER BY a.timestamp DESC;
