<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

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

    if(isset($_GET['constate'])) {
        $estado = statebox() ? "open" : "close";
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $estado
        ]);
    }

    if(isset($_GET['boxhistory']) && $_GET['boxhistory'] === $clav){
        $cons = $con -> prepare("SELECT * FROM caja WHERE usuario = ? AND sucursal = ? ORDER BY id DESC");
        $cons -> bind_param('ss',$sesion,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $trs = "";
        if($Rcons -> num_rows > 0) {
            while($box = mysqli_fetch_array($Rcons)){
                $estado = $box['estado'] == 1 ? '<span class="state open">Abierto</span>' : '<span class="state closed">Cerrado</span>';
                $trs .= '
                    <tr>
                        <td>'.$box['codCaja'].'</td>
                        <td>'.$estado.'</td>
                        <td>$'.miles($box['base']).'</td>
                        <td>$'.miles($box['ventas']).'</td>
                        <td>$'.miles($box['descuentos']).'</td>
                        <td>$'.miles($box['ingresos']).'</td>
                        <td>$'.miles($box['egresos']).'</td>
                        <td>'.$box['sucursal'].'</td>
                        <td>'.$box['usuario'].'</td>
                        <td>'.$box['fecha'].'</td>
                    </tr>
                ';
            }
        }
        else {
            $trs .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Estados de caja vacíos</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            ';
        }
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $trs
        ]);
    }

?>