<?php
/* INFORMACIÓN GENERAL */
define('VERSION','v1.0.0');
define('SECRET','SECRET');

/* INFORMACIÓN DE LA BASE DE DATOS */
define('DB_HOST','DB_HOST');
define('DB_NAME','DB_NAME');
define('DB_USER','DB_USER');
define('DB_PASS','DB_PASS');

/* INFORMACIÓN DEL SERVIDOR SMTP */
define('SMTP_HOST','SMTP_HOST');
define('SMTP_PORT','SMTP_PORT');
define('SMTP_USER','SMTP_USER');
define('SMTP_PASS','SMTP_PASS');
define('SMTP_EMAIL','SMTP_EMAIL');
define('SMTP_NAME','SMTP_NAME');

/* INFORMACIÓN DE ESTADO DE CONFIGURACION */
define('IS_CONFIG_DATABASE',false);
define('IS_CONFIG_ADMIN',false);
define('IS_CONFIG_EMAIL',false);
define('IS_CONFIG_SITE',false);

/* DIRECTORIOS */
define('APP_FILE',__DIR__ . '/../includes/app.php');
define('THEMES_DIR',__DIR__ . '/../public/themes');
define('TEMPLATES_DIR',__DIR__ . '/../includes/templates');
define('IMG_DIR',__DIR__.'/../public/img');
include APP_FILE;