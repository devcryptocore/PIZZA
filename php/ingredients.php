<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('optimizador.php');
    $sesion = "admin";//DUMMIE PARA SESION DE USUARIO

    function units($unit){
        switch ($unit) {
            case 'ml':
                return 'ml';
                break;
            case 'unidad':
                return 'und';
                break;
            default:
                return 'gr';
                break;
        }
    }

    if(isset($_GET['new_ingredient']) && $_GET['new_ingredient'] === $clav){
        try {
            $ingrediente = htmlspecialchars($_POST['ingrediente']);
            $stock = $_POST['stock'];
            $minimo = $_POST['stock_minimo'];
            $costo = $_POST['costo']/$stock;
            $costo_total = $_POST['costo'];
            $unidad = $_POST['unidad'];
            $vencimiento = !empty($_POST['vencimiento']) ? $_POST['vencimiento'] : null;
            $ins = $con -> prepare("INSERT INTO ingredientes (nombre, costo, unidad, stock, stock_minimo, vencimiento) VALUES (?, ?, ?, ?, ?, ?)");
            $ins -> bind_param('sdsdds', $ingrediente, $costo, $unidad, $stock, $minimo, $vencimiento);
            $ins -> execute();
            $idinsert = $con -> insert_id;
            $inv = $con->prepare("INSERT INTO inversion (idinsumo, concepto, cantidad, unidad, costo, registrado_por) VALUES (?, ?, ?, ?, ? ,?)");
            $inv -> bind_param('isdsds',$idinsert,$ingrediente,$stock,$unidad,$costo_total,$sesion);
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

    elseif(isset($_GET['get_ingredients']) && $_GET['get_ingredients'] === $clav){
        try {
            $consult = $con->prepare("SELECT * FROM ingredientes ORDER BY creado DESC");
            $consult -> execute();
            $Rconsult = $consult -> get_result();
            if($Rconsult -> num_rows > 0){
                $ingredientes = "";
                while($ingreds = mysqli_fetch_array($Rconsult)){
                    $id = $ingreds['id'];
                    $nombre = $ingreds['nombre'];
                    $costo = $ingreds['costo']*$ingreds['stock'];
                    $unidad = $ingreds['unidad'];
                    $stock = $ingreds['stock'];
                    $min = $ingreds['stock_minimo'];
                    $vencimiento = $ingreds['vencimiento'];
                    $precioxunidad = $ingreds['costo'];
                    $preciostock = miles($stock*$precioxunidad);
                    $ingredientes .= '
                        <tr onclick="openIngredientOptions(\''.$id.'\')">
                            <td>'.$id.'</td>
                            <td>'.$nombre.'</td>
                            <td>$'.miles($costo).'</td>
                            <td>'.$unidad.'</td>
                            <td>'.$stock.'<span>'.units($unidad).'</span></td>
                            <td>'.$min.'<span>'.units($unidad).'</span></td>
                            <td>$'.$precioxunidad.'</td>
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
        $ingre = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
        $ingre -> bind_param('i',$id);
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
                "costo_total" => $ing['costo']*$ing['stock'],
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
            $cns = $con->prepare("SELECT * FROM ingredientes WHERE id = ?");
            $cns -> bind_param('i',$id);
            $cns -> execute();
            $Rcns = $cns -> get_result();
            if($Rcns -> num_rows > 0){
                $insum = $Rcns -> fetch_assoc();
                $ingrediente = $insum['nombre'];
                $unidad = $insum['unidad'];
                $costo = $insum['costo'];
                $costo_total = $costo*$cantid;
                $upcant = $con -> prepare("UPDATE ingredientes SET stock = COALESCE(stock, 0) + ? WHERE id = ?");
                $upcant -> bind_param('di', $cantid, $id);
                $upcant -> execute();
                if($upcant -> affected_rows > 0){
                    $inv = $con->prepare("INSERT INTO inversion (idinsumo, concepto, cantidad, unidad, costo, registrado_por) VALUES (?, ?, ?, ?, ? ,?)");
                    $inv -> bind_param('isdsds',$id,$ingrediente,$cantid,$unidad,$costo_total,$sesion);
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

    elseif(isset($_GET['modify']) && $_GET['modify'] === $clav){
        try {
            $id = $_POST['id'];
            $ingrediente = htmlspecialchars($_POST['ingrediente']);
            $stock = $_POST['stock'];
            $minimo = $_POST['stock_minimo'];
            $costo = $_POST['costo']/$stock;
            $costo_total = $_POST['costo'];
            $unidad = $_POST['unidad'];
            $vencimiento = !empty($_POST['vencimiento']) ? $_POST['vencimiento'] : null;
            $ins = $con -> prepare("UPDATE ingredientes SET nombre = ?, costo = ?, unidad = ?, stock = ?, stock_minimo = ?, vencimiento = ? WHERE id = ?");
            $ins -> bind_param('sdsddsi', $ingrediente, $costo, $unidad, $stock, $minimo, $vencimiento,$id);
            $ins -> execute();
            $inv = $con->prepare("UPDATE inversion SET idinsumo = ?, concepto = ?, cantidad = ?, unidad = ?, costo = ?, registrado_por = ? WHERE id = ?");
            $inv -> bind_param('isdsdsi',$id,$ingrediente,$stock,$unidad,$costo_total,$sesion,$id);
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