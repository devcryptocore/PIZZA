<?php

    include('../includes/verificator.php');

    if(isset($_GET['get_minimo']) && $_GET['get_minimo']  === $clav) {
        if($_SESSION['sucursal'] == 'system'){
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE stock <= stock_minimo AND (vencimiento >= CURDATE() OR vencimiento IS NULL) ORDER BY stock ASC");
        }
        else {
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE stock <= stock_minimo AND (vencimiento >= CURDATE() OR vencimiento IS NULL) AND sucursal = ? ORDER BY stock ASC");
            $consu -> bind_param('s',$sucursal);
        }
        $consu -> execute();
        $Rconsu = $consu -> get_result();
        if($Rconsu -> num_rows > 0){
            $res = "";
            while($ig = $Rconsu -> fetch_assoc()){
                $bkg = $ig['stock'] >= 0 ? '#fa000061' : '#5100ff69';
                $res .= '
                    <tr style="background:'.$bkg.';" class="elem-busqueda">
                        <td>'.$ig['id'].'</td>
                        <td>'.$ig['nombre'].'</td>
                        <td>'.$ig['unidad'].'</td>
                        <td>'.$ig['stock'].'</td>
                        <td>'.$ig['stock_minimo'].'</td>
                    </tr>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "insumos" => $res,
                    "numero" => $Rconsu -> num_rows
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "insumos" => "No hay productos por agotarse",
                    "numero" => $Rconsu -> num_rows
                ]
            ]);
        }
        
    }

    if(isset($_GET['get_caducados']) && $_GET['get_caducados']  === $clav) {
        $mst = 0;
        $dy = 5;
        if($_SESSION['sucursal'] == 'system'){
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE
             stock > ? AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY vencimiento ASC");
            $consu -> bind_param('ii',$mst,$dy);
        }
        else {
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE
             stock > ? AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND sucursal = ? ORDER BY vencimiento ASC");
            $consu -> bind_param('iis',$mst,$dy,$sucursal);
        }
        $consu -> execute();
        $Rconsu = $consu -> get_result();
        if($Rconsu -> num_rows > 0){
            $res = "";
            while($ig = $Rconsu -> fetch_assoc()){
                $bkg = strtotime(date('Y-m-d')) >= strtotime($ig['vencimiento']) ? '#fa004f61' : '#ff9d0093';
                $res .= '
                    <tr style="background:'.$bkg.';" class="elem-busqueda">
                        <td>'.$ig['id'].'</td>
                        <td>'.$ig['nombre'].'</td>
                        <td>'.$ig['unidad'].'</td>
                        <td>'.$ig['stock'].'</td>
                        <td>'.$ig['vencimiento'].'</td>
                    </tr>
                ';
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "insumos" => $res,
                    "numero" => $Rconsu -> num_rows
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "insumos" => "No hay productos caducados",
                    "numero" => $Rconsu -> num_rows
                ]
            ]);
        }
    }

    if(isset($_GET['get_caducados_notify']) && $_GET['get_caducados_notify']  === $clav) {
        $mst = 0;
        $dy = 5;
        if($_SESSION['sucursal'] == 'system'){
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE
             stock > ? AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY vencimiento ASC");
            $consu -> bind_param('ii',$mst,$dy);
        }
        else {
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE
             stock > ? AND vencimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND sucursal = ? ORDER BY vencimiento ASC");
            $consu -> bind_param('iis',$mst,$dy,$sucursal);
        }
        $consu -> execute();
        $Rconsu = $consu -> get_result();
        echo json_encode([
            "status" => "caducados",
            "title" => "ok",
            "message" =>  [
                "texto" => "Hay " . $Rconsu -> num_rows . " insumos por caducar!",
                "numero" => $Rconsu -> num_rows
            ]
        ]);
    }

    if(isset($_GET['get_minimo_notify']) && $_GET['get_minimo_notify']  === $clav) {
        if($_SESSION['sucursal'] == 'system'){
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE stock <= stock_minimo AND vencimiento >= CURDATE() ORDER BY stock ASC");
        }
        else {
            $consu = $con -> prepare("SELECT * FROM ingredientes WHERE stock <= stock_minimo AND vencimiento >= CURDATE() AND sucursal = ? ORDER BY stock ASC");
            $consu -> bind_param('s',$sucursal);
        }
        $consu -> execute();
        $Rconsu = $consu -> get_result();
        echo json_encode([
            "status" => "minimo",
            "title" => "ok",
            "message" =>  [
                "texto" => "Hay " . $Rconsu -> num_rows . " insumos por agotarse!",
                "numero" => $Rconsu -> num_rows
            ]
        ]);
        
    }

?>