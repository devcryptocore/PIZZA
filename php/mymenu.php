<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

    if(isset($_GET['menu_products']) && $_GET['menu_products'] === $clav){
        $consprod = "SELECT 
            a.id,
            a.producto,
            a.precio,
            a.categoria,
            a.descripcion,
            a.talla,
            a.estado,
            a.oferta,
            a.portada,
            COALESCE(SUM(b.unidades), 0) AS unidades_disponibles,
            COALESCE(SUM(b.porciones), 0) AS porciones_disponibles
        FROM productos a
        LEFT JOIN active_products b ON a.id = b.id_producto  WHERE a.estado = 1 AND a.sucursal = '$sucursal' AND b.sucursal = '$sucursal'
        GROUP BY 
            a.id, a.producto, a.precio, a.categoria, 
            a.descripcion, a.talla, a.estado, a.oferta;";
        $cons = $con->query($consprod);
        $product = [];
        while($pr = mysqli_fetch_array($cons)){
            $product[] = [
                "id" => $pr['id'],
                "producto" => $pr['producto'],
                "precio" => $pr['precio'],
                "categoria" => $pr['categoria'],
                "descripcion" => $pr['descripcion'],
                "talla" => $pr['talla'],
                "estado" => $pr['estado'],
                "oferta" => $pr['oferta'],
                "portada" => $pr['portada'],
                "unidades" => $pr['unidades_disponibles'],
                "porciones" => $pr['porciones_disponibles']
            ];
        }
        echo json_encode([
            "status" => "success",
            "title" => "menu",
            "message" => $product
        ]);
    }

    if(isset($_GET['cons_cart']) && $_GET['cons_cart'] === $clav) {
        $cons = $con -> prepare("SELECT * FROM sell_cart WHERE usuario = ? AND sucursal = ?");
        $cons -> bind_param('ss',$sesion,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $table = '
                <table class="ingredients_table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Und</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
            ';
            $products = "";
            while($pr = mysqli_fetch_array($Rcons)) {
                $idprod = $pr['id_producto'];
                $nprod = $con -> prepare("SELECT * FROM productos WHERE id = ?");
                $nprod -> bind_param('i',$idprod);
                $nprod -> execute();
                $Rnprod = $nprod -> get_result();
                $actpro = $con -> prepare("SELECT * FROM active_products WHERE id_producto = ?");
                $actpro -> bind_param('i',$idprod);
                $actpro -> execute();
                $Ractpro = $actpro -> get_result();
                $acpr = $Ractpro -> fetch_assoc();
                $rpr = $Rnprod -> fetch_assoc();
                $precioprod = $pr['cantidad']*$acpr['precio'];
                $products .= "
                        <tr>
                            <td>".$rpr['producto']."</td>
                            <td>$".miles($acpr['precio'])."</td>
                            <td>".$pr['cantidad']."</td>
                            <td>$".miles($precioprod)."</td>
                            <td>
                                <button class='delbtn' id='delcart'></button>
                            </td>
                        </tr>
                ";
            }
            $table .= $products.'
                    </tbody>
                </table>
            ';
            echo json_encode([
                "status" => "success",
                "title" => 1,
                "message" => $products
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "empty",
                "message" => "Sin productos en el carrito"
            ]);
        }
    }

    if(isset($_GET['get_sys_data'])){
        $idcaja = 1;
        $usu = $con -> prepare("SELECT u.documento,o.nombre,o.apellido
        FROM usuarios u INNER JOIN operadores o ON o.documento = u.documento
        WHERE u.usuario = ?");
        $usu -> bind_param('s',$sesion);
        $usu -> execute();
        $Rusu = $usu -> get_result();
        $us = $Rusu -> fetch_assoc();
        $nombre = $us['nombre'] ?? 'Sin sesión';
        $apellido = $us['apellido'] ?? '';
        $cons = $con -> prepare("SELECT COALESCE(SUM(total), 0) AS total_vendido FROM ventas WHERE idcaja = ? AND usuario = ? AND sucursal = ?");
        $cons -> bind_param('iss',$idcaja,$sesion,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $tl = $Rcons -> fetch_assoc();
        $total = $tl['total_vendido'] ?? 0;
        $data = [
            "sucursal" => $sucursal,
            "nombre" => $nombre. " " .$apellido,
            "vendido" => miles($total)
        ];
        echo json_encode($data);
    }

?>