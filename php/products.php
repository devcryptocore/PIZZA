<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    $sesion = "admin";//DUMMIE PARA SESION DE USUARIO
    include('../includes/verificator.php');

    

    if(isset($_GET['getingredientsforlist']) && $_GET['getingredientsforlist'] === $clav){
        $consul = $con -> prepare("SELECT * FROM ingredientes WHERE vencimiento > DATE_SUB(CURDATE(), INTERVAL 2 DAY) AND sucursal = ?");
        $consul -> bind_param('s',$sucursal);
        $consul -> execute();
        $Rconsul = $consul -> get_result();
        if($Rconsul -> num_rows > 0){
            $list = "";
            while($ing = mysqli_fetch_array($Rconsul)){
                $list .= '
                    <li class="elem-ingrediente">
                        <div class="chb">
                            <input type="checkbox" data-name="'.sanear_string($ing['nombre']).'" id="ing_'.$ing['id'].'" name="ingredientes[]" value="'.$ing['id'].'" class="chking">
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
        $campos = ["producto","precio"];
        $anomalia = validarFormulario($_POST,$campos);
        if(!empty($anomalia)){
            $errs = "";
            foreach($anomalia as $a){
                $errs .= $a . "<br>";
            }
            die(json_encode([
                "status" => "error",
                "title" => "Campos incompletos",
                "message" => $a
            ]));
        }
        $dir = "../res/images/products/" . sanear_string($categoria) . preg_replace('/[^a-zA-Z0-9-_]/', '_', $producto);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        $portada = guardarFoto("portada", $producto, $dir);
        $photo1 = guardarFoto("photo1", $producto, $dir);
        $photo2 = guardarFoto("photo2", $producto, $dir);
        $photo3 = guardarFoto("photo3", $producto, $dir);//NO SE ESTÁN SUBIENDO LAS IMAGENES REVISAR!!!!
        $ingedientes = [];
        $inprod = $con -> prepare("INSERT INTO productos (producto,  precio, categoria, descripcion, talla, estado, oferta, portada, foto_1, foto_2, foto_3, sucursal, usuario)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $inprod -> bind_param('sisssiissssss',$producto,$precio,$categoria,$descripcion,$size,$estado,$oferta,$portada,$photo1,$photo2,$photo3,$sucursal,$sesion);
        $inprod -> execute();
        if($inprod){
            $idprod = $con -> insert_id;
            $errores = 0;
            $correcto = 0;
            $revstock = 0;
            $long = count($_POST['ingredientes']);
            foreach ($_POST['ingredientes'] as $id) {
                $cantidad = intval($_POST['cantidades'][$id]);
                $cns = $con -> prepare("SELECT * FROM ingredientes WHERE id = ? AND sucursal = ?");
                $cns -> bind_param('is',$id,$sucursal);
                $cns -> execute();
                $Rcns = $cns -> get_result();
                if($Rcns -> num_rows > 0){
                    $ig = $Rcns -> fetch_assoc();
                    if($ig['stock'] < $cantidad){
                        echo json_encode([
                            "status" => "error",
                            "title" => "No hay suficiente " . $ig['nombre'],
                            "message" => "No es posible registrar este producto!"//EL PRECIO DEL PRODUCTO DEBE SER POR UNIDAD
                        ]);
                        $xdel = $con -> query("DELETE FROM productos WHERE id = $idprod");
                        exit;
                    }
                    if($ig['vencimiento'] < date('Y-m-d')){
                        echo json_encode([
                            "status" => "error",
                            "title" => $ig['nombre'] . " ya está vencido!",
                            "message" => "No es posible registrar este producto!"
                        ]);
                        $xdel = $con -> query("DELETE FROM productos WHERE id = $idprod");
                        exit;
                    }
                    $medida = $ig['unidad'];
                    $costoxgramo = $ig['costo'];
                    $costoprod = $cantidad*$costoxgramo;
                    $ining = $con -> prepare("INSERT INTO product_ingredients (id_product, ingrediente, cantidad, medida, costo, sucursal, usuario) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $ining -> bind_param('iiisiss',$idprod,$id,$cantidad,$medida,$costoprod,$sucursal,$sesion);
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
                SELECT p.id, p.producto, p.precio, p.categoria, p.talla, p.estado, p.oferta,
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
                    JOIN ingredientes i ON i.id = pi.ingrediente WHERE pi.sucursal = ?
                    GROUP BY pi.id_product
                ) s ON s.id_product = p.id WHERE p.sucursal = ?
                ORDER BY p.fecha_registro DESC
            ";
            $consult = $con->prepare($sql);
            $consult -> bind_param('ss',$sucursal,$sucursal);
            $consult->execute();
            $Rconsult = $consult->get_result();
            if ($Rconsult->num_rows > 0) {
                $productos = "";
                while ($row = $Rconsult->fetch_assoc()) {
                    $estado = $row['estado'] == 1 ? "Disponible" : "No disponible";

                    if($row['estado'] == 1){
                        $stt = '
                            <span class="active_ball" title="Producto activo"></span>
                        ';
                        $estado = "Disponible";
                    }
                    else {
                        $stt = '';
                        $estado = "No disponible";
                    }

                    if($row['oferta'] == 1){
                        $oferta = '
                            <span class="offer_image"></span>
                        ';
                    }
                    else {
                        $oferta = "";
                    }
                    $idf = $row['id'];
                    $srl = $con -> query("SELECT * FROM product_ingredients WHERE id_product = $idf");
                    $xs = $srl -> fetch_assoc();
                    $costo = $row['costo'];
                    $ganancia = $row['ganancia'];
                    if($xs['medida'] == 'unidad'){
                        $costo = $costo / $xs['cantidad'];
                        $ganancia = $row['precio'] - $costo;
                    }

                    $productos .= '
                        <tr onclick="openProductOptions(\''.$row['producto'].'\',\''.$row['id'].'\',\''.$row['talla'].'\')" class="elem-busqueda">
                            <td style="position:relative;">'.$stt.$row['id'].'</td>
                            <td>'.$row['producto'].'</td>
                            <td>'.miles(round($costo)).'</td>
                            <td>$'.miles($row['precio']).'</td>
                            <td>$'.miles(round($ganancia)).'</td>
                            <td>'.$xs['cantidad'].'</td>
                            <td>'.$row['stock_disponible'].'</td>
                            <td style="position:relative;">'.$row['categoria'].$oferta.'</td>
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
        $conpr = $con -> prepare("SELECT * FROM productos WHERE id = ? AND sucursal = ?");
        $conpr -> bind_param('is',$id,$sucursal);
        $conpr -> execute();
        $Rconpr = $conpr -> get_result();
        if($Rconpr -> num_rows > 0) {
            $prod = $Rconpr -> fetch_assoc();
            $conig = $con -> prepare("SELECT * FROM product_ingredients WHERE id_product = ? AND sucursal = ?");
            $conig -> bind_param('is',$id,$sucursal);
            $conig -> execute();
            $Rconig = $conig -> get_result();
            $ingreds = [];
            if($Rconig -> num_rows > 0) {
                while($in = mysqli_fetch_array($Rconig)){
                    $ingreds[] = [
                        "ingrediente" => $in['ingrediente'],
                        "cantidad" => $in['cantidad'],
                        "medida" => $in['medida'],
                        "costo" => $in['costo']
                    ];
                }
            }
            $res = [
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "id" => $prod['id'],
                    "producto" => $prod['producto'],
                    "precio" => $prod['precio'],
                    "categoria" => $prod['categoria'],
                    "estado" => $prod['estado'],
                    "talla" => $prod['talla'],
                    "oferta" => $prod['oferta'],
                    "descripcion" => $prod['descripcion'],
                    "activos" => $ingreds,
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
        $cant = isset($_POST['cantid']) ? $_POST['cantid'] : 1;//Cantidad de pizzas a preparar
        $porciones = isset($_POST['porciones']) && $_POST['porciones'] > 0 ? $_POST['porciones'] : 0;//Número de porciones, 1 si no se envía
        $oferta = isset($_POST['oferta']) ? $_POST['oferta'] : 0;
        $ingreds = $con ->  prepare("SELECT * FROM product_ingredients WHERE id_product = ? AND sucursal = ?");
        $ingreds -> bind_param('is',$id,$sucursal);
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
                $consing = $con->prepare("SELECT * FROM ingredientes WHERE id = ? AND sucursal = ?");
                $consing -> bind_param('is',$ingrediente,$sucursal);
                $consing -> execute();
                $Rconsing = $consing -> get_result();
                if($Rconsing -> num_rows > 0){
                    $ici = $Rconsing->fetch_assoc();
                    if($ici['stock'] < $cantxproduct) {
                        die(
                            json_encode([
                                "status" => "error",
                                "title" => "Unidades no disponibles!",
                                "message" => "Las unidades de " . $ici['nombre'] . " no son suficientes"
                            ])
                        );
                    }
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
                $conprod = $con -> prepare("SELECT * FROM productos WHERE id = ? AND sucursal = ?");
                $conprod -> bind_param('is',$id,$sucursal);
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
                $consuacpro = $con -> prepare("SELECT id FROM active_products WHERE id_producto = ? AND sucursal = ?");
                $consuacpro -> bind_param('is',$id,$sucursal);
                $consuacpro -> execute();
                $Rconsuacpro = $consuacpro -> get_result();
                if($Rconsuacpro -> num_rows > 0){
                    $gprod = $con -> prepare("UPDATE active_products SET unidades = COALESCE(unidades, 0) + ?,
                    porciones = COALESCE(porciones) + ? WHERE id_producto = ?");
                    $gprod -> bind_param('dii',$cant,$porciones,$id);
                }
                else {
                    $gprod = $con -> prepare("INSERT INTO active_products (id_producto,unidades,porciones,precio,sucursal,usuario) VALUES (?, ?, ?, ?, ?, ?)");
                    $gprod -> bind_param('idiiss',$id,$cant,$porciones,$precioprod,$sucursal,$sesion);
                }
                $gprod -> execute();
                if($gprod){
                    $estado = 1;
                    $conestado = $con -> prepare("SELECT estado FROM productos WHERE estado = ? AND id = ? AND sucursal = ?");
                    $conestado -> bind_param('iis',$estado,$id,$sucursal);
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

    if(isset($_GET['get_active_values']) && $_GET['get_active_values'] === $clav) {
        $apr = $con -> prepare("SELECT * FROM active_products WHERE sucursal = ?");
        $apr -> bind_param('s',$sucursal);
        $apr -> execute();
        $Rapr = $apr -> get_result();
        if($Rapr -> num_rows > 0) {
            while($ap = mysqli_fetch_array($Rapr)){
                $resp = [
                    "status" => "success",
                    "title" => "datos",
                    "message" => [
                        "id" => $ap['id'],
                        "idprod" => $ap['id_producto'],
                        "unidades" => $ap['unidades'],
                        "porciones" => $ap['porciones'],
                        "precio" => $ap['precio'],
                        "precio_total" => $ap['precio']*$ap['porciones']
                    ]
                ];
            }
        }
    }

    if(isset($_GET['change_image_product']) && $_GET['change_image_product']) {
        $id = $_POST['id'];
        $producto = $_POST['producto'];
        $categoria = $_POST['categoria'];
        $dir = "../res/images/products/" . sanear_string($categoria) . preg_replace('/[^a-zA-Z0-9-_]/', '_', $producto);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }
        if(isset($_POST['foto']) && $_POST['foto'] == "portada"){
            $foto = 'portada';
            $photo = guardarFoto($foto, $producto, $dir);
            $sec = "UPDATE productos SET portada = ? WHERE id = ?";
        }
        if(isset($_POST['foto']) && $_POST['foto'] == "photo1"){
            $foto = 'photo1';
            $photo = guardarFoto($foto, $producto, $dir);
            $sec = "UPDATE productos SET foto_1 = ? WHERE id = ?";
        }
        if(isset($_POST['foto']) && $_POST['foto'] == "photo2"){
            $foto = 'photo2';
            $photo = guardarFoto($foto, $producto, $dir);
            $sec = "UPDATE productos SET foto_2 = ? WHERE id = ?";
        }
        if(isset($_POST['foto']) && $_POST['foto'] == "photo3"){
            $foto = 'photo3';
            $photo = guardarFoto($foto, $producto, $dir);
            $sec = "UPDATE productos SET foto_3 = ? WHERE id = ?";
        }
        $uphoto = $con -> prepare($sec);
        $uphoto -> bind_param('si',$photo,$id);
        if($uphoto -> execute()){
            if($uphoto -> affected_rows > 0){
                echo json_encode([
                    "status" => "success",
                    "title" => "Foto cambiada",
                    "message" => "Se ha establecido la nueva foto para ".$producto
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "No se ha modificado!",
                    "message" => "Los datos de ".$producto." no están disponibles - ".$photo
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Ha ocurrido un error!",
                "message" => $producto." no tiene candidato para modificar"
            ]);
        }
    }

    if(isset($_GET['mod_this_product']) && $_GET['mod_this_product'] === $clav){
        try {
            $id = $_POST['id'];
            $producto = $_POST['producto'];
            $categoria = $_POST['categoria'];
            $precio = sanear_string($_POST['precio']);
            $talla = isset($_POST['size']) ? $_POST['size'] : 'innecesario';
            $descripcion = $_POST['descripcion'];
            $campos = ['id','producto','categoria','precio','descripcion'];
            $validar = validarFormulario($_POST,$campos);
            if(!empty($validar)){
                $errs = "";
                foreach($validar as $a){
                    $errs .= $a . "<br>";
                }
                die(json_encode([
                    "status" => "error",
                    "title" => "Campos incompletos",
                    "message" => $a
                ]));
            }
            $act = $con -> prepare("UPDATE productos SET producto = ?, precio = ?, categoria = ?, descripcion = ?, talla = ? WHERE id = ?");
            $act -> bind_param('sisssi',$producto,$precio,$categoria,$descripcion,$talla,$id);
            if($act -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "Producto actualizado!",
                    "message" => "Se ha modificado ". $producto . " con éxito."
                ]);
            }
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al modificar el producto: ".$e -> getMessage(),$sesion);
        }
        $con -> close();
    }

    if(isset($_GET['delete_this_product']) && $_GET['delete_this_product'] === $clav){
        $id = $_POST['id'];
        $product = $_POST['producto'];
        $cat = $_POST['categoria'];
        try {
            $dl = $con -> prepare("DELETE FROM productos WHERE id = ?");
            $dl -> bind_param('i',$id);
            $dl -> execute();
            if($dl){
                $dir = "../res/images/products/" . sanear_string($cat) . preg_replace('/[^a-zA-Z0-9-_]/', '_', $product);
                eliminarDirectorio($dir);
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Se ha eliminado el producto"
                ]);
                writeLog("Producto ".$product." eliminado.", $sesion);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "No se ha podido eliminar el producto"
                ]);
                writeLog("Error al eliminar el producto ".$product, $sesion);
            }
        }
        catch (mysqli_sql_exception $e) {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al eliminar ".$product.": ".$e -> getMessage());
        }
        $con -> close();
    }

    if(isset($_GET['offer_this_product']) && $_GET['offer_this_product'] === $clav){
        $id = $_POST['id'];
        $prod = $_POST['producto'];
        $oferta = $_POST['oferta'];
        $ff = $oferta == 1 ? "está" : "ya no está";
        try {
            $offer = $con -> prepare("UPDATE productos SET oferta = ? WHERE id = ?");
            $offer -> bind_param('ii',$oferta,$id);
            if($offer -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "El producto ". $prod  . " " . $ff . " en oferta"
                ]);
                writeLog("Producto ".$prod." puesto en oferta.", $sesion);
            }
            else {
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "El producto ". $prod  . "  no se ha podido poner en oferta"
                ]);
                writeLog("Producto ".$prod." no se ha podido poner en oferta", $sesion);
            }
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al establecer oferta en producto ".$prod.": ".$e -> getMessage());
        }
    }

    if(isset($_GET['deactivate_this_product']) && $_GET['deactivate_this_product'] === $clav){
        $id = $_POST['id'];
        $prod = $_POST['producto'];
        $val = $_POST['val'];
        try {
            $desac = $con -> prepare("UPDATE productos SET estado = ? WHERE id = ?");
            $desac -> bind_param('ii',$val,$id);
            if($desac -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Se ha desactivado ". $prod  . ", ya no aparecerá en la lista de disponibles"
                ]);
                writeLog("Producto ".$prod." desactivado.", $sesion);
            }
            else {
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "El producto ". $prod  . "  no se ha podido desactivar"
                ]);
                writeLog("Producto ".$prod." no se ha podido desactivar", $sesion);
            }
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al establecer desactivar producto ".$prod.": ".$e -> getMessage());
        }
    }

?>