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
        $estado = 0;
        $oferta = isset($_POST['oferta']) ? 1 : 0;
        $size = isset($_POST['size']) ? $_POST['size'] : "innecesario";
        $descripcion = $_POST['descripcion'];
        $identificador = uniqid();
        $dir = "../res/images/products/" . sanear_string($categoria) . preg_replace('/[^a-zA-Z0-9-_]/', '_', $producto) . "_" . $identificador;
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $portada = guardarFoto("portada", $producto, $dir);
        $photo1 = guardarFoto("photo1", $producto, $dir);
        $photo2 = guardarFoto("photo2", $producto, $dir);
        $photo3 = guardarFoto("photo3", $producto, $dir);//NO SE ESTÁN SUBIENDO LAS IMAGENES REVISAR!!!!
        $ingedientes = [];
        $inprod = $con -> prepare("INSERT INTO productos (producto,  precio, categoria, descripcion, talla, estado, oferta, portada, foto_1, foto_2, foto_3)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $inprod -> bind_param('sisssiissss',$producto,$precio,$categoria,$descripcion,$size,$estado,$oferta,$portada,$photo1,$photo2,$photo3);
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
                    $ining -> bind_param('iiisi',$idprod,$id,$cantidad,$medida,$costoprod);
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
                SELECT p.id, p.producto, p.precio, p.categoria, p.estado, p.oferta,
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
                        <tr onclick="openProductOptions(\''.$row['producto'].'\',\''.$row['id'].'\')" class="elem-busqueda">
                            <td>'.$row['id'].'</td>
                            <td>'.$row['producto'].'</td>
                            <td>'.miles(round($row['costo'])).'</td>
                            <td>$'.miles($row['precio']).'</td>
                            <td>$'.miles(round($row['ganancia'])).'</td>
                            <td>'.$row['stock_disponible'].'</td>
                            <td>'.$row['categoria'].'</td>
                            <input type="hidden" id="disponibles_'.$row['id'].'" value="'.$row['stock_disponible'].'">
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

    if(isset($_GET['get_this_product']) && $_GET['get_this_product'] === $clav) {
        $id = $_POST['id'];
        $conpr = $con -> prepare("SELECT * FROM productos WHERE id = ?");
        $conpr -> bind_param('i',$id);
        $conpr -> execute();
        $Rconpr = $conpr -> get_result();
        if($Rconpr -> num_rows > 0) {
            $prod = $Rconpr -> fetch_assoc();
            $res = [
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "id" => $prod['id'],
                    "producto" => $prod['producto'],
                    "precio" => $prod['precio'],
                    "categoria" => $prod['categoria'],
                    "estado" => $prod['estado'],
                    "portada" => $prod['portada'] ?? $default_image,
                    "foto1" => $prod['foto_1'] ?? $default_image,
                    "foto2" => $prod['foto_2'] ?? $default_image,
                    "foto3" => $prod['foto_3'] ?? $default_image
                ]
            ];
            echo json_encode($res);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "No Data",
                "message" => "No hay datos para este producto!"
            ]);
        }
    }

    if(isset($_GET['active_this_product']) && $_GET['active_this_product'] === $clav) {
        $id = $_POST['id'];
        $cant = $_POST['cantid'];//Cantidad de pizzas a preparar
        $porciones = isset($_POST['porciones']) && $_POST['porciones'] > 0 ? $_POST['porciones'] : 0;//Número de porciones, 1 si no se envía
        $oferta = isset($_POST['oferta']) ? $_POST['oferta'] : 0;
        $ingreds = $con ->  prepare("SELECT * FROM product_ingredients WHERE id_product = ?");
        $ingreds -> bind_param('i',$id);
        $ingreds -> execute();
        $Ringreds = $ingreds -> get_result();
        $errores = [];
        if($Ringreds -> num_rows > 0) {
            while($ing = mysqli_fetch_array($Ringreds)){
                $idprod = $ing['id_product'];
                $ingrediente = $ing['ingrediente'];
                $icantidad = $ing['cantidad'];//Cantidad de gramos del un ingrediente requerido para preparar la pizza
                $cantxproduct = $icantidad*$cant;
                if($porciones > 0) {
                    $division = 1/8;//0.125 Equivale al valor por porción
                    $cant = $division*$porciones;//0.125*4=0.5 Equivale al valor de las porciones
                    $cantxproduct = $icantidad * $cant;//Calcular por porciones solo si es pizza 100*0.5 Equivale al calculo de ingrediente por valor de porciones
                }
                $consing = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
                $consing -> bind_param('i',$ingrediente);
                $consing -> execute();
                $Rconsing = $consing -> get_result();
                if($Rconsing -> num_rows > 0){
                    $ici = $Rconsing->fetch_assoc();
                    $stoking = $ici['stock'] - $cantxproduct;
                    $upd = $con -> prepare("UPDATE ingredientes SET stock = ? WHERE id = ?");
                    $upd -> bind_param('di', $stoking,$ingrediente);
                    if($upd -> execute()){
                        if($upd -> affected_rows > 0){
                            
                        }
                        else {
                            $errores[] = "Ingrediente {$ici['nombre']} no tuvo cambios en stock. - ";
                        }
                    }
                    else {
                        $errores[] = "Error en ingrediente {$ici['nombre']} - " . $upd->error;
                    }
                }
                else {
                    $errores[] = "Error en ingrediente $ingrediente - " . $upd->error;
                }
            }
            if(count($errores) === 0) {
                $conprod = $con -> prepare("SELECT * FROM productos WHERE id = ?");
                $conprod -> bind_param('i',$id);
                $conprod -> execute();
                $Rconprod = $conprod -> get_result();
                $prod = $Rconprod -> fetch_assoc();
                $nombreprod = $prod['producto'];
                $precioprod = $prod['precio']*$cant;
                if($porciones  > 0) {
                    $dvs = 1/8;
                    $dvs = $dvs * $porciones;
                    $precioprod = $prod['precio'] * $dvs;
                    $precioprod = $precioprod / $porciones;//Precio por porción
                }
                $gprod = $con -> prepare("INSERT INTO active_products (id_producto,unidades,porciones,precio) VALUES (?, ?, ?, ?)");
                $gprod -> bind_param('idii',$id,$cant,$porciones,$precioprod);
                $gprod -> execute();
                if($gprod){
                    $estado = 1;
                    $conestado = $con -> prepare("SELECT estado FROM productos WHERE estado = ?");
                    $conestado -> bind_param('i',$estado);
                    $conestado -> execute();
                    $Rconestado = $conestado -> get_result();
                    if($Rconestado -> num_rows == 0){
                        $upprods = $con -> prepare("UPDATE productos SET estado = ?, oferta = ? WHERE id = ?");
                        $upprods -> bind_param('iii',$estado,$oferta,$id);
                        $upprods -> execute();
                        if($upprods -> affected_rows > 0){
                            echo json_encode([
                                "status" => "success",
                                "title" => "Producto activado",
                                "message" => "Se han activado " . $cant . " de unidades para " . $nombreprod
                            ]);
                        }
                        else {
                            echo json_encode([
                                "status" => "error",
                                "title" => "No se ha activado el producto!",
                                "message" => "No se ha podido cambiar de estado".$cant
                            ]);
                        }
                    }
                    else {
                        echo json_encode([
                            "status" => "success",
                            "title" => "Producto activado",
                            "message" => "Se han activado " . $cant . " de unidades para " . $nombreprod
                        ]);
                    }
                }
            }
            else {
                $errors = "";
                foreach ($errores as $e) {
                    $errors .= $e;
                }
                echo json_encode([
                    "status" => "error",
                    "title" => "No se ha activado el producto!",
                    "message" => $errors
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "No se ha activado el producto!",
                "message" => "No se ha encontrado ingredientes para este producto."
            ]);
        }
    }

?>