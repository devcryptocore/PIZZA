<?php

    include('../includes/verificator.php');

    if(isset($_GET['new_debt']) && $_GET['new_debt'] === $clav) {
        $uniq = uniqid();
        $concepto = htmlspecialchars($_POST['concepto']);
        $valor = sanear_string($_POST['valor']);
        $abonado = 0;
        $historico = "";
        if(isset($_FILES['foto_factura'])) {
            $dir = "../res/images/invoices/" . $uniq;
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $factura = guardarFoto("foto_factura", $uniq, $dir);
        }
        else {
            $factura = null;
        }
        $con -> begin_transaction();
        try {
            $ins = $con -> prepare("INSERT INTO obligaciones (concepto,valor,abonado,saldo,historico,foto_factura,sucursal) VALUES (?,?,?,?,?,?,?)");
            $ins -> bind_param('siiisss',$concepto,$valor,$abonado,$valor,$historico,$factura,$sucursal);
            $ins -> execute();
            $con -> commit();
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha almacenado la nueva obligación"
            ]);
        }
        catch (Exception $e) {
            $con -> rollback();
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido almacenar la nueva obligación: " . $e -> getMessage()
            ]);
        }
    }

    if(isset($_GET['get_debts']) && $_GET['get_debts'] === $clav) {
        $data = "Sin registros para mostrar";
        $cons = $con -> prepare("SELECT * FROM obligaciones ORDER BY id DESC");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $data = "";
            while($obl = mysqli_fetch_array($Rcons)){
                $pag = $obl['saldo'] <= 0 ? 'style="background: url(../res/images/cancelado.webp) center / 20% no-repeat;";' : '';
                $data .= '
                    <tr '.$pag.' onclick="debt_details(\''.$obl['id'].'\')">
                        <td>'.$obl['id'].'</td>
                        <td>'.$obl['concepto'].'</td>
                        <td>$'.miles($obl['valor']).'</td>
                        <td>$'.miles($obl['abonado']).'</td>
                        <td>$'.miles($obl['saldo']).'</td>
                        <td>'.$obl['sucursal'].'</td>
                        <td>'.$obl['fecha'].'</td>
                    </tr>
                ';
            }
        }
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => $data
        ]);
    }

    if(isset($_GET['get_debt']) && $_GET['get_debt'] === $clav) {
        $saldo = 0;
        $abonado = 0;
        $id = $_POST['id'];
        $cons = $con -> prepare("SELECT * FROM obligaciones WHERE id = ? AND sucursal = ?");
        $cons -> bind_param('is',$id,$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $obl = $Rcons -> fetch_assoc();
            $hist = $obl['historico'];
            $saldo = $obl['saldo'];
            $abonado = $obl['abonado'];
            if(strlen($hist) > 0 || $hist != "" || $hist != null) {
                $data = "";
                $hst = explode("_SEP_",$hist);
                foreach ($hst as $h) {
                    $data .= $h;
                }
                echo json_encode([
                    "status" => "success",
                    "title" => "ok",
                    "message" => [
                        "data" => $data,
                        "saldo" => miles($saldo),
                        "abonado" => miles($abonado)
                    ]
                ]);
            }
            else {
                echo json_encode([
                    "status" => "success",
                    "title" => "ok",
                    "message" => [
                        "data" => "<tr><td>Sin registros para mostrar</td></tr>",
                        "saldo" => miles($saldo),
                        "abonado" => miles($abonado)
                    ]
                ]);
            }
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                        "data" => "<tr><td>Sin registros para mostrar</td></tr>"
                    ]
            ]);
        }
    }

    if(isset($_GET['set_abono']) && $_GET['set_abono'] === $clav) {
        $pagado = false;
        $id = $_POST['id'];
        $entidad = $_POST['entidad'];
        $valor = sanear_string($_POST['valor']);
        $fechahoy = date('d-m-Y H:i:s');
        $entidades_validas = ['efectivo', 'nequi', 'daviplata', 'bancolombia', 'davivienda', 'consignacion', 'otros'];
        if (!in_array($entidad, $entidades_validas)) {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Entidad de pago inválida."
            ]);
            exit;
        }
        $cons = $con -> prepare("SELECT * FROM obligaciones WHERE id = ?");
        $cons -> bind_param('i',$id);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $obl = $Rcons -> fetch_assoc();
            $saldo = $obl['saldo'];
            $his = $obl['historico'] ?? '';
            $his = array_filter(explode("_SEP_",$his));
            $nsaldo = $saldo - $valor;
            $suces = count($his) + 1;
            $historic = '
                <tr><td>' . $suces . '</td>
                <td>$' . miles($valor) . '</td>
                <td> $' . miles($nsaldo ). ' </td>
                <td> ' . $fechahoy . ' </td></tr>_SEP_
            ';
            $ent = $con -> prepare("SELECT " . $entidad . " FROM entidades");
            $ent -> execute();
            $Rent = $ent -> get_result();
            $fondos = $Rent -> fetch_assoc()[$entidad];
            if($valor > $fondos) {
                echo json_encode([
                    "title" => "Error!",
                    "status" => "error",
                    "message" => "No hay fondos suficientes para realizar el abono!"
                ]);
                exit;
            }
            $con -> begin_transaction();
            try {
                if($saldo < $valor){
                    echo json_encode([
                        "title" => "Error!",
                        "status" => "error",
                        "message" => "El valor del abono es mayor al saldo actual!"
                    ]);
                    exit;
                }
                if($nsaldo <= 0) {
                    $pagado = true;
                }
                $tipo = "abono";
                $conc = "Nuevo abono a " . $obl['concepto'];
                $upd = $con -> prepare("UPDATE obligaciones SET abonado = COALESCE(abonado, 0) + ?
                ,saldo = COALESCE(saldo, 0) - ?, historico = CONCAT(historico, ?) WHERE id = ?");
                $upd -> bind_param('iisi',$valor,$valor,$historic,$id);
                $upent = $con -> prepare("UPDATE entidades SET " . $entidad . " = COALESCE(" . $entidad . ",0) - ?");
                $upent -> bind_param('i',$valor);
                $mov = $con -> prepare("INSERT INTO movimientos (tipo,concepto,entidad,valor,sucursal) VALUES (?,?,?,?,?)");
                $mov -> bind_param('sssis',$tipo,$conc,$entidad,$valor,$sucursal);
                $upd -> execute();
                $upent -> execute();
                $mov -> execute();
                $con -> commit();
                echo json_encode([
                    "title" => "Correcto!",
                    "status" => "success",
                    "message" => "Abono realizado con exito!"
                ]);
            }
            catch (Exception $e) {
                echo json_encode([
                    "title" => "Error!",
                    "status" => "error",
                    "message" => "Error al realizar el abono: " . $e -> getMessage()
                ]);
            }
        }
    }

    if (isset($_GET['del_debt']) && $_GET['del_debt'] === $clav) {
        $id = $_POST['id'];
        $del = $con->prepare("DELETE FROM obligaciones WHERE id = ?");
        $del->bind_param('i', $id);
        if ($del->execute()) {
            echo json_encode(['status' => "success", "title" => "Correcto!", 'message' => 'Obligación eliminada correctamente.']);
        } else {
            echo json_encode(['status' => "success", "title" => "Error!", 'message' => 'Error al eliminar: ' . $del->error]);
        }
        exit;
    }

?>