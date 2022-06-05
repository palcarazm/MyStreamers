<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/functions.php';
setlocale(LC_TIME,'spanish.UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('UTC');
error_reporting(0);
error_reporting(E_ALL);