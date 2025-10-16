<?php
    include('../includes/verificator.php');
    $version = time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1f1f1f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NextFlow App">
    <link rel="stylesheet" href="../css/main.css?v=<?=$version;?>">
    <link rel="stylesheet" href="../css/sweetAlert.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../res/izi/css/iziToast.css">
    <link rel="shortcut icon" href="../res/icons/pizza2.svg" type="image/x-icon">
    <title>Dashboard MaxPizza</title>
    <link rel="stylesheet" href="../css/dashboadrd.css?v=<?=$version;?>">
    <script type="module" src="../js/dashboard.js?v=<?=$version;?>"></script>
    <script src="../js/sweetAlert.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../res/izi/js/iziToast.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/exportXLS.js"></script>
    <script src="../js/script.js?v=<?=$version;?>"></script>
    <script type="module" src="../js/validator.js?v=<?=$version;?>"></script>
    <script src="../js/charts.js"></script>

</head>
<div id="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
    </div>
</div>