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
    <script src="../js/dashboard.js?v=<?=$version;?>"></script>
</head>
<body>
    <div class="parent">
        <div class="div2 option-container">
            <div class="logo-container"></div>
            <div class="source-option-container">
                <button class="source-option" data-source="caja">
                    <img src="../res/icons/sell-reg.svg">
                </button>
                <button class="source-option" data-source="ventas">
                    <img src="../res/icons/money-dollar.svg">
                </button>
                <button class="source-option" data-source="productos">
                    <img src="../res/icons/pizza-dark.svg">
                </button>
                <button class="source-option" data-source="ingredientes">
                    <img src="../res/icons/ingredient.svg">
                </button>
                <button class="source-option" data-source="obligaciones">
                    <img src="../res/icons/debt.svg">
                </button>
                <button class="source-option" data-source="reportes">
                    <img src="../res/icons/report.svg">
                </button>
                <button class="source-option" data-source="empleados">
                    <img src="../res/icons/user.svg">
                </button>
                <button class="source-option" data-source="administracion">
                    <img src="../res/icons/admin.svg">
                </button>
                <button class="source-option" data-source="empresa">
                    <img src="../res/icons/store.svg">
                </button>
                <button class="source-option" data-source="acercade">
                    <img src="../res/icons/about.svg">
                </button>
            </div>
            <button class="source-option" data-source="logout" id="logout_button">
                <img src="../res/icons/exit.svg">
            </button>
        </div>
        <div class="div3 source-container" id="sources">
            <iframe src="../admin/active_products.php" frameborder="0" id="sites_container"></iframe>
        </div>
    </div>
</body>
</html>