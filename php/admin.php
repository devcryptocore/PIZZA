<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('../php/optimizador.php');

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

?>