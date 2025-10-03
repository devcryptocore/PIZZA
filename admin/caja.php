<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<script type="module" src="../js/caja.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Caja</h1>
        <div class="total-container"></div>
        <div class="search-container">
            <input type="text" id="FiltrarContenido" placeholder="Buscar insumo" class="search-bar">
        </div>
        <button id="set_entidad" class="stkbutton add">Entidades</button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th>No.</th>
                <th>Insumo</th>
                <th>Costo</th>
                <th>Unidad</th>
                <th>Stock</th>
                <th>Mínimo</th>
                <th>Precio und</th>
                <th>Precio stock</th>
                <th>Caduca</th>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>