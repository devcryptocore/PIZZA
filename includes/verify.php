<?php

    session_start();
    include('../config/connector.php');

    if(isset($_GET['verify']) && $_GET['verify'] === $clav){
        if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            echo json_encode([
                "status" => "no_logged",
                "source" => "../login/"
            ]);
            exit;
        }
        else {
            echo json_encode([
                "status" => "",
                "source" => ""
            ]);
            exit;
        }
    }

?>