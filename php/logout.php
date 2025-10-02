<?php
    session_start();
    $_SESSION = [];
    session_destroy();
    echo json_encode([
        "status" => "success",
        "source" => "../login/"
    ]);
    exit;
?>