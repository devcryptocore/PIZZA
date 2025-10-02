<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');

    if(isset($_GET['go_login']) && $_GET['go_login'] === $clav){
        session_start();

        if(empty($_POST['username']) || empty($_POST['userpasswd'])){
            die(json_encode([
                "status" => "error",
                "title" => "Campos incompletos",
                "message" => [
                    "text" => "Todos los campos son obligatorios!",
                    "source" => ""
                ]
            ]));
        }

        $user = htmlspecialchars(trim($_POST['username']));
        $pass = htmlspecialchars($_POST['userpasswd']);

        $usr = $con -> prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $usr -> bind_param('s',$user);
        $usr -> execute();
        $Rusr = $usr -> get_result();
        if($us = $Rusr -> fetch_assoc()){
            if(password_verify($pass, $us['contrasena'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $us['id'];
                $_SESSION['usuario'] = $us['usuario'];
                $_SESSION['role'] = $us['role'];
                $_SESSION['logged_in'] = true;

                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => [
                        "text" => "Ingreso autorizado, ingresando al sistema..",
                        "source" => "../dashboard/"
                    ] 
                ]);

            }
            else {
                echo json_encode([
                        "status" => "error",
                        "title" => "Error!",
                        "message" => [
                            "text" => "La contraseña ingresada es incorrecta!",
                            "source" => ""
                        ]
                    ]);
            }
        }
        else {
            echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => [
                        "text" => "No se ha encontrado a este usuario en el sistema!",
                        "source" => ""
                    ]
                ]);
        }
        $usr -> close();
        $con -> close();

    }

?>