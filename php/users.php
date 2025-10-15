<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');

    if(isset($_GET['set_orgdata']) && $_GET['set_orgdata'] === $clav) {
        $organizacion = htmlspecialchars($_POST['organizacion']);
        $ptelefono = htmlspecialchars($_POST['ptelefono']);
        $stelefono = htmlspecialchars($_POST['stelefono']) ?? '';
        $email = htmlspecialchars($_POST['email']);
        $direccion = htmlspecialchars($_POST['direccion']);
        $nit = htmlspecialchars($_POST['nit']);
        $encargado = htmlspecialchars($_POST['encargado']);
        $documento = htmlspecialchars($_POST['documento']);
        $logo = "../res/icons/image.svg";
        if(isset($_FILES['logotipo'])) {
            $dir = "../res/images/company/";
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $logo = guardarFoto("logotipo", "logo", $dir);
        }
        $con -> begin_transaction();
        try {
            $ins = $con -> prepare("INSERT INTO company (organizacion,ptelefono,stelefono,email,direccion,nit,encargado,documento,logotipo)
            VALUES (?,?,?,?,?,?,?,?,?)");
            $nom = explode(" ",$encargado);
            $nome = $nom[0] ?? $encargado;
            $ape = $nom[1] ?? '';
            $ins -> bind_param('sssssssss',$organizacion,$ptelefono,$stelefono,$email,$direccion,$nit,$encargado,$documento,$logo);
            if($ins -> execute()) {
                $empl = $con -> query("INSERT INTO operadores (nombre,apellido,documento,telefono,direccion,email,foto) VALUES
                ('$nome','$ape','$documento','$ptelefono','$direccion','$email','$logo')");
                $enti = $con -> query("INSERT INTO entidades (efectivo,nequi,daviplata,bancolombia,consignacion,otro)
                VALUES (0,0,0,0,0,0)");
                $_GET['create_triggers'] = "bWF4cGl6emFsYXVuaW9u";
                require "trigger.php";
                $con -> commit();
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Se han almacenado los datos de su compañia!"
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "Ha ocurrido un error al procesar sus datos: " . $con -> error
                ]);
            }
        }
        catch (Exception $e){
            $con -> rollback();
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error al procesar sus datos: " . $e -> getMessage()
            ]);
        }

    }

    if(isset($_GET['orgdata']) && $_GET['orgdata'] === $clav) {
        $cons = $con -> prepare("SELECT * FROM company");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $org = $Rcons -> fetch_assoc();
            $cus = $con -> prepare("SELECT * FROM usuarios WHERE documento = ?");
            $cus -> bind_param("s",$org['documento']);
            $cus -> execute();
            $Rcus = $cus -> get_result();
            if($Rcus -> num_rows > 0){
                echo json_encode([
                    "status" => "success",
                    "title" => "ok",
                    "message" => [
                        "organizacion" => $org['organizacion'],
                        "ptelefono" => $org['ptelefono'],
                        "stelefono" => $org['stelefono'] ?? '',
                        "email" => $org['email'],
                        "direccion" => $org['direccion'],
                        "nit" => $org['nit'],
                        "encargado" => $org['encargado'],
                        "documento" => $org['documento'],
                        "logotipo" => $org['logotipo']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            }
            else {
                echo json_encode([
                    "status" => "nouser",
                    "title" => "ok",
                    "message" => [
                        "organizacion" => $org['organizacion'],
                        "ptelefono" => $org['ptelefono'],
                        "stelefono" => $org['stelefono'] ?? '',
                        "email" => $org['email'],
                        "direccion" => $org['direccion'],
                        "nit" => $org['nit'],
                        "encargado" => $org['encargado'],
                        "documento" => $org['documento'],
                        "logotipo" => $org['logotipo']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            }
        }
        else {
            echo json_encode([
                "status" => "empty",
                "title" => "",
                "message" => ""
            ]);
        }
    }

    if(isset($_GET['set_admindata']) && $_GET['set_admindata'] === $clav) {
        $usuario = sanear_string(htmlspecialchars($_POST['usuario']));
        $confcontrasena = htmlspecialchars($_POST['conf-contrasena']);
        $contrasena = htmlspecialchars($_POST['contrasena']);
        $documento = htmlspecialchars($_POST['documento']);
        $rol = htmlspecialchars($_POST['rol']);
        $sucursal = htmlspecialchars($_POST['sucursal']);
        $estado = 1;
        $password = password_hash($contrasena, PASSWORD_DEFAULT);

        if($contrasena !== $confcontrasena){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Las contraseñas no coinciden"
            ]);
            exit;
        }

        $ins = $con -> prepare("INSERT INTO usuarios (documento,rol,usuario,contrasena,sucursal,estado) VALUES (?,?,?,?,?,?)");
        $ins -> bind_param('sssssi',$documento,$rol,$usuario,$password,$sucursal,$estado);
        if($ins -> execute()) {
            echo json_encode([
                "status" => "success",
                "title" => "Configuración completada!",
                "message" => "Se han almacenado sus datos, por favor inicie sesión con sus credenciales!"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido realizar el registro del administrador: " . $con -> error
            ]);
        }
    }

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
        if($Rusr -> num_rows > 0){
            $us = $Rusr -> fetch_assoc();
            if($us['estado'] == 0){
                die(json_encode([
                    "status" => "warning",
                    "title" => "Usuario inactivo!",
                    "message" => [
                        "text" => "Se ha restringido su acceso al sistema!",
                        "source" => ""
                    ]
                ]));
            }
            if(password_verify($pass, $us['contrasena'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $us['id'];
                $_SESSION['usuario'] = $us['usuario'];
                $_SESSION['rol'] = $us['rol'];
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