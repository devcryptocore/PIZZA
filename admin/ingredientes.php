<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css">
<body>
    <button id="add_ingredient">+</button>
    <div class="myIngredients">
        <table class="ingredients_table">
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