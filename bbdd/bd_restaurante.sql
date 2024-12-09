DROP DATABASE if exists db_restaurante02;

CREATE SCHEMA `db_restaurante02` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE db_restaurante02;

-- CREACIÓN TABLA CAMARERO
CREATE TABLE `db_restaurante02`.`camarero` (
  `id_camarero` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `id_roles` INT NOT NULL,
  PRIMARY KEY (`id_camarero`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA ROLES
CREATE TABLE `db_restaurante02`.`roles` (
  `id_roles` INT NOT NULL AUTO_INCREMENT,
  `tipo_roles` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_roles`)
  ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA HISTORIAL
CREATE TABLE `db_restaurante02`.`historial` (
  `id_historial` INT NOT NULL AUTO_INCREMENT,
  `id_camarero` INT NOT NULL,
  `id_mesa` INT NOT NULL,
  `hora_inicio` DATETIME NOT NULL,
  `hora_fin` DATETIME NOT NULL,
  PRIMARY KEY (`id_historial`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA MESA
CREATE TABLE `db_restaurante02`.`mesa` (
  `id_mesa` INT NOT NULL AUTO_INCREMENT,
  `id_sala` INT NOT NULL,
  `libre` TINYINT NOT NULL,
  `num_sillas` INT(2) NOT NULL,
  PRIMARY KEY (`id_mesa`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA SALA
CREATE TABLE `db_restaurante02`.`sala` (
  `id_sala` INT NOT NULL AUTO_INCREMENT,
  `id_tipoSala` INT NOT NULL,
  `nombre_sala` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_sala`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACIÓN TABLA TIPO SALA
CREATE TABLE `db_restaurante02`.`tipo_sala` (
  `id_tipoSala` INT NOT NULL AUTO_INCREMENT,
  `tipo_sala` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_tipoSala`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_unicode_ci;

-- CREACION TABLA STOCK
CREATE TABLE `db_restaurante02`.`stock` (
  `idStock` INT NOT NULL AUTO_INCREMENT,
  `sillas_stock` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idStock`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

ALTER TABLE historial 
ADD COLUMN hora_reserva_inicio TIME,
ADD COLUMN hora_reserva_fin TIME;

-- CREACIÓN FOREIGN KEYS
-- FOREIGN KEYS TABLA HISTORIAL
ALTER TABLE
  `db_restaurante02`.`historial`
ADD
  INDEX `fk_id_camarero_idx` (`id_camarero` ASC) VISIBLE,
ADD
  INDEX `fk_id_mesa_idx` (`id_mesa` ASC) VISIBLE;

ALTER TABLE
  `db_restaurante02`.`historial`
ADD
  CONSTRAINT `fk_id_camarero` FOREIGN KEY (`id_camarero`) REFERENCES `db_restaurante02`.`camarero` (`id_camarero`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD
  CONSTRAINT `fk_id_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `db_restaurante02`.`mesa` (`id_mesa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- FOREIGN KEY TABLA MESA
ALTER TABLE
  `db_restaurante02`.`mesa`
ADD
  INDEX `fk_id_Sala_idx` (`id_sala` ASC) VISIBLE;

ALTER TABLE
  `db_restaurante02`.`mesa`
ADD
  CONSTRAINT `fk_id_Sala` FOREIGN KEY (`id_sala`) REFERENCES `db_restaurante02`.`sala` (`id_sala`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- FOREIGN KEY TABLA TIPO SALA
ALTER TABLE
  `db_restaurante02`.`sala`
ADD
  INDEX `fk_id_tipoSala_idx` (`id_tipoSala` ASC) VISIBLE;

ALTER TABLE
  `db_restaurante02`.`sala`
ADD
  CONSTRAINT `fk_id_tipoSala` FOREIGN KEY (`id_tipoSala`) REFERENCES `db_restaurante02`.`tipo_sala` (`id_tipoSala`) ON DELETE NO ACTION ON UPDATE NO ACTION;
  
ALTER TABLE `db_restaurante02`.`camarero` 
ADD CONSTRAINT `fk_roles_camareros`
  FOREIGN KEY (`id_roles`)
  REFERENCES `db_restaurante02`.`roles` (`id_roles`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

-- Inserts roles
INSERT INTO `db_restaurante02`.`roles` (`id_roles`, `tipo_roles`) VALUES ('1', 'Camarero');
INSERT INTO `db_restaurante02`.`roles` (`id_roles`, `tipo_roles`) VALUES ('2', 'Administrador');
INSERT INTO `db_restaurante02`.`roles` (`id_roles`, `tipo_roles`) VALUES ('3', 'Mantenimiento');
INSERT INTO `db_restaurante02`.`roles` (`id_roles`, `tipo_roles`) VALUES ('4', 'Director');
INSERT INTO `db_restaurante02`.`roles` (`id_roles`, `tipo_roles`) VALUES ('5', 'Gerente');

-- Insert camareros
-- pwd: asdASD123
INSERT INTO `db_restaurante02`.`camarero`(`id_camarero`, `nombre`, `usuario`, `password`, `id_roles`)
VALUES
  (NULL, 'Julio', 'Julio', '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS', 1),
  (NULL, 'Marc M', 'MarcM', '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS', 2),
  (NULL, 'Marc C', 'MarcC', '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS', 1),
  (NULL, 'Juanjo', 'Juanjo', '$2y$10$9YAaDvpj8IDI7WRNVxVq6uYzMnCaUWDGMlU6LS.jv6dgpWcmqcswS', 1);

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
INSERT INTO `db_restaurante02`.`mesa` (`id_sala`, `libre`, `num_sillas`) VALUES ('3', '0', '2');
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
