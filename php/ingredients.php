<?php

    include('../includes/verificator.php');

    if(isset($_GET['new_ingredient']) && $_GET['new_ingredient'] === $clav){
        try {
            $ingrediente = htmlspecialchars($_POST['ingrediente']);
            $stock = isset($_POST['stock']) ? $_POST['stock'] : 0;
            $minimo = isset($_POST['stock_minimo']) ? $_POST['stock_minimo'] : 0;
            $costo = sanear_string($_POST['costo']);
            $costound = isset($_POST['stock']) ? sanear_string($_POST['costo'])/$stock : sanear_string($_POST['costo'])/1;
            $costo_total = sanear_string($_POST['costo']);
            $unidad = $_POST['unidad'];
            $vencimiento = !empty($_POST['vencimiento']) ? $_POST['vencimiento'] : null;
            $ins = $con -> prepare("INSERT INTO ingredientes (nombre, costo, costo_und, unidad, stock, stock_minimo, vencimiento, sucursal, usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ins -> bind_param('siisdisss', $ingrediente, $costo, $costound, $unidad, $stock, $minimo, $vencimiento,$sucursal,$sesion);
            $ins -> execute();
            $idinsert = $con -> insert_id;
            $inv = $con->prepare("INSERT INTO inversion (idinsumo, concepto, cantidad, unidad, costo, registrado_por,sucursal) VALUES (?, ?, ?, ?, ?, ? ,?)");
            $inv -> bind_param('isisiss',$idinsert,$ingrediente,$stock,$unidad,$costo_total,$sesion,$sucursal);
            $inv -> execute();
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Nuevo ingrediente registrado correctamente!"
            ]);
            writeLog("Nuevo insumo registrado (".$ingrediente.", cantidad = ".$stock.units($unidad).")",$sesion);
            $ins -> close();
            $inv -> close();
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al registrar insumo: ".$e -> getMessage(),$sesion);
        }
        $con -> close();
    }

    elseif(isset($_GET['get_values'])){
        $stk = $con->prepare("SELECT costo_und, stock FROM ingredientes WHERE sucursal = ?");
        $stk -> bind_param('s',$sucursal);
        $stk -> execute();
        $Rstk = $stk -> get_result();
        if($Rstk -> num_rows > 0){
            $totalcost = 0;
            while($st = mysqli_fetch_array($Rstk)){
                $totalcost += $st['costo_und']*$st['stock'];
            }
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => intval($totalcost)
            ]);
        }
        else {
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Sin resultados"
            ]);
        }
    }

    elseif(isset($_GET['get_ingredients']) && $_GET['get_ingredients'] === $clav){
        try {
            if($_SESSION['sucursal'] == 'system'){
                $consult = $con->prepare("SELECT * FROM ingredientes ORDER BY creado DESC");
            }
            else {
                $consult = $con->prepare("SELECT * FROM ingredientes WHERE sucursal = ? ORDER BY creado DESC");
                $consult -> bind_param('s',$sucursal);
            }
            $consult -> execute();
            $Rconsult = $consult -> get_result();
            if($Rconsult -> num_rows > 0){
                $ingredientes = "";
                while($ingreds = mysqli_fetch_array($Rconsult)){
                    $id = $ingreds['id'];
                    $nombre = $ingreds['nombre'];
                    $costo = $ingreds['costo'];
                    $unidad = $ingreds['unidad'];
                    $stock = $ingreds['stock'];
                    $min = $ingreds['stock_minimo'];
                    $vencimiento = $ingreds['vencimiento'];
                    $precioxunidad = $ingreds['costo_und'];
                    $preciostock = miles(round($stock*$precioxunidad));
                    $ingredientes .= '
                        <tr onclick="openIngredientOptions(\''.$nombre.'\',\''.$id.'\')" class="elem-busqueda">
                            <td>'.$id.'</td>
                            <td>'.$nombre.'</td>
                            <td>$'.miles(round($costo)).'</td>
                            <td>'.$unidad.'</td>
                            <td>'.miles(round($stock)).'<span>'.units($unidad).'</span></td>
                            <td>'.miles(round($min)).'<span>'.units($unidad).'</span></td>
                            <td>$'.miles(round($precioxunidad)).'</td>
                            <td>$'.$preciostock.'</td>
                            <td>'.$vencimiento.'</td>
                        </tr>
                    ';
                }
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => $ingredientes
                ]);
            }
            else {
                echo json_encode([
                    "status" => "success",
                    "title" => "Vacío",
                    "message" => "<h2 class='center_text'>Sin ingredientes registrados.</h2>"
                ]);
            }
            $consult -> close();
        }
        catch (mysqli_sql_exception $e) {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
        }
        $con->close();
    }

    elseif(isset($_GET['get_this_ingredient']) && $_GET['get_this_ingredient'] === $clav){
        $id = $_POST['id'];
        if($_SESSION['sucursal'] == 'system'){
            $ingre = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
            $ingre -> bind_param('i',$id);
        }
        else {
            $ingre = $con->prepare("SELECT * FROM ingredientes WHERE id = ? AND sucursal = ?");
            $ingre -> bind_param('is',$id,$sucursal);
        }
        $ingre -> execute();
        $Ringre = $ingre -> get_result();
        if($Ringre -> num_rows > 0){
            $ing = $Ringre -> fetch_assoc();
            $gr = $ing['unidad'] == "gramo" ? "selected" : "";
            $ml = $ing['unidad'] == "ml" ? "selected" : "";
            $un = $ing['unidad'] == "unidad" ? "selected" : "";
            $slct = '
                <option value="gramo" '.$gr.'>Gramos</option>
                <option value="ml" '.$ml.'>Mililitros</option>
                <option value="unidad" '.$un.'>Unidades</option>
            ';
            $insumo = [
                "nombre" => $ing['nombre'],
                "costo" => $ing['costo'],
                "costo_total" => round($ing['costo_und']*$ing['stock']),
                "unidad" => $ing['unidad'],
                "unidad_select" => $slct,
                "stock" => $ing['stock'],
                "minimo" => $ing['stock_minimo'],
                "vencimiento" => $ing['vencimiento']
            ];
            echo json_encode($insumo);
        }
        else {
            echo json_encode(["Sin registros"]);
        }
        $ingre -> close();
        $con -> close();
    }

    elseif(isset($_GET['cantid']) && $_GET['cantid'] === $clav){
        $id = $_POST['id'];
        $cantid = $_POST['cantid'];
        try {
            if($_SESSION['sucursal'] == 'system'){
                $cns = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
                $cns -> bind_param('i',$id);
            }
            else {
                $cns = $con->prepare("SELECT * FROM ingredientes WHERE id = ? AND sucursal = ?");
                $cns -> bind_param('is',$id,$sucursal);
            }
            $cns -> execute();
            $Rcns = $cns -> get_result();
            if($Rcns -> num_rows > 0){
                $insum = $Rcns -> fetch_assoc();
                $ingrediente = $insum['nombre'];
                $unidad = $insum['unidad'];
                $costo = $insum['costo_und'];
                $ssuc = $insum['sucursal'];
                $ussu = $insum['usuario'];
                $costo_total = $costo*$cantid;
                $upcant = $con -> prepare("UPDATE ingredientes SET costo = COALESCE(costo,0) + ?, stock = COALESCE(stock, 0) + ? WHERE id = ?");
                $upcant -> bind_param('idi', $costo_total,$cantid, $id);
                $upcant -> execute();
                if($upcant -> affected_rows > 0){
                    $inv = $con->prepare("INSERT INTO inversion (idinsumo, concepto, cantidad, unidad, costo, registrado_por,sucursal) VALUES (?, ?, ?, ?, ?, ? ,?)");
                    $inv -> bind_param('isisiss',$id,$ingrediente,$cantid,$unidad,$costo_total,$ussu,$ssuc);
                    $inv -> execute();
                    echo json_encode([
                        "status" => "success",
                        "title" => "Correcto!",
                        "message" => "Se ha ajustado el stock de este insumo"
                    ]);
                    writeLog($cantid.units($unidad)." de ".$ingrediente." agragados",$sesion);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "Atención!",
                        "message" => "No se ha podido ajustar el stock de este insumo"
                    ]);
                    writeLog("Error al agregar unidades al insumo id ".$id, $sesion);
                }   
            }
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al agregar insumo: ".$e->getMessage(), $sesion);
        }
    }

    elseif(isset($_GET['quitar_cantid']) && $_GET['quitar_cantid'] === $clav){
        $id = $_POST['id'];
        $cantid = $_POST['cantid'];
        try {
            if($_SESSION['sucursal'] == 'system'){
                $cns = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
                $cns -> bind_param('i',$id);
            }
            else {
                $cns = $con->prepare("SELECT * FROM ingredientes WHERE id = ? AND sucursal = ?");
                $cns -> bind_param('is',$id,$sucursal);
            }
            $cns -> execute();
            $Rcns = $cns -> get_result();
            if($Rcns -> num_rows > 0){
                $insum = $Rcns -> fetch_assoc();
                $ingrediente = $insum['nombre'];
                $unidad = $insum['unidad'];
                $costo = $insum['costo_und'];
                $ssuc = $insum['sucursal'];
                $ussu = $insum['usuario'];
                $costo_total = $costo*$cantid;
                $upcant = $con -> prepare("UPDATE ingredientes SET costo = COALESCE(costo,0) - ?, stock = COALESCE(stock, 0) - ? WHERE id = ?");
                $upcant -> bind_param('idi', $costo_total,$cantid, $id);
                $upcant -> execute();
                if($upcant -> affected_rows > 0){
                    echo json_encode([
                        "status" => "success",
                        "title" => "Correcto!",
                        "message" => "Se ha ajustado el stock de este insumo"
                    ]);
                    writeLog($cantid.units($unidad)." de ".$ingrediente." restados",$sesion);
                }
                else {
                    echo json_encode([
                        "status" => "error",
                        "title" => "Atención!",
                        "message" => "No se ha podido ajustar el stock de este insumo"
                    ]);
                    writeLog("Error al restar unidades al insumo id ".$id, $sesion);
                }   
            }
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al agregar insumo: ".$e->getMessage(), $sesion);
        }
    }

    elseif(isset($_GET['modify']) && $_GET['modify'] === $clav){
        try {
            $id = $_POST['id'];
            $ingrediente = htmlspecialchars($_POST['ingrediente']);
            $stock = $_POST['stock'] <= 0 ? 0 : $_POST['stock'];
            $minimo = $_POST['stock_minimo'];
            if($stock == 0){
                $costo = 0;
                $costo_total = 0;
            }
            else {
                $costo = sanear_string($_POST['costo'])/$stock;
                $costo_total = sanear_string($_POST['costo']);
            }
            $unidad = $_POST['unidad'];
            $vencimiento = !empty($_POST['vencimiento']) ? $_POST['vencimiento'] : null;
            $ins = $con -> prepare("UPDATE ingredientes SET nombre = ?, costo = ?, costo_und = ?, unidad = ?, stock = ?, stock_minimo = ?, vencimiento = ? WHERE id = ?");
            $ins -> bind_param('sidsdisi', $ingrediente, $costo_total, $costo, $unidad, $stock, $minimo, $vencimiento,$id);
            $ins -> execute();
            $inv = $con->prepare("UPDATE inversion SET idinsumo = ?, concepto = ?, cantidad = ?, unidad = ?, costo = ? WHERE id = ?");
            $inv -> bind_param('isisii',$id,$ingrediente,$stock,$unidad,$costo_total,$id);
            $inv -> execute();
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => $ingrediente." actualizado correctamente!"
            ]);
            writeLog("Insumo modificado (".$ingrediente.", cantidad = ".$stock.units($unidad).")",$sesion);
            $ins -> close();
            $inv -> close();
        }
        catch (mysqli_sql_exception $e){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al modificar insumo: ".$e -> getMessage(),$sesion);
        }
        $con -> close();
    }

    elseif(isset($_GET['delete']) && $_GET['delete'] === $clav){
        $id = $_POST['id'];
        try {
            $dl = $con -> prepare("DELETE FROM ingredientes WHERE id = ?");
            $dl -> bind_param('i',$id);
            $dl -> execute();
            if($dl){
                $dlinv = $con -> prepare("DELETE FROM inversion WHERE idinsumo = ?");
                $dlinv -> bind_param('i',$id);
                $dlinv -> execute();
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Se ha eliminado el insumo"
                ]);
                writeLog("Insumo con id ".$id." eliminado.", $sesion);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "No se ha podido eliminar el insumo"
                ]);
                writeLog("Error al eliminar el insumo con id ".$id, $sesion);
            }
        }
        catch (mysqli_sql_exception $e) {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Ha ocurrido un error: " . $e->getMessage()
            ]);
            writeLog("Error al eliminar insumo id ".$id.": ".$e -> getMessage(),$sesion);
        }
    }

?>