<?php
ob_start();
session_start();

// Siempre UTF-8 en la respuesta
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Zona horaria
date_default_timezone_set('America/Santiago');

 define('DBHOST','201.148.105.43');
 define('DBUSER','chileho1_hramirez');
 define('DBPASS','chileho1_hramirez');
 define('DBNAME','chileho1_kabag');

//application address
define('DIR','http://chilehostit.cl/ot');
define('SITEEMAIL','no-reply@chilehostit.cl');

try {

    // === MYSQLI ===
    $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

    // Forzar UTF-8 en MySQLi
    if (method_exists($mysqli, 'set_charset')) {
        $mysqli->set_charset('utf8mb4');
    } else {
        $mysqli->query("SET NAMES utf8mb4");
    }

    // === PDO ===
    $db = new PDO("mysql:host=".DBHOST.";charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Refuerzo: asegura el charset en la sesi�n PDO
    $db->exec("SET NAMES utf8mb4");

} catch(PDOException $e) {
    // Mostrar error y detener
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

// === Funcion para normalizar texto a UTF-8 ===
if (!function_exists('norm_utf8')) {
    function norm_utf8($s) {
        $s = $s ?? '';
        if ($s !== '' && !mb_check_encoding($s, 'UTF-8')) {
            $s = mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1');
        }
        return $s;
    }
}
 
?>
