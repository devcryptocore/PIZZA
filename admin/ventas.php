<?php
    include('../includes/header.php');
    if(isset($_GET['terminal'])){
        $nterminal = intval($_GET['terminal']) + 1;
    }
    else {
        $nterminal = 1;
    }
?>
<link rel="stylesheet" href="../css/ventas.css" class="versionized">
<script type="module" src="../js/ventas.js" class="versionized"></script>
<body>
    <input type="hidden" name="nterm" id="terminalId" value="<?=$nterminal;?>">
    <div class="parent">
        <div class="div1">
            <div class="logo-container"></div>
            <div class="values-container">
                <span class="descuento">Descuento: <b id="procDesc">0%</b> $0.000</span>
                <input type="text" id="barcode_reciever">
                <span class="totalcont">Total: <h2 id="totalPrice">$0.000</h2></span>
            </div>
        </div>
        <div class="div2">
            <div class="searchbar-container">
                <div class="sell-data" id="clientData">
                    <b>Cliente:</b>
                    <span>Indefinido</span>
                    <span>123456789</span>
                </div>
                <input type="text" id="product_search" placeholder="Producto" autocomplete="off">
                <div id="prod_result"></div>
            </div>
            <div id="sell-details">
                <div class="sell-details-container">
                    <div class="sell-data" id="organization">
                        <b>Organización:</b>
                        <span>Organización</span>
                    </div>
                    <div class="sell-data" id="sucursalName">
                        <b>Sucursal:</b>
                        <span>Sucursal</span>
                    </div>
                    <div class="sell-data" id="sellerName">
                        <b>Vendedor:</b>
                        <span>Usuario</span>
                    </div>
                    <div class="sell-data" id="selledData">
                        <b>Vendido:</b>
                        <span>$0.000</span>
                    </div>
                    <button id="barcodefocus"></button>
                </div>
                <div class="cbuttons">
                    <button class="sbutton history" onclick="get_history()"></button>
                    <button class="sbutton invoices" onclick="get_invoices()"></button>
                    <button class="sbutton clients" onclick="clients()"></button>
                    <button class="sbutton rollbacks" onclick="rollback()"></button>
                </div>
                <div class="devcontainer">
                    <h2 id="devuelta">Cambio: $0</h2>
                    <select name="metodopago" id="metodoPago">
                        <option value="efectivo" selected>Efectivo</option>
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                        <option value="bancolombia">Bancolombia</option>
                        <option value="davivienda">Davivienda</option>
                        <option value="consignacion">Consignación</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>
            <div class="sell_actions">
                <input type="tel" id="field_change" placeholder="Recibido" autocomplete="off">
                <button id="make_sell">Realizar venta</button>
            </div>
        </div>
        <div class="div3">
            <div class="">
                <table class="table-container ingredients_table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Precio Und.</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="addedProducts"></tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="client" id="client" value="">
    <input type="hidden" name="doc_client" id="doc_client" value="">
    <input type="hidden" name="tval" id="totalVal" value="0">
</body>
</html>