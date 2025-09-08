create database spartadb;
use spartadb;
create table socios(
idsocio int primary key auto_increment,
nombre varchar(30),
apellido varchar(30),
direc varchar(30),
telef varchar (15),
email varchar (30));
-- faltó dni

select * from socios;
alter table socios drop fechalta;
alter table socios add column fechalta date  after email ;
alter table socios add column probfis text (200) ;
alter table socios add column planentren boolean; 


CREATE TABLE planes_entrenamiento (
    idplan INT PRIMARY KEY AUTO_INCREMENT,
    idsocio INT NOT NULL,
    tipo_plan ENUM('MASMUSCULAR', 'BAJARPESO', 'OTRO') NOT NULL,
    descripcion_plan VARCHAR(100),
    peso_actual DECIMAL(5,2),
    altura INT,
    disponibilidad_horaria VARCHAR(100),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT TRUE,
    observaciones TEXT,
    FOREIGN KEY (idsocio) REFERENCES socios(idsocio) ON DELETE CASCADE
);
CREATE TABLE seguimiento_entrenamiento (
    idseguimiento INT PRIMARY KEY AUTO_INCREMENT,
    idplan INT NOT NULL,
    fecha_seguimiento DATE NOT NULL,
    peso DECIMAL(5,2),
    medidas TEXT,
    observaciones TEXT,
    FOREIGN KEY (idplan) REFERENCES planes_entrenamiento(idplan) ON DELETE CASCADE
);

CREATE TABLE cuotas (
    idcuota INT(11) AUTO_INCREMENT PRIMARY KEY,
    idsocio INT(11) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_pago DATE,
    estado ENUM('PENDIENTE','PAGADA','VENCIDA') DEFAULT 'PENDIENTE',
    metodo_pago ENUM('EFECTIVO','TARJETA','TRANSFERENCIA','OTRO'),
    observaciones TEXT,
    FOREIGN KEY (idsocio) REFERENCES socios(idsocio)
);

DELIMITER //
CREATE TRIGGER after_socio_insert 
AFTER INSERT ON socios 
FOR EACH ROW
BEGIN
    -- Insertar una cuota automáticamente para el nuevo socio
    INSERT INTO cuotas (
        idsocio,
        monto,
        fecha_emision,
        fecha_vencimiento,
        estado,
        metodo_pago,
        observaciones
    ) VALUES (
        NEW.idsocio,                  -- ID del socio recién insertado
        0.00,                         -- Monto inicial (puedes ajustarlo)
        CURDATE(),                    -- Fecha de emisión (hoy)
        DATE_ADD(CURDATE(), INTERVAL 1 MONTH), -- Fecha de vencimiento (1 mes después)
        'PENDIENTE',                  -- Estado inicial
        NULL,                         -- Método de pago (aún no definido)
        'Cuota generada automáticamente al registrar el socio.'
    );
END //
DELIMITER ;clientes

use spartadb;
create table ejercicios(
idejercicio integer primary key auto_increment, 
nomb_ejer  varchar (50),
descripcion varchar (150),
grupo_mus  ENUM ('PIERNA','BRAZO','PECHO','ESPALDA','HOMBRO','ABDOMEN'),
nivel_dificultad ENUM ('PRINCIPIANTE','INTERMEDIO', 'AVANZADO'),
imagen_ejemplo varchar(200),
video_ejemplo varchar(200));

CREATE TABLE rutinas (
  idrutina INT(11) AUTO_INCREMENT PRIMARY KEY,
  idplan INT(11) NOT NULL,
  nombre VARCHAR(50) NOT NULL,
  descripcion TEXT,
  dia_semana ENUM('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  activa TINYINT(1) DEFAULT 1,
  FOREIGN KEY (idplan) REFERENCES planes_entrenamiento(idplan)
);
 
CREATE TABLE rutina_ejercicios (
    idrutina_ejercicio INT(11) AUTO_INCREMENT PRIMARY KEY,
    idrutina INT(11) NOT NULL,
    idejercicio INT(11) NOT NULL,
    series INT(11) NOT NULL,
    repeticiones VARCHAR(20) NOT NULL,
    descanso_segundos INT(11),
    orden INT(11) NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (idrutina) REFERENCES rutinas(idrutina),
    FOREIGN KEY (idejercicio) REFERENCES ejercicios(idejercicio)
);


CREATE TABLE `rutinas_dias` (
  `iddia` INT AUTO_INCREMENT PRIMARY KEY,
  `idrutina` INT NOT NULL,
  `dia_semana` ENUM('LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO') NOT NULL,
  
  -- Constraint para asegurar que no se pueda añadir el mismo día dos veces a la misma rutina
  UNIQUE KEY `idx_rutina_dia_unico` (`idrutina`, `dia_semana`),
  
  -- Foreign Key para conectar con la tabla de rutinas
  FOREIGN KEY (`idrutina`) 
    REFERENCES `rutinas`(`idrutina`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


alter table rutinas drop column dia_semana; -- se elimino, modificación
alter table rutina_ejercicios drop column idrutina;
alter table rutina_ejercicios add column iddia int (11) not null after idrutina_ejercicio;

CREATE TABLE `rutinas_ejercicios` (
  `idejercicio_rutina` INT AUTO_INCREMENT PRIMARY KEY,
  `iddia` INT NOT NULL,
  `idejercicio` INT NOT NULL,
  `repeticiones` VARCHAR(50) DEFAULT NULL,
  `tiempo_descanso_seg` INT DEFAULT 60,
  `orden` INT DEFAULT 0,
  
  -- Foreign Key para conectar con la tabla de días
  FOREIGN KEY (`iddia`) 
    REFERENCES `rutinas_dias`(`iddia`) 
    ON DELETE CASCADE,
    
  -- Foreign Key para conectar con tu tabla principal de ejercicios
  FOREIGN KEY (`idejercicio`) 
    REFERENCES `ejercicios`(`idejercicio`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `rutinas` 
ADD COLUMN `nivel_dificultad` VARCHAR(50) NOT NULL AFTER `descripcion`;

ALTER TABLE `rutinas` DROP COLUMN `idplan`;
ALTER TABLE `rutinas` DROP FOREIGN KEY `rutinas_ibfk_1`;

CREATE TABLE `socios_rutinas_asignadas` (
  `id_asignacion` INT AUTO_INCREMENT PRIMARY KEY,
  `idsocio` INT NOT NULL,
  `idrutina` INT NOT NULL,
  `fecha_asignacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  
  -- Foreign Key para conectar con la tabla de socios
  FOREIGN KEY (`idsocio`) 
    REFERENCES `socios`(`idsocio`) 
    ON DELETE CASCADE,
    
  -- Foreign Key para conectar con la tabla de rutinas
  FOREIGN KEY (`idrutina`) 
    REFERENCES `rutinas`(`idrutina`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SHOW CREATE TABLE rutinas;

-- 1. Expandir el ENUM de 'tipo_plan' para incluir más objetivos
ALTER TABLE planes_entrenamiento MODIFY COLUMN tipo_plan ENUM(
    'MASMUSCULAR',
    'BAJARPESO',
    'MANTENERPESO',
    'MEJORARRESISTENCIA',
    'PREPARACIONDEPORTE',
    'REHABILITACION',
    'FLEXIBILIDAD',
    'OTRO'
) NOT NULL;

-- 2. Añadir la columna 'nivel_experiencia'
ALTER TABLE planes_entrenamiento ADD COLUMN nivel_experiencia ENUM(
    'PRINCIPIANTE',
    'INTERMEDIO',
    'AVANZADO'
) AFTER altura;


ALTER TABLE planes_entrenamiento ADD COLUMN preferencias_dieteticas varchar(50) AFTER fecha_fin;

ALTER TABLE planes_entrenamiento MODIFY COLUMN tipo_plan VARCHAR(100) NOT NULL;

CREATE TABLE `usuarios` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'empleado', 'socio') NOT NULL,
  `id_socio` INT(11) NULL,
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `fk_usuario_socio`
    FOREIGN KEY (`id_socio`)
    REFERENCES `socios`(`idsocio`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

-- Generando HASH prueba
INSERT INTO `usuarios` (`email`, `password`, `rol`, `id_socio`)
VALUES (
    'enzomoreyra85@gmail.com',
    '$2y$10$gyzUK0h4pyr3vsCxvr0aee0Lvg1.gDRWjBLZslBKKYFkvvL0xYML.',
    'admin',
    136
);

-- CAMBIOS EN SOCIOS. SE AGREGA TABLA NUEVA PERSONAS esta permite diferenciar socios, usuarios, roles y evita redundancia. 
-- porque puede haber socios que no tienen un plan personalizado por otra parte los socios que tienen un plan tienen un usuario y para evitar redundancia y unificar datos

CREATE TABLE personas (
    id_persona INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    dni VARCHAR(20) NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(50) NULL,
    direccion VARCHAR(255) NULL,
    fecha_nacimiento DATE NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- B. Modificar la tabla usuarios para vincularla a personas La tabla usuarios ahora solo se preocupará del acceso.

ALTER TABLE usuarios
ADD COLUMN id_persona INT NULL UNIQUE,
ADD CONSTRAINT fk_usuarios_personas
    FOREIGN KEY (id_persona) REFERENCES personas(id_persona)
    ON DELETE CASCADE;
    
 -- C. Modificar la tabla socios para simplificarla y vincularla a personas La tabla socios ahora solo guardará información específica de la membresía.

ALTER TABLE socios
ADD COLUMN id_persona INT NOT NULL UNIQUE,
ADD CONSTRAINT fk_socios_personas
    FOREIGN KEY (id_persona) REFERENCES personas(id_persona)
    ON DELETE CASCADE,
DROP COLUMN nombre,
DROP COLUMN apellido,
DROP COLUMN dni,
DROP COLUMN direc,
DROP COLUMN telef,
DROP COLUMN email;   

-- Error Code: 1452. Cannot add or update a child row: a foreign key constraint fails (`spartadb`.`#sql-16a4_75`, CONSTRAINT `fk_socios_personas` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE)

-- Paso 1: Desactivar la revisión de claves foráneas
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE seguimiento_entrenamiento;
TRUNCATE TABLE cuotas;
TRUNCATE TABLE planes_entrenamiento;
TRUNCATE TABLE socios_rutinas_asignadas;
TRUNCATE TABLE socios;

-- Paso 3: ¡MUY IMPORTANTE! Volver a activar la revisión de claves foráneas
SET FOREIGN_KEY_CHECKS=1;