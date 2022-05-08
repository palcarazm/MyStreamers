/* CONFIGURACIÓN INICIAL */
        
CREATE TABLE IF NOT EXISTS enlaces
(
  PK_id_enlace INT(3)       NOT NULL AUTO_INCREMENT COMMENT 'ID del tipo de enlace',
  icono        VARCHAR (40) NOT NULL COMMENT 'Icono del tipo de enlace',
  tipo         VARCHAR(50)  NOT NULL COMMENT 'Nombre del tipo de enlace',
  actualizado  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Última actualización',
  PRIMARY KEY (PK_id_enlace)
) COMMENT 'Tabla de tipos de enlaces';

ALTER TABLE enlaces
  ADD CONSTRAINT UQ_tipo UNIQUE (tipo);

CREATE TABLE IF NOT EXISTS modulos
(
  modulo VARCHAR(125) NOT NULL COMMENT 'Nombre del módulo',
  activo BOOLEAN      NOT NULL COMMENT 'Estado de activación del módulo',
  PRIMARY KEY (modulo)
) COMMENT 'Tabla con los estados de los módulos';

CREATE TABLE IF NOT EXISTS opciones
(
  opcion VARCHAR(125) NOT NULL COMMENT 'Nombre de la opción',
  valor  LONGTEXT     NOT NULL COMMENT 'Valor de la opción',
  PRIMARY KEY (opcion)
) COMMENT 'Tabla con las opciones del sitio';

CREATE TABLE IF NOT EXISTS roles
(
  PK_id_rol               INT(1)      NOT NULL AUTO_INCREMENT COMMENT 'ID de rol',
  rol                     VARCHAR(50) NOT NULL COMMENT 'Nombre del rol',
  usuarios_perms          BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos de administración de usuarios',
  participantes_perms     BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos de perfil publico',
  eventos_crear_perms     BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para crear eventos',
  eventos_publicar_perms  BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar eventos',
  noticias_crear_perms    BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para crear noticias',
  noticias_publicar_perms BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar noticias',
  normas_crear_perms      BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para crear normas',
  normas_publicar_perms   BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar normas',
  config_perms            BOOLEAN     NOT NULL DEFAULT 0 COMMENT 'Permisos de configuración del sitio',
  PRIMARY KEY (PK_id_rol)
) COMMENT 'Tabla de roles y permisos';

ALTER TABLE roles
  ADD CONSTRAINT UQ_rol UNIQUE (rol);

CREATE TABLE IF NOT EXISTS users
(
  PK_id_user     INT(11)      NOT NULL AUTO_INCREMENT COMMENT 'ID de usuario',
  username       VARCHAR(50)  NOT NULL COMMENT 'Nombre de usuario',
  email          VARCHAR(320) NOT NULL COMMENT 'Email de usuario',
  pass           CHAR(60)     NOT NULL COMMENT 'Contraseña encriptada',
  FK_id_rol      INT(1)       NOT NULL COMMENT 'ID de rol',
  actualizado    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Última actualización',
  otp            VARCHAR(60)  NULL     COMMENT 'OTP encriptada',
  otp_valid      TIMESTAMP    NULL     COMMENT 'Caducidad del OTP',
  imagen         VARCHAR(50)  NULL     COMMENT 'Ruta a la imagen de perfil',
  bloqueado      BOOLEAN      NOT NULL DEFAULT FALSE COMMENT 'Indicador de bloqueado',
  descripcion    LONGTEXT     NULL     COMMENT 'Descripción del perfil público de usuario',
  perfil_publico BOOLEAN      NULL     COMMENT 'Indicador de perfil público',
  PRIMARY KEY (PK_id_user)
) COMMENT 'Tabla de usuarios';

ALTER TABLE users
  ADD CONSTRAINT UQ_username UNIQUE (username);

ALTER TABLE users
  ADD CONSTRAINT UQ_email UNIQUE (email);

ALTER TABLE users
  ADD CONSTRAINT UQ_imagen UNIQUE (imagen);

CREATE TABLE IF NOT EXISTS users_x_enlaces
(
  FK_id_user   INT(11)       NOT NULL COMMENT 'ID de usuario',
  FK_id_enlace INT(3)        NOT NULL COMMENT 'ID del tipo de enlace',
  enlace       VARCHAR(2083) NOT NULL COMMENT 'Dirrección del enlace'
) COMMENT 'Tabla de enlaces asociados a cada perfil de usuario';

ALTER TABLE users
  ADD CONSTRAINT FK_roles_TO_users
    FOREIGN KEY (FK_id_rol)
    REFERENCES roles (PK_id_rol);

ALTER TABLE users_x_enlaces
  ADD CONSTRAINT FK_enlaces_TO_users_x_enlaces
    FOREIGN KEY (FK_id_enlace)
    REFERENCES enlaces (PK_id_enlace);

ALTER TABLE users_x_enlaces
  ADD CONSTRAINT FK_users_TO_users_x_enlaces
    FOREIGN KEY (FK_id_user)
    REFERENCES users (PK_id_user);

CREATE UNIQUE INDEX user_x_enlaces
  ON users_x_enlaces (FK_id_user ASC, FK_id_enlace ASC);

/* INSERTAR ROLES */
REPLACE INTO `roles` (
  `PK_id_rol`,
  `rol`,
  `usuarios_perms`,
  `participantes_perms`,
  `eventos_crear_perms`,
  `eventos_publicar_perms`,
  `noticias_crear_perms`,
  `noticias_publicar_perms`,
  `normas_crear_perms`,
  `normas_publicar_perms`,
  `config_perms`
)
VALUES
  (
    '1',
    'administrador',
    '1',
    '1',
    '1',
    '1',
    '1',
    '1',
    '1',
    '1',
    '1'
  ),
  (
    '2',
    'helper',
    '0',
    '0',
    '1',
    '0',
    '1',
    '0',
    '1',
    '0',
    '0'
  ),
  (
    '3',
    'participante',
    '0',
    '1',
    '1',
    '0',
    '0',
    '0',
    '0',
    '0',
    '0'
  );
/* INSERTAR TIPO DE ENLACES */
REPLACE INTO `enlaces`(`icono`,`tipo`) VALUES
('fab fa-youtube','YouTube'),
('fab fa-twitch','Twitch'),
('fab fa-instagram','Instagram'),
('fab fa-twitter','Twitter'),
('fab fa-tiktok','TikTok');