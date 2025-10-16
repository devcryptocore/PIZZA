<?php

    include('../includes/verificator.php');

    if(isset($_GET['set_roulette']) && $_GET['set_roulette'] === $clav) {
        $estado = 1;
        $premio1 = $_POST['premio1'];
        $premio2 = $_POST['premio2'];
        $premio3 = $_POST['premio3'];
        $premio4 = $_POST['premio4'];
        $premio5 = $_POST['premio5'];
        $premio6 = $_POST['premio6'];
        $premiada = [
            $_POST['premiada1'] ?? '',
            $_POST['premiada2'] ?? '',
            $_POST['premiada3'] ?? '',
            $_POST['premiada4'] ?? '',
            $_POST['premiada5'] ?? '',
            $_POST['premiada6'] ?? ''
        ];
        $premiada = json_encode($premiada);
        $premio = $_POST['premio'];
        $intentos = $_POST['intentos'];
        $unico = uniqid();
        $ins = $con -> prepare("INSERT INTO ruleta (estado,premio1,premio2,premio3,premio4,premio5,premio6,premiada,premio,intentos,unico)
         VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $ins -> bind_param('issssssssis',$estado,$premio1,$premio2,$premio3,$premio4,$premio5,$premio6,$premiada,$premio,$intentos,$unico);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha activado la ruleta, ahora aprecerá en la página principal."
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido activar la ruleta: " . $ins -> error
            ]);
        }
    }

    if(isset($_GET['set_clean']) && $_GET['set_clean'] === $clav) {
        $pass = trim($_POST['contrasena']);
        $vrf = $_POST['gene'];
        $campo = $_POST['verif'];
        $cons = $con -> prepare("SELECT rol,usuario,contrasena FROM usuarios WHERE usuario = ?");
        $cons -> bind_param('s',$sesion);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0){
            $us = $Rcons -> fetch_assoc();
            if($us['rol'] !== 'administrador'){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "Usted no tiene autorización para realizar esta acción!"
                ]);
                exit;
            }
            if(!password_verify($pass,$us['contrasena'])){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "Contraseña incorrecta!"
                ]);
                exit;
            }
            if($campo !== $vrf) {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "Código de verificación incorrecto!"
                ]);
                exit;
            }
            try {
                $nombreDB = $con->query("SELECT DATABASE()")->fetch_row()[0];
                $con->query("SET FOREIGN_KEY_CHECKS = 0");
                $tablas = $con->query("SHOW TABLES");
                while ($fila = $tablas->fetch_array()) {
                    $tabla = $fila[0];
                    $con->query("TRUNCATE TABLE `$tabla`");
                }
                $triggers = $con->query("SHOW TRIGGERS FROM `$nombreDB`");
                while ($fila = $triggers->fetch_assoc()) {
                    $trigger = $fila['Trigger'];
                    $con->query("DROP TRIGGER IF EXISTS `$trigger`");
                }
                $con->query("SET FOREIGN_KEY_CHECKS = 1");
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "El sistema se ha reestablecido correctamente"
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "No se ha podido reestablecer el sistema: " . $e -> getMessage()
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Usuario no encontrado"
            ]);
        }
    }

?>