<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
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

    if (isset($_GET['make_sell']) && $_GET['make_sell'] === $clav) {
        $term     = $_GET['terminal'];
        $recibido = sanear_string($_POST['recibido']);
        $idventa = uniqid();
        $seriefac = get_last_serie();
        $idcaja   = 1; // Simula ID de caja
        $errors   = [];

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
                $venta = $con->prepare("INSERT INTO ventas 
                    (consecutivo,idventa, idcaja, id_producto, producto, cantidad, porciones, precio, total, recibido, unico, descuento, usuario, sucursal)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $venta->bind_param(
                    'ssiisiiiiiiiss',
                    $seriefac, $idventa, $idcaja, $idproducto, $nombre_producto, $cantidad, $cantidad,
                    $precio, $total, $recibido, $term, $descuento, $sesion, $sucursal
                );
                if (!$venta->execute()) {
                    throw new Exception("Error al procesar la venta de {$nombre_producto}.");
                }
            }
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
        $usuario = $_SESSION['usuario']; 
        $sucursal = $_SESSION['sucursal'];
        $motivo = !empty($_POST['motivo']) ? trim($_POST['motivo']) : "Sin especificar";

        // Verificamos que la venta exista
        $check = $con->prepare("SELECT * FROM ventas WHERE id = ? AND sucursal = ?");
        $check->bind_param("is", $ventaId, $sucursal);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $venta = $result->fetch_assoc();
            $insertDev = $con->prepare("INSERT INTO devoluciones 
                (id_venta, id_producto, producto, cantidad, precio, total, usuario, sucursal, motivo)
                VALUES (?,?,?,?,?,?,?,?,?)");
            $insertDev->bind_param(
                "iisiiisss",
                $venta['id'],
                $venta['id_producto'],
                $venta['producto'],
                $venta['cantidad'],
                $venta['precio'],
                $venta['total'],
                $usuario,
                $sucursal,
                $motivo
            );
            if ($insertDev->execute()) {
                $del = $con->prepare("DELETE FROM ventas WHERE id = ? AND sucursal = ?");
                $del->bind_param("is", $ventaId, $sucursal);
                if ($del->execute()) {
                    echo json_encode([
                        "status" => "success",
                        "title"  => "Devolución realizada",
                        "message"=> "Se devolvió {$venta['cantidad']} unidad(es) de {$venta['producto']} correctamente."
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "title"  => "Error en eliminación",
                        "message"=> "No se pudo borrar la venta después de registrar la devolución."
                    ]);
                }
            } else {
                echo json_encode([
                    "status" => "error",
                    "title"  => "Error en registro",
                    "message"=> "No se pudo registrar la devolución en la base de datos."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "title"  => "Venta no encontrada",
                "message"=> "La venta indicada no existe o no corresponde a esta sucursal."
            ]);
        }
        $con->close();
    }


?>