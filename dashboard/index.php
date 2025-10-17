<?php
error_reporting(E_ALL);
    include('../includes/dashboard-header.php');
    function redirectTo($url) {
        header("Location: $url");
        exit();
    }
    if (!isset($_SESSION['usuario'])) {
        redirectTo("../php/logout.php");
    }
    $opciones = "";
    $ve = $con -> prepare("SELECT u.documento, u.rol, o.nombre, o.foto FROM usuarios u 
    LEFT JOIN operadores o ON u.documento = o.documento WHERE u.usuario = ?;");
    $ve -> bind_param('s',$sesion);
    $ve -> execute();
    $Rve = $ve -> get_result();
    if($Rve -> num_rows > 0) {
        $rp = $Rve -> fetch_assoc();
        $roli = $rp['rol'];
        $nome = $rp['nombre'] ?? 'Indefinido';
        $fot = $rp['foto'] ?? '../res/icons/user.svg';
        if($roli === 'administrador') {
            $opciones = '
                <div class="source-option-container">
                    <button class="source-option" data-source="caja">
                        <img src="../res/icons/sell-reg.svg">
                    </button>
                    <button class="source-option" data-source="ventas">
                        <img src="../res/icons/money-dollar.svg">
                    </button>
                    <button class="source-option" data-source="productos">
                        <img src="../res/icons/pizza-dark.svg">
                    </button>
                    <button class="source-option" data-source="ingredientes">
                        <img src="../res/icons/ingredient.svg">
                    </button>
                    <button class="source-option" data-source="obligaciones">
                        <img src="../res/icons/debt.svg">
                    </button>
                    <button class="source-option" data-source="reportes">
                        <img src="../res/icons/report.svg">
                    </button>
                    <button class="source-option" data-source="empleados">
                        <img src="../res/icons/user.svg">
                    </button>
                    <button class="source-option" data-source="administracion">
                        <img src="../res/icons/admin.svg">
                    </button>
                    <button class="source-option" data-source="empresa">
                        <img src="../res/icons/store.svg">
                    </button>
                    <button class="source-option" data-source="acercade">
                        <img src="../res/icons/about.svg">
                    </button>
                    <button class="source-option" data-source="installap" id="installPWA">
                        <img src="icon-512x512.png" style="width: 35px;">
                    </button>
                </div>
            ';
        }
        if($roli === 'gestionador') {
            $opciones = '
                <div class="source-option-container">
                    <button class="source-option" data-source="caja">
                        <img src="../res/icons/sell-reg.svg">
                    </button>
                    <button class="source-option" data-source="ventas">
                        <img src="../res/icons/money-dollar.svg">
                    </button>
                    <button class="source-option" data-source="productos">
                        <img src="../res/icons/pizza-dark.svg">
                    </button>
                    <button class="source-option" data-source="ingredientes">
                        <img src="../res/icons/ingredient.svg">
                    </button>
                    <button class="source-option" data-source="obligaciones">
                        <img src="../res/icons/debt.svg">
                    </button>
                    <button class="source-option about" data-source="acercade">
                        <img src="../res/icons/about.svg">
                    </button>
                    <button class="source-option" data-source="installap" id="installPWA">
                        <img src="icon-512x512.png" style="width: 35px;">
                    </button>
                </div>
            ';
        }
        if($roli === 'operador') {
            $opciones = '
                <div class="source-option-container">
                    <button class="source-option" data-source="caja">
                        <img src="../res/icons/sell-reg.svg">
                    </button>
                    <button class="source-option" data-source="ventas">
                        <img src="../res/icons/money-dollar.svg">
                    </button>
                    <button class="source-option" data-source="productos">
                        <img src="../res/icons/pizza-dark.svg">
                    </button>
                    <button class="source-option" data-source="ingredientes">
                        <img src="../res/icons/ingredient.svg">
                    </button>
                    <button class="source-option about" data-source="acercade">
                        <img src="../res/icons/about.svg">
                    </button>
                    <button class="source-option" data-source="installap" id="installPWA">
                        <img src="icon-512x512.png" style="width: 35px;">
                    </button>
                </div>
            ';
        }
    }
    else {
        redirectTo("../php/logout.php");
    }
?>
<body>
    <div class="parent">
        <div class="div2 option-container">
            <div class="opeinfo">
                <img src="<?=$fot;?>" alt="Imagen operador">
                <span><?=$nome;?></span>
            </div>
            <div class="logo-container"></div>
            <?=$opciones;?>
            <button class="source-option" data-source="logout" id="logout_button">
                <img src="../res/icons/exit.svg">
            </button>
        </div>
        <div class="div3 source-container" id="sources">
            <iframe src="../admin/caja.php" frameborder="0" id="sites_container"></iframe>
        </div>
    </div>
</body>
</html>