<?php

    use Dotenv\Dotenv;

    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv -> load();

    $dbhost = $_ENV['DB_HOST'];
    $dbuser = $_ENV['DB_USER'];
    $dbpass = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];
    $clav = base64_encode($_ENV['WORD']);
    $exclav = base64_encode($_ENV['EXWORD']);

    $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $con->set_charset("utf8mb4");
    if($con -> connect_error) {
        die("Error de conexión: " . $con->connect_error);
    }
    
    date_default_timezone_set('America/Bogota');
    $fecha = date('Y-m-d');
    $fullfecha = date('Y-m-d H:i:s');
    $version = time();
    include('../php/logger.php');
    $default_image = "../res/icons/image.svg";

//MaxPizza+2025
?>