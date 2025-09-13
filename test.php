<?php
    $insumo = "Carne";
    $cantidad_gramos = 1000;
    $precio_insumo = 25000;

    $insumo2 = "Queso";
    $cantidad_gramos2 = 500;
    $precio_insumo2 = 30000;

    $insumo3 = "Masa";
    $cantidad_gramos3 = 1500;
    $precio_insumo3 = 20000;

    //Calcular el costo por gramo del insumo
    function calculate_gramos($cantidad, $precio){
        $valorgramo = $precio/$cantidad;
        return $valorgramo;
    }

    $gramos_carne = 400*calculate_gramos($cantidad_gramos,$precio_insumo);//10.000
    $gramos_queso = 200*calculate_gramos($cantidad_gramos2,$precio_insumo2);//12.000
    $gramos_masa = 700*calculate_gramos($cantidad_gramos3,$precio_insumo3);//9.333
    $valor_pizza = 50000;
    $precio_costo = $gramos_carne+$gramos_queso+$gramos_masa;
    $ganancia = $valor_pizza-$precio_costo;

    

    echo "El costo de la pizza es de: $".$precio_costo;
    echo "<br>La ganancia es de: $".$ganancia;

?>