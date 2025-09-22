<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    $sesion = "admin";//DUMMIE PARA SESION DE USUARIO

    if(isset($_GET['getingredientsforlist']) && $_GET['getingredientsforlist'] === $clav){
        $st = 100;
        $consul = $con -> prepare("SELECT * FROM ingredientes WHERE stock > ? AND vencimiento > DATE_SUB(CURDATE(), INTERVAL 2 DAY)");
        $consul -> bind_param('d',$st);
        $consul -> execute();
        $Rconsul = $consul -> get_result();
        if($Rconsul -> num_rows > 0){
            $list = "";
            while($ing = mysqli_fetch_array($Rconsul)){
                $list .= '
                    <li class="elem-ingrediente">
                        <div class="chb">
                            <input type="checkbox" data-name="'.$ing['nombre'].'" id="ing_'.$ing['id'].'" name="ingredientes[]" value="'.$ing['id'].'" class="chking">
                            <label for="ing_'.$ing['id'].'">'.$ing['nombre'].'</label>
                        </div>
                        <div class="chcant">
                            <input type="number" name="cantidades['.$ing['id'].']" id="cant_'.$ing['id'].'" placeholder="Cant. '.units($ing['unidad']).'">
                        </div>
                    </li>
                ';
            }
            echo json_encode(["status" => "success", "message" => $list]);
        }
        else {
            echo json_encode(["status" => "success", "message" => "<li>No hay ingredientes registrados</li>"]);
        }
        $consul -> close();
        $con -> close();
    }

    if(isset($_GET['set_new_product']) && $_GET['set_new_product'] === $clav){
        $producto = $_POST['producto'];
        $precio = sanear_string($_POST['precio']);
        $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : "uncategorized";
        $estado = isset($_POST['estado']) ? 1 : 0;
        $oferta = isset($_POST['oferta']) ? 1 : 0;
        $vencimiento = !empty($_POST['vencimiento']) ? $_POST['vencimiento'] : null;
        $ingedientes = [];
        $inprod = $con -> prepare("INSERT INTO productos (producto,  precio, categoria, estado, oferta, vencimiento) VALUES (?, ?, ?, ?, ?, ?)");
        $inprod -> bind_param('sdsiis',$producto,$precio,$categoria,$estado,$oferta,$vencimiento);
        $inprod -> execute();
        if($inprod){
            $idprod = $con -> insert_id;
            $errores = 0;
            $correcto = 0;
            $long = count($_POST['ingredientes']);
            foreach ($_POST['ingredientes'] as $id) {
                $cantidad = intval($_POST['cantidades'][$id]);
                $cns = $con -> prepare("SELECT * FROM ingredientes WHERE id = ?");
                $cns -> bind_param('i',$id);
                $cns -> execute();
                $Rcns = $cns -> get_result();
                if($Rcns -> num_rows > 0){
                    $ig = $Rcns -> fetch_assoc();
                    if($ig['stock'] < $cantidad){
                        echo json_encode([
                            "status" => "error",
                            "title" => "No hay suficiente " . $ig['nombre'],
                            "message" => "No es posible registrar este producto!"
                        ]);
                        exit;
                    }
                    if($ig['vencimiento'] < date('Y-m-d')){
                        echo json_encode([
                            "status" => "error",
                            "title" => $ig['nombre'] . " ya está vencido!",
                            "message" => "No es posible registrar este producto!"
                        ]);
                        exit;
                    }
                    $medida = $ig['unidad'];
                    $costoxgramo = $ig['costo'];
                    $costoprod = $cantidad*$costoxgramo;
                    $ining = $con -> prepare("INSERT INTO product_ingredients (id_product, ingrediente, cantidad, medida, costo) VALUES (?, ?, ?, ?, ?)");
                    $ining -> bind_param('iidsd',$idprod,$id,$cantidad,$medida,$costoprod);
                    $ining -> execute();
                    if($ining){
                        if(isset($estado)){
                            $id_ingr = $ining -> insert_id;
                            $newstock = $ig['stock'] - $cantidad;
                            $uping = $con -> prepare("UPDATE ingredientes SET stock = ? WHERE id = ?");
                            $uping -> bind_param('di',$newstock,$id);
                            $uping -> execute();
                            if($uping -> affected_rows > 0){//Enlazar con longitud del array
                                $correcto += 1;
                            }
                            else {
                                $errores += 1;
                                $dlp = $con -> query("DELETE FROM productos WHERE id = $id");
                            }
                        }
                        else {
                            $correcto += 1;
                        }
                    }
                    else {
                        $dli = $con -> query("DELETE FROM product_ingredients WHERE id_product = $id");
                        $dlp = $con -> query("DELETE FROM productos WHERE id = $id");
                        $errores += 1;
                    }
                }
                else {
                    $errores += 1;
                }
            }
            if($errores === 0){
                echo json_encode([
                    "status" => "success",
                    "title" => "Producto registrado!",
                    "message" => $producto . " se ha registrado con éxito."
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "No se ha registrado el producto!",
                    "message" => "No es posible registrar este producto!"
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "No se ha registrado el producto!",
                "message" => "No es posible registrar este producto!"
            ]);
        }
        $con -> close();
    }

    if(isset($_GET['get_products']) && $_GET['get_products'] === $clav) {
        try {
    $sql = "
        SELECT p.id, p.producto, p.precio, p.categoria, p.estado, p.oferta, p.vencimiento,
               IFNULL(c.costo,0) AS costo,
               (p.precio - IFNULL(c.costo,0)) AS ganancia,
               IFNULL(s.stock_disponible,0) AS stock_disponible
        FROM productos p
        LEFT JOIN (
            SELECT id_product, SUM(costo) AS costo
            FROM product_ingredients
            GROUP BY id_product
        ) c ON c.id_product = p.id
        LEFT JOIN (
            SELECT pi.id_product, MIN(
                CASE 
                  WHEN pi.cantidad > 0 
                  THEN FLOOR(COALESCE(i.stock,0) / pi.cantidad)
                  ELSE 0
                END
            ) AS stock_disponible
            FROM product_ingredients pi
            JOIN ingredientes i ON i.id = pi.ingrediente
            GROUP BY pi.id_product
        ) s ON s.id_product = p.id
        ORDER BY p.fecha_registro DESC
    ";

    $consult = $con->prepare($sql);
    $consult->execute();
    $Rconsult = $consult->get_result();

    if ($Rconsult->num_rows > 0) {
        $productos = "";
        while ($row = $Rconsult->fetch_assoc()) {
            $estado = $row['estado'] == 1 ? "Disponible" : "No disponible";
            $productos .= '
                <tr onclick="openIngredientOptions(\''.$row['producto'].'\',\''.$row['id'].'\')" class="elem-busqueda">
                    <td>'.$row['id'].'</td>
                    <td>'.$row['producto'].'</td>
                    <td>'.miles(round($row['costo'])).'</td>
                    <td>$'.miles($row['precio']).'</td>
                    <td>$'.miles(round($row['ganancia'])).'</td>
                    <td>'.$row['stock_disponible'].'</td>
                    <td>'.$row['categoria'].'</td>
                    <td>'.$estado.'</td>
                    <td>'.$row['vencimiento'].'</td>
                </tr>
            ';
        }

        echo json_encode([
            "status"  => "success",
            "title"   => "Correcto!",
            "message" => $productos
        ]);
    } else {
        echo json_encode([
            "status"  => "success",
            "title"   => "Vacío",
            "message" => "<h2 class=\"center_text\">Sin productos registrados.</h2>"
        ]);
    }

    $consult->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        "status"  => "error",
        "title"   => "Error!",
        "message" => "Ha ocurrido un error: " . $e->getMessage()
    ]);
}
$con->close();

    }

?>