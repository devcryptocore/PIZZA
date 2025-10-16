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

?>