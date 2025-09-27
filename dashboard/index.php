<?php
    include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard MaxPizza</title>
    <link rel="stylesheet" href="../css/dashboadrd.css?v=<?=$version;?>">
</head>
<body>
    <div class="parent">
        <div class="div1 head-board"></div>
        <div class="div2 option-container">
            <div class="logo-container"></div>
            <div class="source-option-container">
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Inicio</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Ventas</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Productos</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Ingredientes</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Reportes</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Empleados</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Administración</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Empresa</span>
                </button>
                <button class="source-option">
                    <img src="../res/icons/lupa.svg">
                    <span>Acerca de</span>
                </button>
            </div>
        </div>
        <div class="div3 source-container" id="sources">
            <iframe src="../admin/active_products.php" frameborder="0"></iframe>
        </div>
    </div>
</body>
</html>