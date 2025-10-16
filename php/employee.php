<?php

    include('../includes/verificator.php');

    if(isset($_GET['set_new_employee']) && $_GET['set_new_employee'] === $clav) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $documento = $_POST['documento'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        if(isset($_FILES['foto_empleado'])) {
            $dir = "../res/images/empleados/" . sanear_string($documento);
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $fempleado = guardarFoto("foto_empleado", sanear_string($documento), $dir);
        }
        else {
            $fempleado = "../res/icons/image.svg";
        }
        $ins = $con -> prepare("INSERT INTO operadores (nombre,apellido,documento,telefono,direccion,email,foto) VALUES (?,?,?,?,?,?,?)");
        $ins -> bind_param('sssssss',$nombre,$apellido,$documento,$telefono,$direccion,$email,$fempleado);
        if($ins -> execute()) {
            echo json_encode([
                "status" => "success",
                "title" => "Empleado registrado",
                "message" => "Se ha almacenado la información de ".$nombre
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Ha ocurrido un error!",
                "message" => "No se ha podido almacenar la información de ".$nombre
            ]);
        }
    }

    if(isset($_GET['get_employees']) && $_GET['get_employees'] === $clav) {
        $data = "Sin registros para mostrar";
        $cons = $con -> prepare("SELECT * FROM operadores ORDER BY id DESC");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $data = "";
            $isuser = "";
            while($obl = mysqli_fetch_array($Rcons)){
                $ido = $con -> prepare("SELECT documento FROM usuarios WHERE documento = ?");
                $ido -> bind_param('s',$obl['documento']);
                $ido -> execute();
                $Rido = $ido -> get_result();
                if($Rido -> num_rows > 0){
                    $isuser = "";
                }
                else {
                    $isuser = '<button class="actButton def" onclick="opr_action(\'def\',\''.$obl['documento'].'\')"></button>';
                }
                $fotoem = $obl['foto'] ?? '../res/icons/user.svg';
                $data .= '
                    <tr>
                        <td>'.$obl['id'].'</td>
                        <td>
                            <div class="emphoto" style="background-image:url(\''.$fotoem.'\')"></div>
                        </td>
                        <td>'.$obl['nombre'] . ' ' . $obl['apellido'] . '</td>
                        <td>'.$obl['documento'].'</td>
                        <td><a href="https://wa.me/+57'.$obl['telefono'].'" target="_blank">'.$obl['telefono'].'</a></td>
                        <td>'.$obl['direccion'].'</td>
                        <td><a href="mailto:'.$obl['email'].'" target="_blank">'.$obl['email'].'</td>
                        <td>'.$obl['fecharegistro'].'</td>
                        <td>
                            '.$isuser.'
                            <button class="actButton mod" onclick="opr_action(\'mod\',\''.$obl['id'].'\')"></button>
                            <button class="actButton del" onclick="opr_action(\'del\',\''.$obl['id'].'\')"></button>
                        </td>
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

    if(isset($_GET['mod_employee']) && $_GET['mod_employee'] === $clav) {
        $id = $_POST['id'];
        $cons = $con -> prepare("SELECT * FROM operadores WHERE id = ?");
        $cons -> bind_param('i',$id);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $opr = $Rcons -> fetch_assoc();
            $mes = '
                <div class="form-container">
                    <form id="set_mod_employee">
                        <input type="hidden" name="id" value="'.$opr['id'].'">
                        <div class="oneInput">    
                            <div class="inputContainer" style="background: url(../res/icons/new-user.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="nombre" id="nombre" required value="'.$opr['nombre'].'">
                                <label for="nombre" class="active-label">Nombre</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/new-user.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="apellido" id="apellido" required value="'.$opr['apellido'].'">
                                <label for="apellido" class="active-label">Apellido</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/v-card.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="documento" id="documento" required value="'.$opr['documento'].'">
                                <label for="documento" class="active-label">Documento</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/phone.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="tel" name="telefono" id="telefono" required value="'.$opr['telefono'].'">
                                <label for="telefono" class="active-label">Teléfono</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/address.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="direccion" id="direccion" required value="'.$opr['direccion'].'">
                                <label for="direccion" class="active-label">Dirección</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/email.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="email" id="email" required value="'.$opr['email'].'">
                                <label for="email" class="active-label">E-mail</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer con-image" style="justify-content:center;">
                                <input type="hidden" name="old_ephoto" value="'.$opr['foto'].'">
                                <input type="file" name="foto_empleado" id="portada" class="form-image" value="'.($opr['foto'] ?? '').'">
                                <label for="portada" id="forPortada" class="fore-photo" style="background:url('.($opr['foto'] ?? '').') center / cover no-repeat;"></label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" value="Modificar" class="send-button">
                        </div>
                    </form>
                </div>
            ';            
        }
        else {
            $mes = "Sin datos para este empleado";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Modificar empleado",
            "message" => $mes
        ]);
    }

    if(isset($_GET['set_mod_employee']) && $_GET['set_mod_employee'] === $clav) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $documento = $_POST['documento'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        if(isset($_FILES['foto_empleado']) && $_FILES['foto_empleado']['error'] === UPLOAD_ERR_OK) {
            $dir = "../res/images/empleados/" . sanear_string($documento);
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $fempleado = guardarFoto("foto_empleado", sanear_string($documento), $dir);
        }
        else {
            $fempleado = $_POST['old_ephoto'] ?? "../res/icons/image.svg";
        }
        $consdoc = $con -> prepare("SELECT documento FROM operadores WHERE id = ?");
        $consdoc -> bind_param('i',$id);
        $consdoc -> execute();
        $Rconsdoc = $consdoc -> get_result();
        $dcu = $Rconsdoc -> fetch_assoc()['documento'];

        $coad = $con -> prepare("SELECT * FROM usuarios WHERE documento = ?");
        $coad -> bind_param('s',$dcu);
        $coad -> execute();
        $Rcoad = $coad -> get_result();
        if($Rcoad -> num_rows > 0){
            $i = $Rcoad -> fetch_assoc();
            $idu = $i['id'];
            $upus = $con -> prepare("UPDATE usuarios SET documento = ? WHERE id = ?");
            $upus -> bind_param('si',$documento,$idu);
            $upus -> execute();
        }
        
        $ins = $con -> prepare("UPDATE operadores SET nombre = ?,apellido = ?,documento = ?,telefono = ?,direccion = ?,email = ?, foto = ? WHERE id = ?");
        $ins -> bind_param('sssssssi',$nombre,$apellido,$documento,$telefono,$direccion,$email,$fempleado,$id);
        if($ins -> execute()) {
            echo json_encode([
                "status" => "success",
                "title" => "Empleado modificado",
                "message" => "Se ha modificado la información de ".$nombre
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Ha ocurrido un error!",
                "message" => "No se ha podido modificar la información de ".$nombre
            ]);
        }
    }

    if(isset($_GET['del_employee']) && $_GET['del_employee'] === $clav) {
        $id = $_POST['id'];
        $dl = $con -> prepare("DELETE FROM operadores WHERE id = ?");
        $dl -> bind_param('i',$id);
        if($dl -> execute()){
            $consdoc = $con -> prepare("SELECT documento FROM operadores WHERE id = ?");
            $consdoc -> bind_param('i',$id);
            $consdoc -> execute();
            $Rconsdoc = $consdoc -> get_result();
            $dcu = $Rconsdoc -> fetch_assoc()['documento'];
            $coad = $con -> prepare("SELECT * FROM usuarios WHERE documento = ?");
            $coad -> bind_param('s',$dcu);
            $coad -> execute();
            $Rcoad = $coad -> get_result();
            if($Rcoad -> num_rows > 0){
                $i = $Rcoad -> fetch_assoc();
                $idu = $i['id'];
                $upus = $con -> prepare("DELETE FROM usuarios WHERE id = ?");
                $upus -> bind_param('i',$idu);
                $upus -> execute();
            }
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha eliminado los datos del empleado."
            ]);
        }
        else  {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido eliminar los datos del empleado"
            ]);
        }
    }

    if(isset($_GET['set_admindata']) && $_GET['set_admindata'] === $clav) {
        $usuario = sanear_string(htmlspecialchars($_POST['usuario']));
        $confcontrasena = htmlspecialchars($_POST['conf-contrasena']);
        $contrasena = htmlspecialchars($_POST['contrasena']);
        $documento = htmlspecialchars($_POST['documento']);
        $rol = htmlspecialchars($_POST['rol']);
        $sucursal = htmlspecialchars($_POST['sucursal']);
        $estado = 1;
        $password = password_hash($contrasena, PASSWORD_DEFAULT);

        if($contrasena !== $confcontrasena){
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "Las contraseñas no coinciden"
            ]);
            exit;
        }

        $ins = $con -> prepare("INSERT INTO usuarios (documento,rol,usuario,contrasena,sucursal,estado) VALUES (?,?,?,?,?,?)");
        $ins -> bind_param('sssssi',$documento,$rol,$usuario,$password,$sucursal,$estado);
        if($ins -> execute()) {
            echo json_encode([
                "status" => "success",
                "title" => "Acceso creado!",
                "message" => "Ahora el usuario tiene acceso al sistema como " . $rol
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido realizar el registro del usuario: " . $con -> error
            ]);
        }
    }

?>