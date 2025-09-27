<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    $sucursal = "las_americas";
    $sesion = "admin";//DUMMIE PARA SESION DE USUARIO

    if(isset($_GET['set_new_employee']) && $_GET['set_new_employee'] === $clav) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $documento = $_POST['documento'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $ins = $con -> prepare("INSERT INTO operadores (nombre,apellido,documento,telefono,direccion,email) VALUES (?,?,?,?,?,?)");
        $ins -> bind_param('ssssss',$nombre,$apellido,$documento,$telefono,$direccion,$email);
        if($ins -> execute()) {
            echo json_encode([
                "status" => "success",
                "title" => "Empleado registrado",
                "message" => "Se ha almacenado la información de ".$nombre
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Ha ocurrido un error!",
                "message" => "No se ha podido almacenar la información de ".$nombre
            ]);
        }
    }

?>