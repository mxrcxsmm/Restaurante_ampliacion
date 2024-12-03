-- ELIMINAR BASE DE DATOS SI EXISTE
DROP SCHEMA IF EXISTS `db_restaurante02`;

-- CREAR BASE DE DATOS
CREATE SCHEMA `db_restaurante02` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE db_restaurante02;

-- CREACIÓN TABLA CAMARERO
CREATE TABLE `db_restaurante02`.`camarero` (
  `id_camarero` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_camarero`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA USUARIO (CRUD de usuarios)
CREATE TABLE `db_restaurante02`.`usuario` (
  `id_usuario` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `rol` ENUM('camarero', 'gerente', 'mantenimiento') NOT NULL,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA TURNOS
CREATE TABLE `db_restaurante02`.`turno` (
  `id_turno` INT NOT NULL AUTO_INCREMENT,
  `nombre_turno` VARCHAR(50) NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA RESERVAS
CREATE TABLE `db_restaurante02`.`reserva` (
  `id_reserva` INT NOT NULL AUTO_INCREMENT,
  `id_camarero` INT NOT NULL,
  `id_turno` INT NOT NULL,
  `fecha` DATE NOT NULL,
  PRIMARY KEY (`id_reserva`),
  CONSTRAINT `fk_reserva_camarero` FOREIGN KEY (`id_camarero`) REFERENCES `db_restaurante02`.`camarero` (`id_camarero`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reserva_turno` FOREIGN KEY (`id_turno`) REFERENCES `db_restaurante02`.`turno` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA TIPO SALA
CREATE TABLE `db_restaurante02`.`tipo_sala` (
  `id_tipoSala` INT NOT NULL AUTO_INCREMENT,
  `tipo_sala` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_tipoSala`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA SALA
CREATE TABLE `db_restaurante02`.`sala` (
  `id_sala` INT NOT NULL AUTO_INCREMENT,
  `id_tipoSala` INT NOT NULL,
  `nombre_sala` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_sala`),
  CONSTRAINT `fk_id_tipoSala` FOREIGN KEY (`id_tipoSala`) REFERENCES `db_restaurante02`.`tipo_sala` (`id_tipoSala`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA MESA
CREATE TABLE `db_restaurante02`.`mesa` (
  `id_mesa` INT NOT NULL AUTO_INCREMENT,
  `id_sala` INT NOT NULL,
  `libre` TINYINT NOT NULL,
  `num_sillas` INT(2) NOT NULL,
  PRIMARY KEY (`id_mesa`),
  CONSTRAINT `fk_id_Sala` FOREIGN KEY (`id_sala`) REFERENCES `db_restaurante02`.`sala` (`id_sala`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA HISTORIAL
CREATE TABLE `db_restaurante02`.`historial` (
  `id_historial` INT NOT NULL AUTO_INCREMENT,
  `id_camarero` INT NOT NULL,
  `id_mesa` INT NOT NULL,
  `hora_inicio` DATETIME NOT NULL,
  `hora_fin` DATETIME NOT NULL,
  PRIMARY KEY (`id_historial`),
  CONSTRAINT `fk_id_camarero` FOREIGN KEY (`id_camarero`) REFERENCES `db_restaurante02`.`camarero` (`id_camarero`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_id_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `db_restaurante02`.`mesa` (`id_mesa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA STOCK
CREATE TABLE `db_restaurante02`.`stock` (
  `idStock` INT NOT NULL AUTO_INCREMENT,
  `sillas_stock` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idStock`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- Relación entre usuario y camarero (añadir campo en tabla usuario)
ALTER TABLE `db_restaurante02`.`usuario`
ADD COLUMN `id_camarero` INT,
ADD CONSTRAINT `fk_usuario_camarero` FOREIGN KEY (`id_camarero`) REFERENCES `db_restaurante02`.`camarero` (`id_camarero`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Relación entre mesa y stock (añadir campo en tabla mesa)
ALTER TABLE `db_restaurante02`.`mesa`
ADD COLUMN `id_stock` INT,
ADD CONSTRAINT `fk_mesa_stock` FOREIGN KEY (`id_stock`) REFERENCES `db_restaurante02`.`stock` (`idStock`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Relación entre historial y turno (añadir campo en tabla historial)
ALTER TABLE `db_restaurante02`.`historial`
ADD COLUMN `id_turno` INT,
ADD CONSTRAINT `fk_historial_turno` FOREIGN KEY (`id_turno`) REFERENCES `db_restaurante02`.`turno` (`id_turno`) ON DELETE NO ACTION ON UPDATE NO ACTION;


-- INSERTAR TURNOS
INSERT INTO `db_restaurante02`.`turno` (`nombre_turno`, `hora_inicio`, `hora_fin`) VALUES
('Mediodía - Primer turno', '12:00:00', '14:00:00'),
('Mediodía - Segundo turno', '14:00:00', '16:00:00'),
('Noche - Primer turno', '20:00:00', '22:00:00'),
('Noche - Segundo turno', '22:00:00', '00:00:00');

-- Insert camareros
-- pwd: asdASD123
INSERT INTO
  `camarero` (`id_camarero`, `nombre`, `usuario`, `password`)
VALUES
  (
    NULL,
    'Julio',
    'Julio',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS'
  ),
  (
    NULL,
    'Marc M',
    'MarcM',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS'
  ),
  (
    NULL,
    'Marc C',
    'MarcC',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS'
  ),
  (
    NULL,
    'Juanjo',
    'Juanjo',
    '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS'
  );

-- Insert tipo sala
INSERT INTO `db_restaurante02`.`tipo_sala` (`tipo_sala`) VALUES ('Terraza');
INSERT INTO `db_restaurante02`.`tipo_sala` (`tipo_sala`) VALUES ('Comedor');
INSERT INTO `db_restaurante02`.`tipo_sala` (`tipo_sala`) VALUES ('Sala privada');

-- Insert salas
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('1', 'Terraza principal');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('1', 'Terraza este');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('1', 'Terraza oeste');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('2', 'Comedor 1 PB');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('2', 'Comedor 2 P1');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('3', 'Sala privada PB');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('3', 'Sala privada 1 P1');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('3', 'Sala privada 2 P1');
INSERT INTO `db_restaurante02`.`sala` (`id_tipoSala`, `nombre_sala`) VALUES ('3', 'Sala privada 3 P1');

-- Insert mesas
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('1', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('2', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('4', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('5', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('6', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('7', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('8', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '4');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '2');
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('9', '0', '2');

-- Insert stock
INSERT INTO `db_restaurante02`.`stock` (`sillas_stock`) VALUES ('30');
