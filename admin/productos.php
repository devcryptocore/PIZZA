<?php
    include('../includes/header.php');
    if($_SESSION['sucursal'] === 'system'){
        $su12 = "<th>Sucursal</th>";
    }
    else {
        $su12 = "";
    }
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/products.css?v=<?=$version;?>">
<script type="module" src="../js/producto.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Productos</h1>
        <div class="total-container" style="display: none;"></div>
        <div class="search-container">
            <input type="text" id="FiltrarContenido" placeholder="Buscar producto" class="search-bar">
        </div>
        <button class="stkbutton add" id="categories">Categorías</button>
        <button id="get_barcodes"></button>
        <button id="add_ingredient"></button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th>No.</th>
                <th>Producto</th>
                <th>Tam.</th>
                <th>Costo</th>
                <th>Precio</th>
                <th>Ganancia</th>
                <th>Activos</th>
                <th>Stock</th>
                <th>Oferta %</th>
                <th>Categoría</th>
                <?=$su12;?>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>