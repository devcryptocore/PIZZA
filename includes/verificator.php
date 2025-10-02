<?php
    session_start();
    $sesion = $_SESSION['usuario'];

    $verif12 = $con -> prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $verif12 -> bind_param('s',$sesion);
    $verif12 -> execute();
    $Rverfi12 = $verif12 -> get_result();
    if($Rverfi12 -> num_rows > 0) {
        $v12 = $Rverfi12 -> fetch_assoc();
        $rol = $v12['role'];
        $sucursal = $v12['sucursal'];
    }
    else {
        die("Sin sesión");
    }

?>