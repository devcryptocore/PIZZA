<?php
    include('../includes/header.php');
?>
<link rel="stylesheet" href="../css/ingredients.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/products.css?v=<?=$version;?>">
<link rel="stylesheet" href="../css/empresa.css?v=<?=$version;?>">
<script type="module" src="../js/empresa.js?v=<?=$version;?>"></script>
<body>
    <div class="headbar">
        <h1>Empresa</h1>
        <button class="stkbutton" id="modCompany" onclick="modcomp()">Actualizar</button>
        <button class="stkbutton" id="sucursales" onclick="cursals()">Sucursales</button>
        <button class="stkbutton" id="nosotros" onclick="nosotrostext()">Nosotros</button>
        <button class="stkbutton" id="faqs" onclick="faqstext()">FAQs</button>
    </div>
    <div class="cont-page">
        <div class="sucont">
            <div class="sides leftside">
                <img src="../res/icons/image.svg" alt="Company Logo" id="comlogo">
                <div class="title-container">
                    <span id="company_title">Compañia</span>
                </div>
            </div>
            <div class="sides rightside">
                <div class="data-camp">
                    <ul>
                        <li><b>Teléfono 1:</b> <span id="telcamp">3100000000</span></li>
                        <li><b>Teléfono 2:</b> <span id="telcamp2">3100000000</span></li>
                        <li><b>E-Mail:</b> <span id="mailcamp">company@example.com</span></li>
                        <li><b>Dirección:</b> <span id="dircamp">Cra 2 #45-56 Centro</span></li>
                        <li><b>NIT:</b> <span id="nitcamp">123456789-9</span></li>
                        <li><b>Administrador:</b> <span id="nomcamp">Jhon Doe</span></li>
                        <li><b>Documento:</b> <span id="doccamp">123456789</span></li>
                        <li><b>Registrado desde:</b> <span id="fechcamp">01-01-2000</span></li>
                    </ul>
                </div>
                <div class="sucursales"></div>
            </div>
        </div>
    </div>
</body>
</html>