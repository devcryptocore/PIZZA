<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

    if(isset($_GET['get_invoices']) && $_GET['get_invoices']) {
        $cons = $con -> prepare("SELECT DISTINCT consecutivo, idventa, fechareg FROM ventas WHERE usuario = ? AND sucursal = ?");
        $cons -> bind_param('ss',$sesion,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $mess = "";
        if($Rcons -> num_rows > 0) {
            while($fc = $Rcons -> fetch_assoc()) {
                $mess .= '
                    <tr class="thisfac" onclick="open_bill(\''.$fc['consecutivo'].'\',\'rev\')">
                        <td>'.$fc['consecutivo'].'</td>
                        <td>'.$fc['idventa'].'</td>
                        <td>'.$fc['fechareg'].'</td>
                    </tr>
                ';
            }
        }
        else {
            $mess = "Sin facturas para mostrar";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Facturas de " . $sesion,
            "message" => $mess
        ]);
    }


?>