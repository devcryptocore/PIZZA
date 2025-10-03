<?php
    include('../config/connector.php');
    include('../php/funcion.php');
    if(isset($_GET['num'])){
        $rev = isset($_GET['rev']) ? 1 : 0;
        $facnum = $_GET['num'];
        $vendedor = "";
        $sucursal = "";
        $fechareg = "";
        $recibido = "";
        $idventa = "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura No. <?=$facnum;?></title>
    <script src="../js/script.js"></script>
</head>
<style>
    * {
        box-sizing: border-box;
    }
    body {
        width: 400px;
        margin: 0;
        padding: 0;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 20px;
    }
    .logo {
        width: 100px;
        aspect-ratio: 1/1;
        object-fit: contain;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table thead tr {
        background: #1f1f1f;
    }
    table thead tr th {
        color: #fff;
        font-size: 12px;
        text-align: center;
        padding: 3px;
    }
    table tbody tr td {
        text-align: center;
        font-size: 11px;
        padding: 5px 3px;
    }
    table tbody tr td:nth-child(1){
        text-align: left;
    }
    .cinfo {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 1px;
        font-size: 11px;
    }
    .cdata {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px dotted;
    }
    .footer span {
        font-size: 9px;
    }
    .factext {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        font-size: 10px;
        background: url(../res/images/logotype_bn.png) center / 60% no-repeat;
    }
    .factext p {
        margin: 0;
    }
    .factext ul {
        margin: 0;
        padding: 0px 15px;
    }
    @media print {
        body {
            width: 80mm;
            margin: 0;
        }
    }
</style>
<body>
    <div class="container">
        <img src="../res/images/logo.jpeg" alt="company logo" class="logo">
        <div class="cinfo">
            <div class="cdata">
                <b>Factura No.</b><span><?=$facnum;?></span>
            </div>
            <div class="cdata">
                <b>Cliente:</b><span id="clinom">Indefinido / 123456789</span>
            </div>
            <div class="cdata">
                <b>Fecha:</b><span id="fechafac">01/01/2000</span>
            </div>
            <div class="cdata">
                <b>Vendedor:</b><span id="vendedor">Vendedor</span>
            </div>
            <div class="cdata">
                <b>Sucursal:</b><span id="sucursal">Sucursal</span>
            </div>
            <div class="cdata">
                <b>Dirección:</b><span id="sucursal">Cra 7a #15-18 B/Las Américas</span>
            </div>
            <div class="cdata">
                <b>Contacto:</b><span id="sucursal">310 000 0000 - 312 000 0000</span>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $fac = $con -> prepare("SELECT * FROM ventas WHERE consecutivo = ?");
                    $fac -> bind_param('s',$facnum);
                    $fac -> execute();
                    $Rfac = $fac -> get_result();
                    if($Rfac ->num_rows > 0){
                        $total = 0;
                        while($f = $Rfac -> fetch_assoc()){
                            $dsc = '';
                            $total += $f['total'];
                            if($f['descuento'] > 0){
                                $dsc = 'style="background:url(../res/icons/offer-grey.svg) right / 15px no-repeat;"';
                            }
                            echo '
                                <tr>
                                    <td '.$dsc.'>'.$f['producto'].'</td>
                                    <td>'.$f['cantidad'].'</td>
                                    <td>$'.miles($f['precio']).'</td>
                                    <td>$'.miles($f['total']).'</td>
                                </tr>
                            ';
                            $vendedor = $f['usuario'];
                            $sucursal = $f['sucursal'];
                            $fechareg = $f['fechareg'];
                            $recibido = $f['recibido'];
                            $idventa = $f['idventa'];
                            $cliente = $f['cliente'];
                            $clidoc = $f['clidoc'];
                        }
                        $cuser = $con -> prepare("SELECT u.documento,o.* FROM usuarios u INNER JOIN operadores o
                        ON u.documento = o.documento WHERE u.usuario = ?");
                        $cuser -> bind_param('s',$vendedor);
                        $cuser -> execute();
                        $Rcuser = $cuser -> get_result();
                        $nom = $Rcuser -> fetch_assoc();
                        $vendedor = $nom['nombre'] ?? $vendedor . " " . $nom['apellido'] ?? '';
                        echo '
                            <tr style="background:#1f1f1f;"><td></td><td></td><td></td><td></td></tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:left;font-size:12px;"><b>Total:</b></td>
                                <td style="font-size:12px;">$'.miles($total).'</td>
                            </tr>
                        ';
                    }
                    else {
                        header("Location: ../");
                    }
                ?>
            </tbody>
        </table>
        <div class="factext">
            <b>Gracias por su compra</b>
            <span>Apreciamos su confianza en nuestros productos.</span>
            <p>
                Esta factura constituye el soporte legal de la transacción realizada, de acuerdo con las normas tributarias vigentes. Guárdela como comprobante de pago.
            </p>
            <b>Condiciones de uso:</b>
            <ul>
                <li>Esta factura es personal e intransferible.</li>
                <li>No constituye título valor.</li>
                <li>Para cambios, garantías o reclamaciones, por favor conserve este documento y comuníquese con nosotros dentro de los plazos legales establecidos.</li>
            </ul>
            <b>Aviso legal:</b>
            <span>El uso de nuestros productos/servicios implica la aceptación de nuestras políticas de calidad, garantía y privacidad.</span>
            <span>Para solicitar devoluciones por favor use este código: <b><?=$idventa;?></b></span>
            <b style="margin: 0 auto;margin-top:10px;">¡Gracias por preferirnos!</b>
            <i style="margin: 0 auto;">Seguiremos trabajando para brindarle la mejor atención.</i>
        </div>
        <div class="footer"><span>&copy; Cryptocore <?=date('Y');?></span></div>
    </div>
    <script>
        (()=>{
            let rev = '<?=$rev;?>';
            document.querySelector("#fechafac").textContent = '<?=$fechareg;?>';
            document.querySelector("#vendedor").textContent = '<?=$vendedor;?>';
            document.querySelector("#sucursal").textContent = '<?=$sucursal;?>';
            document.querySelector("#clinom").textContent = '<?=$cliente . " / " . $clidoc;?>';
            window.print();
            if(rev == 0) {
                setTimeout(()=>{
                    window.close();
                },3000);
            }
        })();
    </script>
</body>
</html>
<?php
    }
    else {
        header("Location: ../");
    }
?>