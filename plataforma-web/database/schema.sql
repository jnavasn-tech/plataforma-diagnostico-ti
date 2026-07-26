-- =====================================================================
-- Plataforma Web de Diagnóstico de Habilidades y Capacitación Técnica
-- Esquema inicial de base de datos (MySQL 8.0+)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS plataforma_capacitacion
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE plataforma_capacitacion;

-- ---------------------------------------------------------------------
-- Usuarios de la plataforma (jóvenes egresados del nivel diversificado)
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario        INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo   VARCHAR(150)  NOT NULL,
    correo            VARCHAR(150)  NOT NULL UNIQUE,
    contrasena_hash   VARCHAR(255)  NOT NULL,
    edad              TINYINT UNSIGNED NULL,
    departamento      VARCHAR(100)  NULL,
    nivel_diversificado VARCHAR(150) NULL COMMENT 'Carrera cursada en el diversificado',
    fecha_registro    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo            TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Catálogo de habilidades/competencias informáticas evaluables
-- ---------------------------------------------------------------------
CREATE TABLE habilidades (
    id_habilidad      INT AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(120) NOT NULL,
    descripcion       TEXT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Banco de preguntas del módulo de diagnóstico
-- ---------------------------------------------------------------------
CREATE TABLE preguntas_diagnostico (
    id_pregunta       INT AUTO_INCREMENT PRIMARY KEY,
    id_habilidad      INT NOT NULL,
    enunciado         TEXT NOT NULL,
    opcion_a          VARCHAR(255) NOT NULL,
    opcion_b          VARCHAR(255) NOT NULL,
    opcion_c          VARCHAR(255) NOT NULL,
    opcion_d          VARCHAR(255) NOT NULL,
    respuesta_correcta ENUM('A','B','C','D') NOT NULL,
    nivel_dificultad  ENUM('basico','intermedio','avanzado') NOT NULL DEFAULT 'basico',
    FOREIGN KEY (id_habilidad) REFERENCES habilidades(id_habilidad)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Evaluaciones de diagnóstico realizadas por un usuario
-- ---------------------------------------------------------------------
CREATE TABLE evaluaciones_diagnostico (
    id_evaluacion     INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    fecha_evaluacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    puntaje_total     DECIMAL(5,2) NULL COMMENT 'Porcentaje global 0-100',
    resumen_ia        TEXT NULL COMMENT 'Diagnóstico/retroalimentación generado por la IA',
    estado            ENUM('en_progreso','completada') NOT NULL DEFAULT 'en_progreso',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Respuestas individuales dadas dentro de una evaluación
-- ---------------------------------------------------------------------
CREATE TABLE respuestas_usuario (
    id_respuesta      INT AUTO_INCREMENT PRIMARY KEY,
    id_evaluacion     INT NOT NULL,
    id_pregunta       INT NOT NULL,
    opcion_seleccionada ENUM('A','B','C','D') NOT NULL,
    es_correcta       TINYINT(1) NOT NULL,
    FOREIGN KEY (id_evaluacion) REFERENCES evaluaciones_diagnostico(id_evaluacion),
    FOREIGN KEY (id_pregunta) REFERENCES preguntas_diagnostico(id_pregunta)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Catálogo de módulos de capacitación técnica
-- ---------------------------------------------------------------------
CREATE TABLE modulos_capacitacion (
    id_modulo         INT AUTO_INCREMENT PRIMARY KEY,
    id_habilidad      INT NOT NULL,
    titulo            VARCHAR(150) NOT NULL,
    descripcion       TEXT NULL,
    nivel_dificultad  ENUM('basico','intermedio','avanzado') NOT NULL DEFAULT 'basico',
    orden_secuencia   INT NOT NULL DEFAULT 1,
    contenido_url     VARCHAR(255) NULL,
    FOREIGN KEY (id_habilidad) REFERENCES habilidades(id_habilidad)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Ruta de aprendizaje adaptativa generada por la IA para cada usuario
-- ---------------------------------------------------------------------
CREATE TABLE rutas_aprendizaje (
    id_ruta            INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario         INT NOT NULL,
    id_evaluacion       INT NOT NULL,
    fecha_generacion   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    justificacion_ia   TEXT NULL COMMENT 'Explicación generada por la IA sobre la ruta sugerida',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_evaluacion) REFERENCES evaluaciones_diagnostico(id_evaluacion)
) ENGINE=InnoDB;

-- Detalle de módulos incluidos dentro de una ruta de aprendizaje
CREATE TABLE ruta_modulos (
    id_ruta            INT NOT NULL,
    id_modulo          INT NOT NULL,
    orden               INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id_ruta, id_modulo),
    FOREIGN KEY (id_ruta) REFERENCES rutas_aprendizaje(id_ruta),
    FOREIGN KEY (id_modulo) REFERENCES modulos_capacitacion(id_modulo)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Progreso del usuario dentro de cada módulo de capacitación
-- ---------------------------------------------------------------------
CREATE TABLE progreso_usuario (
    id_progreso        INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario          INT NOT NULL,
    id_modulo           INT NOT NULL,
    estado              ENUM('no_iniciado','en_progreso','completado') NOT NULL DEFAULT 'no_iniciado',
    porcentaje_avance   DECIMAL(5,2) NOT NULL DEFAULT 0,
    fecha_inicio        DATETIME NULL,
    fecha_finalizacion  DATETIME NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_modulo) REFERENCES modulos_capacitacion(id_modulo),
    UNIQUE KEY uq_usuario_modulo (id_usuario, id_modulo)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Datos semilla mínimos para poder probar el flujo end-to-end
-- ---------------------------------------------------------------------
INSERT INTO habilidades (nombre, descripcion) VALUES
 ('Ofimática - Word', 'Redacción y formato de documentos'),
 ('Ofimática - Excel', 'Hojas de cálculo y fórmulas básicas'),
 ('Ofimática - PowerPoint', 'Elaboración de presentaciones'),
 ('Ciberseguridad básica', 'Navegación segura en internet');

INSERT INTO preguntas_diagnostico
 (id_habilidad, enunciado, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, nivel_dificultad)
VALUES
 (1, '¿Qué extensión de archivo genera Microsoft Word por defecto?', '.docx', '.xlsx', '.pptx', '.pdf', 'A', 'basico'),
 (2, '¿Qué función de Excel suma un rango de celdas?', 'PROMEDIO()', 'SUMA()', 'CONTAR()', 'BUSCARV()', 'B', 'basico'),
 (3, '¿Cuál es la mejor práctica al recibir un correo con un enlace desconocido?', 'Hacer clic de inmediato', 'Reenviarlo a contactos', 'Verificar el remitente antes de interactuar', 'Ignorar el remitente', 'C', 'basico');

INSERT INTO modulos_capacitacion (id_habilidad, titulo, descripcion, nivel_dificultad, orden_secuencia) VALUES
 (1, 'Fundamentos de Word', 'Formato, estilos y plantillas de documentos', 'basico', 1),
 (2, 'Fundamentos de Excel', 'Fórmulas, tablas y gráficos básicos', 'basico', 1),
 (4, 'Navegación segura en internet', 'Identificación de phishing y buenas prácticas', 'basico', 1);
