<?php

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
        $bcon = $con -> prepare("SELECT codcaja FROM caja WHERE usuario = ? AND sucursal = ? ORDER BY codcaja DESC LIMIT 1");
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

    if(isset($_GET['set_entidad']) && $_GET['set_entidad'] === $clav){
        $entidad = $_POST['entidad'];
        $inicial = sanear_string($_POST['inicial']);
        $cons = $con -> prepare("SELECT * FROM entidades");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $ent = $Rcons -> fetch_assoc();
            $enti = $ent[$entidad];
            if($enti > 0) {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "La entidad ya ha sido establecida"
                ]);
                exit;
            }
        }
        $ins = $con -> prepare("UPDATE entidades SET {$entidad} = ?");
        $ins -> bind_param('i',$inicial);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha inicializado la entidad ".$entidad." y se han almacenado los fondos"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido inicializar la entidad"
            ]);
            exit;
        }
    }

    if(isset($_GET['transfer']) && $_GET['transfer'] === $clav) {
        $desde = $_POST['desde_entidad'];
        $hacia = $_POST['hacia_entidad'];
        $monto = sanear_string($_POST['monto']);
        $tipo = "transferencia";
        $concept = "Transferencia de $" . miles($monto) . " desde " . $desde . " hacia " . $hacia . " por " . $sesion;
        $con -> begin_transaction();
        try {
            $ver = $con -> prepare("SELECT {$desde} FROM entidades");
            $ver -> execute();
            $Rver = $ver -> get_result();
            $fondos = $Rver -> fetch_assoc()[$desde];
            if($fondos < $monto){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error",
                    "message" => "El valor del monto no puede ser mayor al de " . $desde
                ]);
                exit;
            }
            $upd = $con -> prepare("UPDATE entidades SET {$desde} = COALESCE({$desde}, 0) - ?
            , {$hacia} = COALESCE({$hacia}, 0) + ?");
            $upd -> bind_param('ii',$monto,$monto);
            $upd -> execute();
            $ins = $con -> prepare("INSERT INTO movimientos (tipo,concepto,entidad,valor,sucursal)
            VALUES (?,?,?,?,?)");
            $ins -> bind_param('sssis',$tipo,$concept,$desde,$monto,$sucursal);
            $ins -> execute();
            $con -> commit();
            echo json_encode([
                "status" => "success",
                "title" => "Transferencia exitosa!",
                "message" => "Se ha realizado la transferencia de $" . miles($monto) . " desde " . $desde . " hacia " . $hacia
            ]);
        }
        catch (Exception $e) {
            $con -> rollback();
            echo json_encode([
                "status" => "error",
                "title" => "Error de transferencia!",
                "message" => "No se ha podido realizar la transferencia: " . $e -> getMessage()
            ]);
        }
    }

    if(isset($_GET['movement']) && $_GET['movement'] === $clav) {
        $tipo = $_POST['tipo'];
        $entidad = $_POST['entidad'];
        $monto = sanear_string($_POST['monto']);
        $concepto = htmlspecialchars($_POST['concepto']);
        $boxcode = boxcode();
        $con -> begin_transaction();
        try  {
            if(!statebox()) {
                echo json_encode([
                    "status" => "warning",
                    "title" => "Caja cerrada!",
                    "message" => "Se debe realizar apertura de caja primero!"
                ]);
                exit;
            }
            if($tipo == "egreso") {
                $ver = $con -> prepare("SELECT {$entidad} FROM entidades");
                $ver -> execute();
                $Rver = $ver -> get_result();
                $fondos = $Rver -> fetch_assoc()[$entidad];
                if($fondos < $monto){
                    echo json_encode([
                        "status" => "error",
                        "title" => "Error",
                        "message" => "El valor del monto no puede ser mayor al de " . $entidad
                    ]);
                    exit;
                }
                $upd = $con -> prepare("UPDATE entidades SET {$entidad} = COALESCE({$entidad},0) - ?");
                $upbox = $con -> prepare("UPDATE caja SET egresos = COALESCE(egresos,  0) + ? WHERE codcaja = ?");
                $concept = "Gasto de $" . miles($monto) . " por " . $concepto . " registrado por " . $sesion;
            }
            else {
                $upd = $con -> prepare("UPDATE entidades SET {$entidad} = COALESCE({$entidad},0) + ?");
                $upbox = $con -> prepare("UPDATE caja SET egresos = COALESCE(ingresos,  0) + ? WHERE codcaja = ?");
                $concept = "Ingreso de $" . miles($monto) . " por " . $concepto . " registrado por " . $sesion;
            }
            $upd -> bind_param('i',$monto);
            $upd -> execute();
            $upbox -> bind_param('ii',$monto,$boxcode);
            $upbox -> execute();
            $mov = $con -> prepare("INSERT INTO movimientos (cocaja,tipo,concepto,entidad,valor,sucursal)
            VALUES(?,?,?,?,?,?)");
            $mov -> bind_param('isssis',$boxcode,$tipo,$concept,$entidad,$monto,$sucursal);
            $mov -> execute();
            $con -> commit();
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha registrado el movimiento financiero correctamente"
            ]);
        }
        catch (Exception $e) {
            $con -> rollback();
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido realizar este movimiento: " . $e -> getMessage()
            ]);
        }
    }

?>