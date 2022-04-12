/* TABLA DE OPCIONES */
CREATE TABLE IF NOT EXISTS opciones
(
  opcion VARCHAR(125) NOT NULL COMMENT 'Nombre de la opción',
  valor  LONGTEXT     NOT NULL COMMENT 'Valor de la opción',
  PRIMARY KEY (opcion)
) COMMENT 'Tabla con las opciones del sitio';
/* TABLA DE MÓDULOS*/
CREATE TABLE IF NOT EXISTS modulos
(
  modulo VARCHAR(125) NOT NULL COMMENT 'Nombre del módulo',
  activo BOOLEAN      NOT NULL COMMENT 'Estado de activación del módulo',
  PRIMARY KEY (modulo)
) COMMENT 'Tabla con los estados de los módulos';
/* TABLA DE ROLES */
CREATE TABLE IF NOT EXISTS roles(
  PK_id_rol INT(1) NOT NULL AUTO_INCREMENT COMMENT 'ID de rol',
  rol VARCHAR(50) NOT NULL COMMENT 'Nombre del rol',
  usuarios_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos de administración de usuarios',
  participantes_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos de perfil publico',
  eventos_crear_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para crear eventos',
  eventos_publicar_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar eventos',
  noticias_crear_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para crear noticias',
  noticias_publicar_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar noticias',
  normas_crear_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para crear normas',
  normas_publicar_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos para publicar normas',
  config_perms BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Permisos de configuración del sitio',
  actualizado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Última actualización',
  PRIMARY KEY (PK_id_rol),
  CONSTRAINT UQ_rol UNIQUE (rol)
) COMMENT 'Tabla de roles y permisos';
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
  /* TABLA DE USUARIOS */
  CREATE TABLE IF NOT EXISTS users(
  PK_id_user  INT(11)      NOT NULL AUTO_INCREMENT COMMENT 'ID de usuario',
  username    VARCHAR(50)  NOT NULL COMMENT 'Nombre de usuario',
  email       VARCHAR(320) NOT NULL COMMENT 'Email de usuario',
  pass        CHAR(60)     NOT NULL COMMENT 'Contraseña encriptada',
  FK_id_rol   INT(1)       NOT NULL COMMENT 'ID de rol',
  actualizado TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Última actualización',
  otp         VARCHAR(60)  NULL     COMMENT 'OTP encriptada',
  otp_valid   TIMESTAMP    NULL     COMMENT 'Caducidad del OTP',
  imagen      VARCHAR(40)  NULL     COMMENT 'Ruta a la imagen de perfil',
  bloqueado   BOOLEAN      NOT NULL DEFAULT FALSE COMMENT 'Indicador de bloqueado',
  PRIMARY KEY (PK_id_user)
) COMMENT 'Tabla de usuarios';