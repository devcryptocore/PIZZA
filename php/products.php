<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');
    include('vendor/autoload.php');
    use Picqer\Barcode\BarcodeGeneratorPNG;
    

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
        $generator = new BarcodeGeneratorPNG();
        $idcode = $_POST['idcode'];
        $uniqd = rand(10000,99999) . $idcode;
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
        $photo3 = guardarFoto("photo3", $producto, $dir);
        $barcode = $generator -> getBarcode($uniqd, $generator::TYPE_CODE_128);
        $archivocb = $dir . '/' . $uniqd . ".png";
        file_put_contents($archivocb, $barcode);
        $ingedientes = [];
        $inprod = $con -> prepare("INSERT INTO productos (id_code, cod_barras, producto, precio, categoria, descripcion, talla, estado, oferta, portada, foto_1, foto_2, foto_3, barcode, sucursal, usuario)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $inprod -> bind_param('ississsiisssssss',$idcode,$uniqd,$producto,$precio,$categoria,$descripcion,$size,$estado,$oferta,$portada,$photo1,$photo2,$photo3,$archivocb,$sucursal,$sesion);
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

    if(isset($_GET['get_products']) && $_GET['get_products'] === $clav) {//FALTA CALCULAR VALOR DE OFERTA
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

                    if($row['oferta'] > 0){
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

                    if($row['oferta'] > 0){
                        $row['precio'] = calculate_offer($row['precio'],$row['oferta'])['newprice'];
                    }

                    if($xs['medida'] == 'unidad'){
                        if($xs['cantidad'] > 0){
                            $costo = $costo / $xs['cantidad'];
                            $ganancia = $row['precio'] - $costo;
                        }
                        else {
                            $costo = 0;
                            $ganancia = 0;
                        }
                    }

                    $cap = $con -> query("SELECT * FROM active_products WHERE id_producto = $idf");
                    $ap = $cap -> fetch_assoc();
                    if($row['oferta'] > 0){
                        $ap['precio'] = calculate_offer($ap['precio'],$row['oferta'])['newprice'];
                    }
                    if(isset($ap['porciones']) && $ap['porciones'] > 0){
                        $costoporc = $costo/$ap['porciones'];
                        $cost = "
                            <div class='dvcont'>
                                <span><b>Porc:</b> $".miles(round($costoporc,2))."</span>
                                <span><b>Und:</b> $".miles(round($costo))."</span>
                            </div>
                        ";
                        $prec = "
                            <div class='dvcont'>
                                <span><b>Porc:</b> $".miles($ap['precio'])."</span>
                                <span><b>Und:</b> $".miles($row['precio'])."</span>
                            </div>
                        ";
                        $gan = "
                            <div class='dvcont'>
                                <span><b>Porc:</b> $".miles($ap['precio']-round($costoporc,2))."</span>
                                <span><b>Und:</b> $".miles(round($row['precio']-$costo))."</span>
                            </div>
                        ";
                        $actv = "
                            <div class='dvcont'>
                                <span><b>Porc:</b> ".$ap['porciones']."</span>
                                <span><b>Und:</b> ".round($ap['unidades'],2)."</span>
                            </div>
                        ";
                        $stk = "
                            <div class='dvcont'>
                                <span><b>Porc:</b> ".($row['stock_disponible']*8)."</span>
                                <span><b>Und:</b> ".$row['stock_disponible']."</span>
                            </div>
                        ";
                    }
                    else {
                        $cost = miles(round($costo));
                        $prec = miles($row['precio']);
                        $gan = miles(round($ganancia));
                        $actv = round($ap['unidades'] ?? 0);
                        $stk = $row['stock_disponible'];
                    }

                    $talla = $row['talla'] == 'innecesario' ? "⊘" : $row['talla'];
                    $offr = $row['oferta'] > 0 ? $row['oferta']."% Off" : 0 ;

                    $productos .= '
                        <tr onclick="openProductOptions(\''.$row['producto'].'\',\''.$row['id'].'\',\''.$row['talla'].'\')" class="elem-busqueda">
                            <td style="position:relative;">'.$stt.$row['id'].'</td>
                            <td>'.$row['producto'].'</td>
                            <td>'.$talla.'</td>
                            <td>'.$cost.'</td>
                            <td>'.$prec.'</td>
                            <td>'.$gan.'</td>
                            <td>'.$actv.'</td>
                            <td>'.$stk.'</td>
                            <td>'.$offr.'</td>
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
                    "foto3" => $prod['foto_3'] ?? $default_image,
                    "barcode" => $prod['barcode'] ?? $default_image
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
                $precioprod = $prod['precio'];
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
                $cpr = $con -> query("SELECT * FROM active_products WHERE id_producto = $id");
                if($cpr -> num_rows > 0){
                    $apr = $con -> prepare("DELETE FROM active_products WHERE id_producto = ?");
                    $apr -> bind_param('i',$id);
                    $apr -> execute();
                }
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

    if(isset($_GET['offer_this_product']) && $_GET['offer_this_product'] === $clav){//ESTABLECER PORCENTAJE DE OFERTA
        $id = $_POST['id'];
        $prod = $_POST['producto'];
        $porcentaje = $_POST['porcoffer'];
        $ff = $porcentaje > 0 ? "está" : "ya no está";
        try {
            $offer = $con -> prepare("UPDATE productos SET oferta = ? WHERE id = ?");
            $offer -> bind_param('ii',$porcentaje,$id);
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
            if($_GET['act'] == "activar"){
                $nst = 1;
                $std = $con -> prepare("UPDATE productos SET estado = ? WHERE id = ?");
                $std -> bind_param('ii',$nst,$id);
                if($std -> execute()){
                    echo json_encode([
                        "status" => "success",
                        "title" => "Producto activado!",
                        "message" => "El producto se ha activado correctamente"
                    ]);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "No se activó el producto!",
                        "message" => "No se ha podido activar el producto. "
                    ]);
                }
                exit;
            }
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

    if(isset($_GET['find_product']) && $_GET['find_product'] === $clav) {
        $producto = htmlspecialchars($_POST['producto']);
        $estado = 1;
        $cs = "SELECT p.*, ap.*
            FROM productos p
            INNER JOIN active_products ap
            ON ap.id_producto = p.id
            WHERE (p.producto LIKE CONCAT('%', ?, '%')
                OR p.categoria LIKE CONCAT('%', ?, '%'))
                OR p.id_code = ?
            AND p.estado = ?;";
        //$consl = $con -> prepare("SELECT * FROM productos WHERE (producto LIKE CONCAT('%', ?, '%') OR categoria LIKE CONCAT('%', ?, '%')) AND estado = ?");
        $consl = $con -> prepare($cs);
        $consl -> bind_param('ssii', $producto, $producto, $producto, $estado);
        $consl -> execute();
        $Rconsl = $consl -> get_result();
        if($Rconsl -> num_rows > 0){
            $produc = "";
            while($prd = mysqli_fetch_array($Rconsl)){
                $price = $prd['precio'];
                $bg = "";
                if($prd['oferta'] > 0){
                    $bg = "style='background: #dddddd url(../res/icons/offer-yellow.svg) right / 30px no-repeat;'";
                    $price = calculate_offer($price,$prd['oferta'])['newprice'];
                }
                $produc .= '
                    <button class="found_product" onclick="add_product(\''.$prd['id_producto'].'\')" '.$bg.'>
                        <b>'.$prd['producto'].'</b>
                        <span>$'.miles($price).'</span>
                    </button>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "Ok",
                "message" => $produc
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Vacío",
                "message" => "Sin resultados"
            ]);
        }
    }

    if(isset($_GET['add_sell_product']) && $_GET['add_sell_product'] === $clav) {
        $id = $_POST['id'];
        $term = $_POST['terminal'];
        $cantidad = 1;
        $cpd = $con -> prepare("SELECT * FROM productos WHERE id = ?");
        $cpd -> bind_param('i',$id);
        $cpd -> execute();
        $Rcpd = $cpd -> get_result();
        if($Rcpd -> num_rows > 0){
            $p = $Rcpd -> fetch_assoc();
            $concart = $con -> prepare("SELECT * FROM sell_cart WHERE id_producto = ? AND usuario = ? AND unico = ?");
            $concart -> bind_param('isi',$id,$sesion,$term);
            $concart -> execute();
            $Rconcart = $concart -> get_result();
            $incart = $con -> prepare("INSERT INTO sell_cart (unico,id_producto,cantidad,sucursal,usuario) VALUES (?, ?, ?, ?, ?)");
            $incart -> bind_param('iiiss',$term,$id,$cantidad,$sucursal,$sesion);
            if($Rconcart -> num_rows > 0){
                $incart = $con -> prepare("UPDATE sell_cart SET cantidad = COALESCE(cantidad, 0) + ? WHERE id_producto = ? AND unico = ?");
                $incart -> bind_param('iii',$cantidad,$id,$term);
            }
            if ($incart -> execute()) {
                echo json_encode([
                    "status" => "success",
                    "title" => "Ok",
                    "message" => "ok"
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "error",
                    "message" => "error"
                ]);
            }
        }
    }

    if(isset($_GET['get_added_products']) && $_GET['get_added_products'] === $clav) {
        $term = $_GET['terminal'];
        $cons = $con -> prepare("SELECT * FROM sell_cart WHERE unico = ? AND usuario = ?");
        $cons -> bind_param('is',$term,$sesion);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if ($Rcons -> num_rows > 0) {
            $prods = "";
            $total = 0;
            $descu = 0;
            while($pr = mysqli_fetch_array($Rcons)){
                $consp = $con -> prepare("SELECT productos.*, active_products.* FROM productos
                INNER JOIN active_products WHERE productos.id = ? AND active_products.id_producto = ?");
                $consp -> bind_param('ii',$pr['id_producto'],$pr['id_producto']);
                $consp -> execute();
                $Rconsp = $consp -> get_result();
                $prto = $Rconsp -> fetch_assoc();
                $discount = 0;
                $price = $prto['precio'];
                $fff = "";
                $porcc = $prto['porciones'] > 0 ? 'porc.' : 'und.';
                if($prto['oferta'] > 0){
                    $calcularoferta = calculate_offer($prto['precio'],$prto['oferta']);
                    $price = $calcularoferta['newprice'];
                    $discount = $calcularoferta['discount']*$pr['cantidad'];
                    $descu += $discount;
                    $fff = '<span class="offer_image"></span>';
                }
                $subtotal = $price*$pr['cantidad'];
                $total += $subtotal;
                $prods .= '
                    <tr>
                        <td>'.$fff.$prto['producto'].'</td>
                        <td>
                            <input type="number" name="cantidad" value="'.$pr['cantidad'].'"
                            class="prcant" onkeyup="changecant(this,\''.$pr['id_producto'].'\')" autocomplete="off">
                            <span style="font-size:10px;">'.$porcc.'</span>
                        </td>
                        <td>$'.miles($prto['precio']).'</td>
                        <td>$'.miles($discount).'</td>
                        <td>$'.miles($subtotal).'</td>
                        <td>
                            <button class="delthisprd" onclick="deladdedproduct(\''.$pr['id_producto'].'\')"></button>
                        </td>
                    </tr>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "prods" => $prods,
                    "total" => $total,
                    "offer" => $prto['oferta']."%",
                    "descuento" => miles($descu)
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "empty",
                "message" => [
                    "prods" => "<tr><td style='padding:15px;'>Sin productos seleccionados</td></tr>",
                    "total" => "0000",
                    "offer" => "0%",
                    "descuento" => "0"
                ]
            ]);
        }
    }

    if(isset($_GET['cant_added_products']) && $_GET['cant_added_products'] === $clav) {
        $id = $_POST['id'];
        $valor = $_POST['valor'];
        $term = $_POST['terminal'];
        $vlo = 0;
        $cons = $con -> prepare("SELECT * FROM active_products WHERE id_producto = ?");
        $cons -> bind_param('i', $id);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $ap = $Rcons -> fetch_assoc();
            $unidades = $ap['unidades'];
            if($ap['porciones'] > 0){
                $unidades = $ap['porciones'];
            }
            if($valor <= 0){
                die(json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "No se puede establecer la cantidad a 0"
                ]));
            }
            if($unidades < $valor) {
                die(json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "La cantidad establecida es mayor a la disponible"
                ]));
            }
            $change = $con -> prepare("UPDATE sell_cart SET cantidad = ? WHERE unico = ? AND id_producto = ?");
            $change -> bind_param('iii',$valor,$term,$id);
            if($change -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "ok",
                    "message" => "changed"
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "No se ha podido cambiar el valor de la cantidad"
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Producto no disponible"
            ]);
        }
    }

    if(isset($_GET['del_added_products']) && $_GET['del_added_products'] === $clav){
        $id = $_POST['id'];
        $term = $_POST['terminal'];
        $dl = $con -> prepare("DELETE FROM sell_cart WHERE unico = ? AND id_producto = ?");
        $dl -> bind_param('ii',$term,$id);
        if($dl -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Eliminado",
                "message" => "Se ha eliminado el producto de la lista"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha podido eliminar el priducto de la lista"
            ]);
        }
    }

    if(isset($_GET['get_barcodes']) && $_GET['get_barcodes'] === $clav) {
        $cons = $con -> prepare("SELECT id_code,producto,barcode FROM productos");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $rps = "";
        if($Rcons -> num_rows > 0){
            while($cb = mysqli_fetch_array($Rcons)){
                $nn = $cb['barcode'] == null || $cb['barcode'] == '' ? 'none' : 'flex';
                $rps .= '
                    <div style="
                    width:220px;height:110px;padding:15px 8px;display:'.$nn.';flex-direction:column;align-items:center;justify-content:center;gap:10px;border:1px solid #1f1f1f;border-radius:6px;
                    ">
                        <h2 style="font-size:18px;">'.$cb['producto'].'</h2>
                        <img src="'.$cb['barcode'].'" alt="CB_'.$cb['id_code'].'" style="width:180px;">
                    </div>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => $rps
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "bad",
                "message" => "Sin códigos de barras"
            ]);
        }
    }

    if(isset($_GET['from_barcode']) && $_GET['from_barcode'] === $clav) {
        $barcode = $_POST['barcode'];
        $stato = 1;
        $bc = $con -> prepare("SELECT id FROM productos WHERE cod_barras = ? AND estado = ?");
        $bc -> bind_param('si',$barcode,$stato);
        $bc -> execute();
        $Rbc = $bc -> get_result();
        if($Rbc -> num_rows > 0) {
            $idp = $Rbc -> fetch_assoc();
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => $idp['id']
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha encontrado el producto"
            ]);
        }
    }

?>
