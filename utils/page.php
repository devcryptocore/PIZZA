<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('../php/optimizador.php');

    if(isset($_GET['get_roulette']) && $_GET['get_roulette'] === $exclav) {
        $cons = $con -> prepare("SELECT * FROM ruleta");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $rl = $Rcons -> fetch_assoc();
            echo json_encode([
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "estado" => $rl['estado'],
                    "premio1" => $rl['premio1'],
                    "premio1" => $rl['premio1'],
                    "premio2" => $rl['premio2'],
                    "premio3" => $rl['premio3'],
                    "premio4" => $rl['premio4'],
                    "premio5" => $rl['premio5'],
                    "premio6" => $rl['premio6'],
                    "premiada" => $rl['premiada'],
                    "premio" => $rl['premio'],
                    "intentos" => $rl['intentos'],
                    "uniqid" => $rl['unico']
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "empty"
            ]);
        }
    }

    if(isset($_GET['get_pizzas']) && $_GET['get_pizzas'] === $exclav) {
        $dummie = "../res/images/pizza_dummie.png";
        try {
            $sql = "SELECT p.*,ap.porciones,ap.precio AS precioporcion FROM productos p INNER JOIN
             active_products ap ON ap.id_producto = p.id WHERE p.categoria = (SELECT categoria FROM categoria_principal) AND p.estado = 1";
            $result = $con->query($sql);
            $pzz = [];
            $description = [];
            while ($row = $result->fetch_assoc()) {
                $portadax = $row['portada'] ?? $dummie;
                $portada = str_replace("../","",$portadax);
                $pzz[] = $portada;
                $producto = $row['producto'] ?? '';
                $description[$producto] = [
                    'id' => $row['id'],
                    'talla' => $row['talla'],
                    'porciones' => $row['porciones'],
                    'precio' => $row['precio'],
                    'precioporcion' => $row['precioporcion'],
                    'descripcion' => $row['descripcion']
                ];
            }
            echo json_encode([
                'status' => 'success',
                'pzz' => $pzz,
                'description' => $description
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    if(isset($_GET['get_categories']) && $_GET['get_categories'] === $exclav) {
        $estado = 1;
        $cons = $con -> prepare("SELECT DISTINCT categoria,imagen FROM categorias WHERE estado = ?");
        $cons -> bind_param('i',$estado);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $cats = "";
            while($c = $Rcons -> fetch_assoc()){
                $cats .= '
                    <div class="cat_card" onclick="loadPage(\'categories.php?c='.$c['categoria'].'\')"
                    data-aos="fade-right" data-aos-offset="150" data-aos-delay="100" style="background-image: url(\''.str_replace("../","",$c['imagen']).'\');">
                        <div class="catnom">
                            <span>'.$c['categoria'].'</span>
                        </div>
                    </div>
                ';
            }
        }
        else {
            $cats = "No se han registrado categorías";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Ok",
            "message" => $cats
        ]);
    }

    if(isset($_GET['add_to_cart']) && $_GET['add_to_cart'] === $exclav) {
        $idsesion = $_POST['idsesion'];
        $idproducto = $_POST['idproducto'];
        $cantidad = $_POST['cantidad'] ?? 1;
        $estado = 1;
        $cprod = $con -> prepare("SELECT p.*,ap.precio AS precioporcion FROM productos p
        INNER JOIN active_products ap ON ap.id_producto = p.id WHERE p.id = ? AND p.estado = ?");
        $cprod -> bind_param('ii',$idproducto,$estado);
        $cprod -> execute();
        $Rcprod = $cprod -> get_result();
        if($Rcprod -> num_rows > 0) {
            $prod = $Rcprod -> fetch_assoc();
            $talla = $prod['talla'];
            $nomprod = $prod['producto'];
            $precio = $talla == 'L' ? $prod['precioporcion'] : $prod['precio'];
            $subtotal = $precio * $cantidad;
            $cons = $con -> prepare("SELECT * FROM shopping_cart WHERE idsesion = ? AND idproducto = ?");
            $cons -> bind_param('si',$idsesion,$idproducto);
            $cons -> execute();
            $Rcons = $cons -> get_result();
            if($Rcons -> num_rows > 0) {
                $cart = $Rcons -> fetch_assoc();
                $can = $cart['cantidad'];
                $ncan = $can + $cantidad;
                $subtotal = $precio * $ncan;
                $upd = $con -> prepare("UPDATE shopping_cart SET cantidad = ?, subtotal = ?
                WHERE idsesion = ? AND idproducto = ?");
                $upd -> bind_param('iisi',$ncan,$subtotal,$idsesion,$idproducto);
                if($upd -> execute()){
                    $countprod = $con -> query("SELECT * FROM shopping_cart WHERE idsesion = '$idsesion'");
                    $tot = $con -> prepare("SELECT COALESCE(SUM(subtotal),0) AS total FROM shopping_cart WHERE idsesion = ?");
                    $tot -> bind_param('s',$idsesion);
                    $tot -> execute();
                    $Rtot = $tot -> get_result();
                    $total = $Rtot -> fetch_assoc()['total'];
                    echo json_encode([
                        "status" => "success",
                        "title" => "Producto agregado correctamente",
                        "countprod" => $countprod -> num_rows
                    ]);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "Error!",
                        "message" => "No se hapodido agregar el producto seleccionado"
                    ]);
                }
                exit;
            }
            else {
                $ins = $con -> prepare("INSERT INTO shopping_cart (idsesion,idproducto,producto,cantidad,precio,subtotal) VALUES (?,?,?,?,?,?)");
                $ins -> bind_param('sisiii',$idsesion,$idproducto,$nomprod,$cantidad,$precio,$subtotal);
                if($ins -> execute()) {
                    $countprod = $con -> query("SELECT * FROM shopping_cart WHERE idsesion = '$idsesion'");
                    $tot = $con -> prepare("SELECT COALESCE(SUM(subtotal),0) AS total FROM shopping_cart WHERE idsesion = ?");
                    $tot -> bind_param('s',$idsesion);
                    $tot -> execute();
                    $Rtot = $tot -> get_result();
                    $total = $Rtot -> fetch_assoc()['total'];
                    echo json_encode([
                        "status" => "success",
                        "title" => "Producto agregado correctamente",
                        "prodcount" => $countprod -> num_rows
                    ]);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "Error!",
                        "message" => "No se hapodido agregar el producto seleccionado"
                    ]);
                }
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "No disponible!",
                "message" => "Este producto no está disponible"
            ]);
        }
    }

    if(isset($_GET['get_my_cart']) && $_GET['get_my_cart'] === $exclav) {
        $total = 0;
        $idsesion = $_POST['idsesion'];
        $cons = $con -> prepare("SELECT * FROM shopping_cart WHERE idsesion = ?");
        $cons -> bind_param('s',$idsesion);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $cart_prod = "";
            $count = 0;
            while($cart = $Rcons -> fetch_assoc()) {
                $total += $cart['subtotal'];
                $count += $cart['cantidad'];
                $cart_prod .= '
                    <tr>
                        <td>'.$cart['producto'].'</td>
                        <td>'.$cart['cantidad'].'</td>
                        <td>$'.miles($cart['precio']).'</td>
                        <td>$'.miles($cart['subtotal']).'</td>
                        <td class="delprod" onclick="removeFromCart(\''.$cart['id'].'\')"></td>
                    </tr>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "products" => $cart_prod,
                    "total" => miles($total)
                ],
                "count" => $count
            ]);
        }
        else {
            echo json_encode([
                "status" => "empty",
                "title" => "Ok",
                "message" => "
                    <div class='vacont'>
                        <img src='res/images/pizzawalk.webp' alt='pizza walk' class='pizzawalk'>
                        <h3>Su carrito está vacío</h3>
                    </div>
                ",
                "count" => 0
            ]);
        }
    }

    if(isset($_GET['del_from_cart']) && $_GET['del_from_cart'] === $exclav) {
        $id = $_POST['id'];
        $del = $con -> prepare("DELETE FROM shopping_cart WHERE id = ?");
        $del -> bind_param('i',$id);
        if($del -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha quitado el producto del carrito"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido quitar el producto"
            ]);
        }
    }

    if(isset($_GET['clean_cart']) && $_GET['clean_cart'] === $exclav) {
        $id = $_POST['idsesion'];
        $del = $con -> prepare("DELETE FROM shopping_cart WHERE idsesion = ?");
        $del -> bind_param('s',$id);
        if($del -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha limpiado el carrito"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido limpiar el producto"
            ]);
        }
    }

    if(isset($_GET['get_product_info']) && $_GET['get_product_info'] === $exclav) {
        $idprod = $_POST['idprod'] ?? 0;
        $cons = $con -> prepare("SELECT p.*,ap.porciones,ap.precio AS prepor FROM productos p INNER JOIN
             active_products ap ON ap.id_producto = p.id WHERE p.id = ?");
        $cons -> bind_param('i',$idprod);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $default_img = "../res/images/p4.png";
        $prod = $Rcons -> fetch_assoc();
        $ta = $prod['talla'] ?? '';
        $precio = $ta == 'L' ? miles($prod['prepor'] ?? 0) . '<span class="smlt"> * porción</span>' : miles($prod['precio'] ?? 0);
        echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "producto" => $prod['producto'] ?? '',
                    "precio" => $precio,
                    "categoria" => $prod['categoria']  ?? '',
                    "descripcion" => $prod['descripcion']  ?? '',
                    "talla" => $prod['talla']  ?? '',
                    "oferta" => $prod['oferta']  ?? 0,
                    "portada" => str_replace("../","",$prod['portada'] ?? $default_img),
                    "foto_1" => str_replace("../","",$prod['foto_1'] ?? $default_img),
                    "foto_2" => str_replace("../","",$prod['foto_2'] ?? $default_img),
                    "foto_3" => str_replace("../","",$prod['foto_3'] ?? $default_img)
                ]
        ]);
    }

    if(isset($_GET['get_some_products']) && $_GET['get_some_products'] === $exclav) {
        $estado = 1;
        $cons = $con -> prepare("SELECT p.*,ap.porciones,ap.precio AS prepor FROM productos p INNER JOIN
             active_products ap ON ap.id_producto = p.id WHERE p.estado = ? ORDER BY p.id DESC LIMIT 8");
        $cons -> bind_param('i',$estado);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $prods = "";
            while($pr = $Rcons -> fetch_assoc()){
                //$precio = $pr['talla'] == 'L' ? miles($pr['prepor']) . '<span class="smlt"> * porción</span>' : miles($pr['precio']);
                if($pr['talla'] == 'L'){
                    $precio = miles($pr['prepor']) . '<span class="smlt"> * porción</span>';
                    $allpizza = '
                        <button class="completbt" onclick="pizzacompleta(\'#cantidb_'.$pr['id'].'\',\''.$pr['id'].'\')">Completa</button>
                    ';
                }
                else {
                    $precio = miles($pr['precio']);
                    $allpizza = '';
                }
                $portada = $pr['portada'] ?? 'res/icons/image.svg';
                $prods .= '
                    <div class="prod-card">
                        <div class="up-to-card">
                            <h3 class="card-title" style="white-space: wrap; display: inline-block; font-size: 10px;text-align:center;">'.$pr['producto'].'</h3>
                        </div>
                        <div onclick="this_product(\''.$pr['id'].'\')" class="prod-image" style="background-image:url('.str_replace("../","",$portada).')"></div>
                        <div class="makont">
                            <div class="lfcon">
                                <div class="price-cont">
                                    <span>$'.$precio.'</span>
                                </div>
                                <div class="prod-form">
                                    <div id="sell_this">
                                        <input type="hidden" name="id" value="10">
                                        <div class="counter-cont">
                                            <span class="counter-bt minus" onclick="setcan(\'minus\',\'#cantidb_'.$pr['id'].'\')">-</span>
                                            <input type="number" name="cantidad" value="1" id="cantidb_'.$pr['id'].'" readonly>
                                            <span class="counter-bt more" onclick="setcan(\'more\',\'#cantidb_'.$pr['id'].'\')">+</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lfcon">
                                '.$allpizza.'
                                <input type="submit" value="" class="car_add_button" onclick="addToCart(\''.$pr['id'].'\',\'#cantidb_'.$pr['id'].'\')">
                            </div>
                        </div>
                    </div>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => $prods
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => "<div class='sppm'></div>"
            ]);
        }
    }

    if(isset($_GET['set_pedido']) && $_GET['set_pedido'] === $exclav) {
        header('Content-Type: application/json; charset=utf-8');
        $est = 1;
        $idpedido = uniqid();
        $idsesion = $_POST['idsesion'];
        $nombre = htmlspecialchars($_POST['nombre']);
        $telefono = sanear_string($_POST['telefono']);
        $direccion = htmlspecialchars($_POST['direccion']);
        $coordenadas = $_POST['coordenadas'] ?? '';
        $comentario = htmlspecialchars($_POST['comentario']);
        $estado = "recibido";
        $fechahora = date('d/m/Y H:i:s');

        $org = $con -> prepare("SELECT ptelefono FROM company");
        $org -> execute();
        $Rorg = $org -> get_result();
        $telefonorg = $Rorg -> fetch_assoc()['ptelefono'] ?? '3100000000';

        $cart = $con -> prepare("SELECT sc.*, p.precio AS prodprecio, p.estado,p.talla FROM shopping_cart sc INNER JOIN productos p
        ON p.id = sc.idproducto WHERE sc.idsesion = ?");
        $cart -> bind_param('s',$idsesion);
        $cart -> execute();
        $Rcart = $cart -> get_result();
        if($Rcart -> num_rows > 0) {
            $total = 0;
            $productos = "";
            $producto = [];
            while($car = $Rcart -> fetch_assoc()){
                $idproducto = $car['idproducto'];
                $precio = $car['precio'];
                $cantidad = $car['cantidad'];
                $subtotal = $car['subtotal'];

                $producto[] = [
                    "idproducto" => $idproducto,
                    "precio" => $precio,
                    "cantidad" => $cantidad,
                    "subtotal" => $subtotal
                ];
                $productos .= "- " . $cantidad . " ⬌ " . $car['producto'] . " ⬌ $" . miles($subtotal) ."\n";
                $total += $subtotal;
            }
            $producto = json_encode($producto, JSON_UNESCAPED_UNICODE);
            $con -> begin_transaction();
            try {
                $insr = $con -> prepare("INSERT INTO pedidos (
                idpedido,
                idsesion,
                pedido,
                nombre,
                telefono,
                direccion,
                coordenadas,
                comentario
                ) VALUES (?,?,?,?,?,?,?,?)");
                $insr -> bind_param('ssssssss',$idpedido,$idsesion,$producto,$nombre,$telefono,$direccion,$coordenadas,$comentario);
                $insr -> execute();
                $cleancart = $con -> prepare("DELETE FROM shopping_cart WHERE idsesion = ?");
                $cleancart -> bind_param('s',$idsesion);
                $cleancart -> execute();
                $con -> commit();
                if (ob_get_length()) ob_end_clean();
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => [
                        "text" => "Su pedido se ha registrado con éxito, será redirigido a Whatsapp",
                        "nombre" => urlencode($nombre),
                        "telefono" => urlencode($telefono),
                        "direccion" => urlencode($direccion),
                        "pedido" => $productos,
                        "total" => miles($total),
                        "fecha" => $fechahora,
                        "comentario" => urlencode($comentario),
                        "telefono" => $telefonorg,
                        "page" => $dominio
                    ]
                ],JSON_UNESCAPED_UNICODE);
            }
            catch (Exception $e) {
                $con -> rollback();
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => [
                        "text" => "No se ha podido procesar su pedido " . $e -> getMessage()
                    ]
                ]);
            }
            
        }

    }

    if(isset($_GET['menu_products']) && $_GET['menu_products'] === $exclav){
        $categoria = $_GET['c'];
        $estado = 1;
        $cons = $con -> prepare("SELECT p.*,ap.porciones,ap.precio AS prepor FROM productos p INNER JOIN
             active_products ap ON ap.id_producto = p.id WHERE p.categoria = ? AND p.estado = ? ORDER BY p.id DESC");
        $cons -> bind_param('si',$categoria,$estado);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $prods = "";
            while($pr = $Rcons -> fetch_assoc()){
                if($pr['talla'] == 'L'){
                    $precio = miles($pr['prepor']) . '<span class="smlt"> * porción</span>';
                    $allpizza = '
                        <button class="completbt" onclick="pizzacompleta(\'#cantidb_'.$pr['id'].'\',\''.$pr['id'].'\')" style="top:unset;left:unset;right:0px;">
                        Completa
                        </button>
                    ';
                }
                else {
                    $precio = miles($pr['precio']);
                    $allpizza = '';
                }
                $portada = $pr['portada'] ?? '';
                $prods .= '
                    <div class="prod-card">
                        <div class="up-to-card">
                            <h3 class="card-title" style="white-space: nowrap; display: inline-block; font-size: 11.3798px;">'.$pr['producto'].'</h3>
                        </div>
                        <div onclick="this_product(\''.$pr['id'].'\')" class="prod-image" style="background-image:url('.str_replace("../","",$portada).')"></div>
                        <div class="price-cont" style="position:relative;">
                            <span>$'.$precio.'</span>
                            '.$allpizza.'
                        </div>
                        <div class="prod-form">
                            <div id="sell_this">
                                <input type="hidden" name="id" value="10">
                                <div class="counter-cont">
                                    <span class="counter-bt minus" onclick="setcan(\'minus\',\'#cantidb_'.$pr['id'].'\')">-</span>
                                    <input type="number" name="cantidad" value="1" id="cantidb_'.$pr['id'].'" readonly>
                                    <span class="counter-bt more" onclick="setcan(\'more\',\'#cantidb_'.$pr['id'].'\')">+</span>
                                </div>
                                <input type="submit" value="Agregar" class="send-button" onclick="addToCart(\''.$pr['id'].'\',\'#cantidb_'.$pr['id'].'\')">
                            </div>
                        </div>
                    </div>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => $prods
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => "<h3>Sin productos para mostrar</h3>"
            ]);
        }
    }

    if(isset($_GET['get_company_data']) && $_GET['get_company_data'] === $exclav) {
        $cons = $con -> prepare("SELECT c.*,a.* FROM company c LEFT JOIN about_section a ON 1");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $org = $Rcons -> fetch_assoc();
        $sucursales = [];
        $sucx = $con -> prepare("SELECT * FROM sucursales");
        $sucx -> execute();
        $Rsucx = $sucx -> get_result();
        if($Rsucx -> num_rows > 0) {
            while($sucs = $Rsucx -> fetch_assoc()) {
                $sucursales[$sucs['sucursal'] ?? ''] = [
                    "id" => $sucs['id'] ?? '',
                    "sucubicacion" => $sucs['ubicacion'] ?? '1.604526, -77.131243',
                    "sucdireccion" => $sucs['direccion'] ?? 'St 5a #12-45 Center',
                    "suctelefono" => $sucs['telefono'] ?? '3100000000',
                    "sucfoto" => $sucs['foto'] ?? 'res/images/default_logo.png'
                ];
            }
        }
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => [
                "organizacion" => $org['organizacion'] ?? 'NextFlow App',
                "ptelefono" => $org['ptelefono'] ?? '3100000000',
                "stelefono" => $org['stelefono'] ?? '3100000000',
                "email" => $org['email'] ?? 'nextflow@example.com',
                "direccion" => $org['direccion'] ?? 'St 5a #12-45 Center',
                "nit" => $org['nit'] ?? '123456789-1',
                "encargado" => $org['encargado'] ?? 'Jhon Doe',
                "documento" => $org['documento'] ?? '123456789',
                "logotipo" => $org['logotipo'] ?? 'res/images/default_logo.png',
                "publicidad" => $org['faqs'] ?? 'res/images/amigos.webp',
                "nosotros" => $org['nosotros'] ?? '',
                "fecha" => $org['fecharegistro'] ?? '01-01-2000',
                "faqs" => $org['faqs'] ?? '',
                "page" => $dominio,
                "sucursales" => $sucursales
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

?>