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

    if(isset($_GET['boxhistory']) && $_GET['boxhistory'] === $clav){
        $cons = $con -> prepare("SELECT *,COALESCE(SUM(ventas),0) AS t_ventas,
        COALESCE(SUM(ingresos),0) AS t_ingresos, COALESCE(SUM(egresos),0) AS t_egresos FROM caja WHERE usuario = ? AND sucursal = ? ");
        $cons -> bind_param('ss',$sesion,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {

        }
    }

?>