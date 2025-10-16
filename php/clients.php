<?php

    include('../includes/verificator.php');

    if (isset($_GET['com_client']) && $_GET['com_client'] === $clav) {
        $action = $_GET['action'] ?? null;
        if ($action === "save") {
            $nombre = $_POST["nombre"];
            $documento = $_POST["documento"];
            $direccion = $_POST["direccion"];
            $telefono = $_POST["telefono"];
            $id = $_POST["id"];
            if (!empty($_POST["id"])) {
                $stmt = $con->prepare("UPDATE clientes SET nombre=?, documento=?, direccion=?, telefono=? WHERE id=?");
                $stmt->bind_param("ssssd",$nombre,$documento,$direccion,$telefono,$id);
                $stmt->execute();
                echo json_encode(["status"=>"success","title" => "Correcto!","message"=>"Cliente actualizado correctamente"]);
            } else {
                $total = 0;
                $stmt = $con->prepare("INSERT INTO clientes (nombre,documento,direccion,telefono) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$nombre,$documento,$direccion,$telefono);
                $stmt->execute();
                echo json_encode(["status"=>"success","title" => "Correcto!","message"=>"Cliente registrado correctamente"]);
            }
        }
        if ($action === "delete") {
            $id = $_POST["id"];
            $stmt = $con->prepare("DELETE FROM clientes WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            echo json_encode(["status"=>"success","title" => "Correcto","message"=>"Cliente eliminado correctamente"]);
        }
        if ($action === "list") {
            $res = $con->query("SELECT * FROM clientes ORDER BY id DESC");
            $clientes = "";
            if($res -> num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $onclick = 'onclick="editarCliente(\''.$row['id'].'\',\''.$row['nombre'].'\',\''.$row['documento'].'\',\''.$row['direccion'].'\',\''.$row['telefono'].'\')"';
                    $clientes .= '
                        <tr>
                            <td '.$onclick.'>'.$row['id'].'</td>
                            <td '.$onclick.'>'.$row['nombre'].'</td>
                            <td '.$onclick.'>'.$row['documento'].'</td>
                            <td '.$onclick.'>'.$row['direccion'].'</td>
                            <td '.$onclick.'>'.$row['telefono'].'</td>
                            <td '.$onclick.'>'.$row['total_comprado'].'</td>
                            <td '.$onclick.'>'.$row['fecha_registro'].'</td>
                            <td>
                                <button class="delthisprd" onclick="eliminarCliente(\''.$row['id'].'\')"></button>
                            </td>
                        </tr>
                    ';
                }
            }
            else {
                $clientes = '<tr>Sin clientes para mostrar</tr>';
            }
            echo json_encode([
                "status" => "success",
                "title" => "correcto",
                "message" => $clientes
            ]);
        }
        exit;
    }

    if(isset($_GET['get_client']) && $_GET['get_client'] === $clav){
        $doc = $_POST['doc'];
        $consu = $con -> prepare("SELECT * FROM clientes WHERE documento = ?");
        $consu -> bind_param('s',$doc);
        $consu -> execute();
        $Rconsu = $consu -> get_result();
        if($Rconsu -> num_rows > 0){
            $cli = $Rconsu -> fetch_assoc();
            $res = '
                <div class="cli_cont">
                    <img src="../res/icons/user.svg" alt="cli_logo" class="clilogo">
                    <div class="cli_info">
                        <h3>'.$cli['nombre'].'</h3>
                        <button class="send-button" onclick="setClient(\''.$cli['nombre'].'\',\''.$cli['documento'].'\')">Establecer</button>
                    </div>
                </div>
            ';
        }
        else {
            $res = "<h2>No se ha encontrado el cliente</h2>";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Cliente encontrado",
            "message" => $res
        ]);
    }

?>