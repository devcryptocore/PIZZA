<?php

    $isJsonRequest = 
    isset($_SERVER['HTTP_ACCEPT']) && 
    str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    if ($isJsonRequest) {
        header('Content-Type: application/json; charset=utf-8');
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    set_error_handler(function($errno, $errstr, $errfile, $errline) use ($isJsonRequest) {
        http_response_code(500);
        $message = "$errstr en $errfile:$errline";

        if ($isJsonRequest) {
            echo json_encode([
                "status"  => "error",
                "title"   => "Error interno",
                "message" => $message
            ]);
        } else {
            echo "<div style='padding:10px;background:#fee;border:1px solid #c00;color:#900'>
                    <b>Error interno:</b> $message
                </div>";
        }
        exit;
    });
    set_exception_handler(function($e) use ($isJsonRequest) {
        http_response_code(500);
        $message = $e->getMessage();

        if ($isJsonRequest) {
            echo json_encode([
                "status"  => "error",
                "title"   => "Excepción",
                "message" => $message
            ]);
        } else {
            echo "<div style='padding:10px;background:#fee;border:1px solid #c00;color:#900'>
                    <b>Excepción:</b> $message
                </div>";
        }
        exit;
    });

?>