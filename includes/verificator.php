<?php
    session_start();
    $sesion = $_SESSION['usuario'] ?? '';

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('../php/optimizador.php');
    include('../php/logger.php');

    $verif12 = $con -> prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $verif12 -> bind_param('s',$sesion);
    $verif12 -> execute();
    $Rverfi12 = $verif12 -> get_result();
    if($Rverfi12 -> num_rows > 0) {
        $v12 = $Rverfi12 -> fetch_assoc();
        $rol = $v12['rol'];
        $sucursal = $v12['sucursal'];
    }
    else {
        $sesion = "SET_NULL";
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
        $bcon = $con->prepare("SELECT codcaja FROM caja WHERE usuario = ? AND sucursal = ? ORDER BY id DESC LIMIT 1");
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

?>