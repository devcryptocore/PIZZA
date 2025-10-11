<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/products.css?v=<?=$version;?>">
<script type="module" src="../js/debts.js?v=<?=$version;?>"></script>
<style>
    .debtbody tr td {
        font-size: 10px !important;
    }
    table tr th:nth-child(7){
        border-radius: 0 6px 0 0;
    }
</style>
<body>
    <div class="headbar">
        <h1>Obligaciones</h1>
        <button id="add_ingredient"></button>
    </div>
    <div class="myIngredients">
        <table class="table-container ingredients_table">
            <thead>
                <th>No.</th>
                <th>Concepto</th>
                <th>Valor</th>
                <th>Abonado</th>
                <th>Saldo</th>
                <th>Sucursal</th>
                <th>Fecha</th>
            </thead>
            <tbody id="ingredients"></tbody>
        </table>
    </div>
</body>
</html>