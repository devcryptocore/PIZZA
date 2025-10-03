<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/products.css" class="versionized">
<script type="module" src="../js/clientes.js" class="versionized"></script>
<style>
    .delthisprd {
        width: 35px;
        aspect-ratio: 1 / 1;
        background: transparent url(../res/icons/delete-red.svg) center / 20px no-repeat;
        cursor: pointer;
        border: 0;
    }
</style>
<body>
    <div class="headbar">
        <h1>Clientes</h1>
        <div class="total-container"></div>
        <div class="search-container">
            <input type="text" id="FiltrarContenido" placeholder="Buscar cliente" class="search-bar">
        </div>
        <button id="add_ingredient"></button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th>ID</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Total Comprado</th>
                <th>Fecha Registro</th>
                <th></th>
            </thead>
            <tbody id="tablaClientes"></tbody>
        </table>
    </div>
</body>
</html>