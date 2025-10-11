<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/products.css?<?=$version;?>">
<link rel="stylesheet" href="../css/ingredients.css?<?=$version;?>">
<link rel="stylesheet" href="../css/employee.css?<?=$version;?>">
<script type="module" src="../js/employee.js"></script>
<body>
    <div class="headbar">
        <h1>Empleados</h1>
        <button id="new_employee" class="addBtn"></button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th style="border-radius: 6px 0 0 0;">No.</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>E-Mail</th>
                <th>Registrado</th>
                <th style="border-radius: 0 6px 0 0;"></th>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>