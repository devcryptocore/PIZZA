<?php

    include('../includes/verificator.php');

    if(isset($_GET['get_invoices']) && $_GET['get_invoices'] === $clav) {
        $facnum = $_POST['facnum'];
        $cons = $con -> prepare("SELECT DISTINCT consecutivo FROM ventas WHERE consecutivo = ? AND sucursal = ?");
        $cons -> bind_param('ss',$facnum,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $mess = "";
        if($Rcons -> num_rows > 0) {
            $fac = $Rcons -> fetch_assoc();
            $mess = $fac['consecutivo'];
            echo json_encode([
                "status" => "success",
                "title" => "Factura encontrada",
                "message" => $mess
            ]);
        }
        else {
            $mess = "El número de factura ingresado no coincide con ninguna factura de esta sucursal";
            echo json_encode([
                "status" => "error",
                "title" => "Factura no encontrada",
                "message" => $mess
            ]);
        }
    }

    if(isset($_GET['get_to_rollback']) && $_GET['get_to_rollback'] === $clav) {
        $idventa = $_POST['idventa'];
        $cons = $con -> prepare("SELECT * FROM ventas WHERE idventa = ? AND sucursal = ?");
        $cons -> bind_param('ss',$idventa,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $mess = "";
        if($Rcons -> num_rows > 0) {
            while($fc = $Rcons -> fetch_assoc()) {
                $mess .= '
                    <tr class="thisfac" onclick="set_rollback(\''.$fc['id'].'\',\''.$fc['cantidad'].'\',\''.$fc['producto'].'\')">
                        <td style="font-size:10px;">'.$fc['consecutivo'].'</td>
                        <td style="font-size:10px;">'.$fc['producto'].'</td>
                        <td style="font-size:10px;">'.$fc['cantidad'].'</td>
                        <td style="font-size:10px;">$'.miles($fc['precio']).'</td>
                        <td style="font-size:10px;">$'.miles($fc['total']).'</td>
                        <td style="font-size:10px;">$'.miles($fc['descuento']).'</td>
                    </tr>
                ';
            }
        }
        else {
            $mess = "El código ingresado no coincide con las facturas de esta sucursal";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Venta realizada por " . $sesion,
            "message" => $mess
        ]);
    }

    if(isset($_GET['history_invoices']) && $_GET['history_invoices'] === $clav) {
        $fc = $con -> prepare("SELECT * FROM ventas WHERE usuario = ? AND sucursal = ? ORDER BY consecutivo DESC LIMIT 20");
        $fc -> bind_param('ss',$sesion,$sucursal);
        $fc -> execute();
        $Rfc = $fc -> get_result();
        $mess = "";
        if($Rfc -> num_rows > 0) {
            while($fct = $Rfc -> fetch_assoc()) {
                $mess .= '
                    <tr class="thisfac" onclick="open_bill(\''.$fct['consecutivo'].'\',\'rev\')">
                        <td style="font-size:10px;">'.$fct['consecutivo'].'</td>
                        <td style="font-size:10px;">'.$fct['producto'].'</td>
                        <td style="font-size:10px;">'.$fct['cantidad'].'</td>
                        <td style="font-size:10px;">$'.miles($fct['precio']).'</td>
                        <td style="font-size:10px;">$'.miles($fct['total']).'</td>
                        <td style="font-size:10px;">$'.miles($fct['descuento']).'</td>
                    </tr>
                ';
            }
        }
        else {
            $mess = "El código ingresado no coincide con las facturas de esta sucursal";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Ventas realizadas por " . $sesion,
            "message" => $mess
        ]);
    }

?>