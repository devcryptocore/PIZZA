<?php

    include('../includes/verificator.php');

    function get_last_serie() {
        global $con;
        $vnt = $con->prepare("SELECT consecutivo FROM ventas ORDER BY id DESC LIMIT 1");
        $vnt->execute();
        $result = $vnt->get_result();
        $row = $result->fetch_assoc();
        $last = $row ? (int)$row['consecutivo'] : 0;
        $next = $last + 1;
        $serie = str_pad($next, 8, "0", STR_PAD_LEFT);
        if (strlen($serie) > 8) {
            $serie = substr($serie, -8);
        }
        return $serie;
    }

    function statebox(){
        global $con,$sesion,$sucursal;
        $est = 1;
        $box = $con -> prepare("SELECT estado FROM caja WHERE estado = ? AND usuario = ? AND sucursal = ?");
        $box -> bind_param('iss',$est,$sesion,$sucursal);
        $box -> execute();
        $Rbox = $box -> get_result();
        if($Rbox -> num_rows > 0) {
            return true;
        }
        return false;
    }

    function boxcode() {
        global $con,$sesion,$sucursal;
        $bcon = $con -> prepare("SELECT codcaja FROM caja WHERE usuario = ? AND sucursal = ? ORDER BY codcaja DESC LIMIT 1");
        $bcon -> bind_param('ss',$sesion,$sucursal);
        $bcon -> execute();
        $Rbcon = $bcon -> get_result();
        if($Rbcon -> num_rows > 0) {
            $nc = $Rbcon -> fetch_assoc()['codcaja'];
            return $nc;
        }
        else {
            return 1;
        }
    }

    if (isset($_GET['make_sell']) && $_GET['make_sell'] === $clav) {
        $term     = $_GET['terminal'];
        $metodopago = $_POST['metodopago'];
        $recibido = sanear_string($_POST['recibido']);
        $cliente = empty($_POST['cliente']) ? 'Indefinido' : $_POST['cliente'];
        $clidoc = empty($_POST['clidoc']) ? '0000000000' : $_POST['clidoc'];
        $idventa = uniqid();
        $seriefac = get_last_serie();
        $idcaja   = boxcode();
        $errors   = [];

        if(!statebox()){
            echo json_encode([
                "status" => "warning",
                "title"  => "Sin apertura de caja!",
                "message"=> "No puede procesar ventas sin hacer apertura caja!"
            ]);
            exit;
        }

        $con->begin_transaction();
        try {
            $sql = "SELECT sc.id_producto, sc.cantidad,
                        p.producto, p.oferta,
                        ap.precio
                    FROM sell_cart sc
                    INNER JOIN productos p ON p.id = sc.id_producto
                    INNER JOIN active_products ap ON ap.id_producto = sc.id_producto
                    WHERE sc.unico = ? AND sc.usuario = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('is', $term, $sesion);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("No se encontraron productos en el carrito.");
            }
            $tsl = 0;
            while ($prod = $result->fetch_assoc()) {
                $idproducto = $prod['id_producto'];
                $nombre_producto = $prod['producto'];
                $cantidad = $prod['cantidad'];
                $precio   = $prod['precio'];
                $oferta   = $prod['oferta'];
                $descuento= 0;
                if ($oferta > 0) {
                    $calcular = calculate_offer($precio, $oferta);
                    $precio   = $calcular['newprice'];
                    $descuento = $calcular['discount'] * $cantidad;
                }

                $total = $precio * $cantidad;
                $tsl += $total;
                $venta = $con->prepare("INSERT INTO ventas 
                    (consecutivo,idventa, metodopago, idcaja, id_producto, producto, cantidad, porciones, precio, total, recibido, unico, descuento, cliente, clidoc, usuario, sucursal)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $venta->bind_param(
                    'sssiisiiiiiiissss',
                    $seriefac, $idventa, $metodopago, $idcaja, $idproducto, $nombre_producto, $cantidad, $cantidad,
                    $precio, $total, $recibido, $term, $descuento, $cliente, $clidoc, $sesion, $sucursal
                );
                if (!$venta->execute()) {
                    throw new Exception("Error al procesar la venta de {$nombre_producto}.");
                }
            }
            $uup = $con -> prepare("UPDATE clientes SET total_comprado = COALESCE(total_comprado,0) + ? WHERE documento = ?");
            $uup -> bind_param('is',$tsl,$clidoc);
            @$uup -> execute();//Suprime warnings
            $con->commit();
            echo json_encode([
                "status" => "success",
                "title"  => "Correcto!",
                "message"=> [
                    "text" => "Se ha realizado la venta correctamente, desea imprimir la factura?",
                    "numero" => $seriefac
                ]
            ]);

        } catch (Exception $e) {
            $con->rollback();
            echo json_encode([
                "status" => "error",
                "title"  => "No se ha realizado la venta!",
                "message"=> [
                    "text" => $e->getMessage(),
                    "numero" => "0"
                ]
            ]);
        }

        $con->close();
    }

    if (isset($_GET['devolucion']) && $_GET['devolucion'] === $clav) {
        $ventaId = intval($_POST['venta_id']);
        $devolver = intval($_POST['cantidad']);
        $motivo = !empty($_POST['motivo']) ? trim($_POST['motivo']) : "Sin especificar";
        $check = $con->prepare("SELECT * FROM ventas WHERE id = ? AND sucursal = ?");
        $check->bind_param("is", $ventaId, $sucursal);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $venta = $result->fetch_assoc();
            if ($devolver > $venta['cantidad']) {
                echo json_encode([
                    "status" => "error",
                    "title"  => "Cantidad inválida",
                    "message"=> "No se pueden devolver más productos de los que se vendieron."
                ]);
                exit;
            }
            $insertDev = $con->prepare("INSERT INTO devoluciones 
                (id_venta, id_producto, producto, cantidad, precio, total, usuario, sucursal, motivo)
                VALUES (?,?,?,?,?,?,?,?,?)");
            $totalDev = $venta['precio'] * $devolver;
            $insertDev->bind_param("iisiiisss",$venta['id'],$venta['id_producto'],$venta['producto'],$devolver,
                $venta['precio'],$totalDev,$sesion,$sucursal,$motivo);
            if ($insertDev->execute()) {
                if ($devolver == $venta['cantidad']) {
                    $del = $con->prepare("DELETE FROM ventas WHERE id = ? AND sucursal = ?");
                    $del->bind_param("is", $ventaId, $sucursal);
                    $del->execute();
                } else {
                    $nuevaCantidad = $venta['cantidad'] - $devolver;
                    $nuevoTotal = $nuevaCantidad * $venta['precio'];
                    $upd = $con->prepare("UPDATE ventas SET cantidad = ?, total = ? WHERE id = ? AND sucursal = ?");
                    $upd->bind_param("iiis",$nuevaCantidad,$nuevoTotal,$ventaId,$sucursal);
                    $upd->execute();
                }
                echo json_encode([
                    "status" => "success",
                    "title"  => "Devolución realizada",
                    "message"=> "Se devolvieron $devolver unidad(es) de {$venta['producto']} correctamente."
                ]);
            }
        }
    }



?>