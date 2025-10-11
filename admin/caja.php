<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/caja.css?v=<?=$version;?>">
<script type="module" src="../js/caja.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Caja</h1>
        <div class="search-container">
            <button id="boxsearch"></button>
            <input type="date" id="FiltrarBoxes" placeholder="Buscar" class="search-bar">
        </div>
        <button id="transfer" class="stkbutton transfer">Transferir</button>
        <button id="movement" class="stkbutton movement">Movimiento</button>
        <button id="set_entidad" class="stkbutton entityes">Entidades</button>
        <button id="set_box_state" class="stkbutton close_box">Abrir caja</button>
    </div>
    <div class="fondosbar"></div>
    <div class="myIngredients">
        <table class="table-container ingredients_table" id="boxtable">
            <thead>
                <th>Cod.</th>
                <th>Estado</th>
                <th>Base</th>
                <th>Ventas</th>
                <th>Descuentos</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Sucursal</th>
                <th>Usuario</th>
                <th>Fecha</th>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>