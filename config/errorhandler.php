<?php

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    header('Content-Type: application/json; charset=utf-8');

    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "title"   => "Error interno",
            "message" => "$errstr en $errfile:$errline"
        ]);
        exit;
    });

    set_exception_handler(function($e) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "title"   => "Excepción",
            "message" => $e->getMessage()
        ]);
        exit;
    });

?>