<?php
    include('../includes/verificator.php');
    $version = time();
    function redirectTo($url) {
        header("Location: $url");
        exit();
    }
    if (!isset($_SESSION['usuario'])) {
        redirectTo("../php/logout.php");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css?v=<?=$version;?>">
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../res/izi/css/iziToast.css">
    <link rel="stylesheet" href="../css/leaflet.css">
    <link rel="shortcut icon" href="../res/icons/pizza2.svg" type="image/x-icon">
    <script src="../js/sweetAlert.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../res/izi/js/iziToast.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/exportXLS.js"></script>
    <script src="../js/script.js?v=<?=$version;?>"></script>
    <script type="module" src="../js/validator.js?v=<?=$version;?>"></script>
    <script src="../js/charts.js"></script>
    <script src="../js/mapsJS/leaflet.js"></script>
</head>
<div id="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
    </div>
</div>