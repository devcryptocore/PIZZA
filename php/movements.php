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

    if(isset($_GET['set_entidad']) && $_GET['set_entidad'] === $clav){
        $entidad = $_POST['entidad'];
        $inicial = sanear_string($_POST['inicial']);
        $cons = $con -> prepare("SELECT entidad FROM entidades WHERE entidad = ?");
        $cons -> bind_param('s',$entidad);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "La entidad ya ha sido establecida"
            ]);
            exit;
        }
        $ins = $con -> prepare("INSERT INTO entidades (entidad,inicial,monto) VALUES (?, ?, ?)");
        $ins -> bind_param('sii',$entidad,$inicial,$inicial);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha creado ".$entidad." y se ha almacenado los fondos."
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido crear la entidad"
            ]);
            exit;
        }
    }

?>