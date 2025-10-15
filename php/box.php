<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    include('../includes/verificator.php');

    function statebox(){
        global $con,$sesion,$sucursal;
        $est = 1;
        $box = $con -> prepare("SELECT estado FROM caja WHERE estado = ? AND usuario = ? AND sucursal = ?");
        $box -> bind_param('iss',$est,$sesion,$sucursal);
        $box -> execute();
        $Rbox = $box -> get_result();
        if($Rbox -> num_rows > 0) {
            return true;
        }
        return false;
    }

    function boxcode() {
        global $con,$sesion,$sucursal;
        $bcon = $con -> prepare("SELECT codcaja FROM caja WHERE id = (SELECT MAX(id) FROM caja) AND usuario = ? AND sucursal = ?");
        $bcon -> bind_param('ss',$sesion,$sucursal);
        $bcon -> execute();
        $Rbcon = $bcon -> get_result();
        if($Rbcon -> num_rows > 0) {
            $nc = $Rbcon -> fetch_assoc()['codcaja'];
            return $nc;
        }
        else {
            return 1;
        }
    }

    if(isset($_GET['constate'])) {
        $estado = statebox() ? "open" : "close";
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $estado
        ]);
    }

    if(isset($_GET['get_fondos']) && $_GET['get_fondos'] === $clav) {
        $consfondos = $con -> prepare("SELECT * FROM entidades");
        $consfondos -> execute();
        $Rcons = $consfondos -> get_result();
        $ent = $Rcons -> fetch_assoc();
        $efectivo = $ent['efectivo'] ?? 0;
        $nequi = $ent['nequi'] ?? 0;
        $daviplata = $ent['daviplata'] ?? 0;
        $bancolombia = $ent['bancolombia'] ?? 0;
        $davivienda = $ent['davivienda'] ?? 0;
        $consignacion = $ent['consignacion'] ?? 0;
        $otros = $ent['otros'] ?? 0;
        $are = '
            <div class="entcon xlsbutton" onclick="xls(\'#boxtable\')"></div>
            <div class="entcon">
                <b>Efectivo</b><span>$'.miles($efectivo).'</span>
            </div>
            <div class="entcon">
                <b>Nequi</b><span>$'.miles($nequi).'</span>
            </div>
            <div class="entcon">
                <b>Daviplata</b><span>$'.miles($daviplata).'</span>
            </div>
            <div class="entcon">
                <b>Bancolombia</b><span>$'.miles($bancolombia).'</span>
            </div>
            <div class="entcon">
                <b>Davivienda</b><span>$'.miles($davivienda).'</span>
            </div>
            <div class="entcon">
                <b>Consignación</b><span>$'.miles($consignacion).'</span>
            </div>
            <div class="entcon">
                <b>Otros</b><span>$'.miles($otros).'</span>
            </div>
        ';
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $are
        ]);
    }

    if(isset($_GET['boxhistory']) && $_GET['boxhistory'] === $clav){
        $mes = date('m');
        $cons = $con -> prepare("SELECT * FROM caja WHERE usuario = ? AND sucursal = ? AND MONTH(fecha) = ? ORDER BY id DESC");
        $cons -> bind_param('ssi',$sesion,$sucursal,$mes);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $bcaja = boxcode();
        $trs = "";
        if($Rcons -> num_rows > 0) {
            while($box = mysqli_fetch_array($Rcons)){
                $estado = $box['estado'] == 1 ? '<span class="state open">Abierto</span>' : '<span class="state closed">Cerrado</span>';
                $trs .= '
                    <tr onclick="get_details(\''.$box['codcaja'].'\')">
                        <td>'.$box['codcaja'].'</td>
                        <td>'.$estado.'</td>
                        <td>$'.miles($box['base']).'</td>
                        <td>$'.miles($box['ventas']).'</td>
                        <td>$'.miles($box['descuentos']).'</td>
                        <td>$'.miles($box['ingresos']).'</td>
                        <td>$'.miles($box['egresos']).'</td>
                        <td>'.$box['sucursal'].'</td>
                        <td>'.$box['usuario'].'</td>
                        <td>'.$box['fecha'].'</td>
                    </tr>
                ';
            }
        }
        else {
            $trs .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Estados de caja vacíos</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            ';
        }
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $trs
        ]);
    }

    if(isset($_GET['set_box_state']) && $_GET['set_box_state'] === $clav) {
        if(isset($_POST['accion']) && $_POST['accion'] === 'cerrar') {
            $stado = 0;
            $codebox = boxcode();
            $concaja = $con -> prepare("SELECT * FROM caja WHERE usuario = ? AND sucursal = ? AND codcaja = ?");
            $concaja -> bind_param('ssi',$sesion,$sucursal,$codebox);
            $concaja -> execute();
            $Rconcaja = $concaja -> get_result();
            if($Rconcaja -> num_rows > 0) {
                $dt = $Rconcaja -> fetch_assoc();
                $base = $dt['base'];
                $ventas = $dt['ventas'];
                $ingr = $dt['ingresos'];
                $egre = $dt['egresos'];
                $total = ($ingr - $ventas) + ($base + $ventas) - $egre;
                $upb = $con -> prepare("UPDATE caja SET estado = ? WHERE usuario = ? AND sucursal = ? AND codcaja = ?");
                $upb -> bind_param('sssi',$stado,$sesion,$sucursal,$codebox);
                if($upb -> execute()) {
                    echo json_encode([
                        "status" => "success",
                        "title" => "Caja cerrada!",
                        "message" => "Se ha realizado el cierre de caja, total caja hoy: $" . miles($total)
                    ]);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "Error de cierre!",
                        "message" => "No se ha podido realizar el cierre de caja"
                    ]);
                }
                exit;
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error de cierre!",
                    "message" => "Estado de caja incorrecto"
                ]);
                exit;
            }
        }
        $base = sanear_string($_POST['saldobase']);
        $entidad = $_POST['entidad'];
        $estado = 1;
        $con -> begin_transaction();
        try {
            $cent = $con -> prepare("SELECT {$entidad} FROM entidades");
            $cent -> execute();
            $Rcent = $cent -> get_result();
            if($Rcent -> num_rows == 0){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error de apertura!",
                    "message" => "Debe inicializar la entidad " . $entidad . " para dar apertura de caja."
                ]);
                exit;
            }
            $vcent = $Rcent -> fetch_assoc()[$entidad];
            if($vcent < $base){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error de apertura!",
                    "message" => "El valor base es mayor a los fondos disponibles en " . $entidad
                ]);
                exit;
            }
            $box = $con -> prepare("INSERT INTO caja (estado,base,usuario,sucursal) VALUES (?,?,?,?)");
            $box -> bind_param('iiss',$estado,$base,$sesion,$sucursal);
            $box -> execute();
            $codcaja = $box -> insert_id;
            $upd = $con -> prepare("UPDATE caja SET codcaja = ? WHERE id = ?");
            $upd -> bind_param('ii',$codcaja,$codcaja);
            $upd -> execute();
            $con -> commit();
            echo json_encode([
                "status" => "success",
                "title" => "Apertura exitosa!",
                "message" => "Se ha realizado la apertura de caja para hoy con $" . miles($base)
            ]);
        }
        catch (Exception $e) {
            $con -> rollback();
            echo json_encode([
                "status" => "error",
                "title" => "Error de apertura!",
                "message" => "No se ha podido realizar la apertura de la caja: " . $e -> getMessage()
            ]);
        }
        
    }

    if(isset($_GET['findbox']) && $_GET['findbox'] === $clav) {
        $fech = $_POST['fecha'];
        $consl = $con -> prepare("SELECT * FROM caja WHERE DATE(fecha) = ?");
        $consl -> bind_param('s',$fech);
        $consl -> execute();
        $Rconsl = $consl -> get_result();
        if($Rconsl -> num_rows > 0) {
            $box = $Rconsl -> fetch_assoc();
            $resp = '
                <tr onclick="get_details(\''.$box['codcaja'].'\')">
                    <td>'.$box['ventas'].'</td>
                    <td>'.$box['sucursal'].'</td>
                    <td>'.$box['usuario'].'</td>
                    <td>'.$box['fecha'].'</td>
                </tr>
            ';
        }
        else {
            $resp = '
                <tr>
                    <td>No se</td>
                    <td> realizaron</td>
                    <td> aperturas de caja</td>
                    <td> en esta fecha</td>
                </tr>
            ';
        }
        echo json_encode([
            "status" => "success",
            "title" => "Resultado de caja",
            "message" => $resp
        ]);
    }

    if(isset($_GET['boxdetails']) && $_GET['boxdetails'] === $clav) {
        $resp = "";
        $efectivo = 0;
        $nequi = 0;
        $daviplata = 0;
        $bancolombia = 0;
        $davivienda = 0;
        $consignacion = 0;
        $otros = 0;
        $ventas = 0;
        $ingresos = 0;
        $egresos = 0;
        $are = "";
        $fech = $_POST['codcaja'] ?? '';
        $consl = $con -> prepare("SELECT * FROM movimientos WHERE codcaja = ?");
        $consl -> bind_param('s',$fech);
        $consl -> execute();
        $Rconsl = $consl -> get_result();
        if($Rconsl -> num_rows > 0) {
            while($box = $Rconsl -> fetch_assoc()){
                if($box['tipo'] == 'venta'){
                    $ventas += $box['valor'];
                }
                if($box['tipo'] == 'ingreso'){
                    $ingresos += $box['valor'];
                }
                if($box['tipo'] == 'egreso'){
                    $egresos += $box['valor'];
                }
                if($box['entidad'] == 'efectivo' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $efectivo += $box['valor'];
                }
                elseif($box['entidad'] == 'nequi' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $nequi += $box['valor'];
                }
                elseif($box['entidad'] == 'daviplata' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $daviplata += $box['valor'];
                }
                elseif($box['entidad'] == 'bancolombia' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $bancolombia += $box['valor'];
                }
                elseif($box['entidad'] == 'davivienda' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $davivienda += $box['valor'];
                }
                elseif($box['entidad'] == 'consignacion' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $consignacion += $box['valor'];
                }
                elseif($box['entidad'] == 'otros' && $box['tipo'] != 'egreso' || $box['tipo'] != 'transferencia'){
                    $otros += $box['valor'];
                }
                $resp .= '
                    <tr>
                        <td style="font-size:10px;">'.$box['tipo'].'</td>
                        <td style="font-size:10px;">'.$box['concepto'].'</td>
                        <td style="font-size:10px;">'.$box['entidad'].'</td>
                        <td style="font-size:10px;">$'.miles($box['valor']).'</td>
                        <td style="font-size:10px;">'.$box['sucursal'].'</td>
                        <td style="font-size:10px;">'.$box['fecha'].'</td>
                    </tr>
                ';
            }
            $are = '
                <div class="entcon xlsbutton" onclick="xls(\'#detable\')"></div>
                <div class="entcon">
                    <b>Efectivo</b><span>$'.miles($efectivo).'</span>
                </div>
                <div class="entcon">
                    <b>Nequi</b><span>$'.miles($nequi).'</span>
                </div>
                <div class="entcon">
                    <b>Daviplata</b><span>$'.miles($daviplata).'</span>
                </div>
                <div class="entcon">
                    <b>Bancolombia</b><span>$'.miles($bancolombia).'</span>
                </div>
                <div class="entcon">
                    <b>Davivienda</b><span>$'.miles($davivienda).'</span>
                </div>
                <div class="entcon">
                    <b>Consignación</b><span>$'.miles($consignacion).'</span>
                </div>
                <div class="entcon">
                    <b>Otros</b><span>$'.miles($otros).'</span>
                </div>
            ';
        }
        else {
            $resp = '
                <tr>
                    <td>Sin</td>
                    <td>detalles</td>
                    <td></td>
                    <td></td>
                </tr>
            ';
        }
        echo json_encode([
            "status" => "success",
            "title" => "Detalles de caja",
            "message" => [
                "datatable" => $resp,
                "entities" => $are,
                "ventas" => miles($ventas),
                "ingresos" => miles($ingresos),
                "egresos" => miles($egresos)
            ]
        ]);
    }

?>