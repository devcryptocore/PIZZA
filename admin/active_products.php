<?php
    include('../includes/header.php');
?>
<script src="../js/fitty.min.js"></script>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/active-products.css?v=<?=$version;?>">
<script type="module" src="../js/active-product.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Productos activos</h1>
        <div class="total-container"></div>
        <div class="search-container">
            <input type="text" id="FiltrarContenido" placeholder="Buscar producto" class="search-bar">
        </div>
    </div>
    <div class="myIngredients" id="myMenu"></div>
</body>
</html>