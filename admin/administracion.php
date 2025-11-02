<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?<?=$version;?>">
<link rel="stylesheet" href="../css/employee.css?<?=$version;?>">
<link rel="stylesheet" href="../css/caja.css?<?=$version;?>">
<link rel="stylesheet" href="../css/administration.css?<?=$version;?>">
<script type="module" src="../js/administration.js?<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Administración</h1>
        <button id="set_roulette" class="stkbutton movement">Ruleta</button>
        <button id="set_bot" class="stkbutton telegram">Telegram</button>
        <button id="set_clean" class="stkbutton close_box">Sys clean</button>
    </div>
    <div class="admin-source-container">
        <iframe src="../index.html" frameborder="0"></iframe>
    </div>
</body>
</html>