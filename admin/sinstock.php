<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<script type="module" src="../js/stockminimo.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Por agotar</h1>
        <div class="total-container" style="display: none;"></div>
        <div class="search-container">
            <input type="text" id="FiltrarContenido" placeholder="Buscar insumo" class="search-bar">
        </div>
        <button id="add_ingredient" style="display: none;"></button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th>No.</th>
                <th>Insumo</th>
                <th>Unidad</th>
                <th>Stock</th>
                <th>Mínimo</th>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>