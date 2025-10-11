<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('../php/optimizador.php');

    if(isset($_GET['get_roulette']) && $_GET['get_roulette'] === $exclav) {
        $cons = $con -> prepare("SELECT * FROM ruleta");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $rl = $Rcons -> fetch_assoc();
            echo json_encode([
                "status" => "success",
                "title" => "Ok",
                "message" => [
                    "estado" => $rl['estado'],
                    "premio1" => $rl['premio1'],
                    "premio1" => $rl['premio1'],
                    "premio2" => $rl['premio2'],
                    "premio3" => $rl['premio3'],
                    "premio4" => $rl['premio4'],
                    "premio5" => $rl['premio5'],
                    "premio6" => $rl['premio6'],
                    "premiada" => $rl['premiada'],
                    "premio" => $rl['premio'],
                    "unico" => $rl['unico']
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "empty"
            ]);
        }
    }

?>