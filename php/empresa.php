<?php

    include('../includes/verificator.php');

    if(isset($_GET['get_company_data']) && $_GET['get_company_data'] === $clav) {
        $cons = $con -> prepare("SELECT c.*,a.* FROM company c LEFT JOIN about_section a ON 1");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $org = $Rcons -> fetch_assoc();
        $sucursales = [];
        $sucx = $con -> prepare("SELECT * FROM sucursales");
        $sucx -> execute();
        $Rsucx = $sucx -> get_result();
        if($Rsucx -> num_rows > 0) {
            while($sucs = $Rsucx -> fetch_assoc()) {
                $sucursales[$sucs['sucursal'] ?? ''] = [
                    "id" => $sucs['id'] ?? '',
                    "sucubicacion" => $sucs['ubicacion'] ?? '',
                    "sucdireccion" => $sucs['direccion'] ?? '',
                    "suctelefono" => $sucs['telefono'] ?? '',
                    "sucfoto" => $sucs['foto'] ?? ''
                ];
            }
        }
        echo json_encode([
            "status" => "success",
            "title" => "ok",
            "message" => [
                "organizacion" => $org['organizacion'] ?? '',
                "ptelefono" => $org['ptelefono'] ?? '',
                "stelefono" => $org['stelefono'] ?? '',
                "email" => $org['email'] ?? '',
                "direccion" => $org['direccion'] ?? '',
                "nit" => $org['nit'] ?? '',
                "encargado" => $org['encargado'] ?? '',
                "documento" => $org['documento'] ?? '',
                "logotipo" => $org['logotipo'] ?? '',
                "nosotros" => $org['nosotros'] ?? '',
                "fecha" => $org['fecharegistro'] ?? '',
                "faqs" => $org['faqs'] ?? '',
                "contacto" => $org['contacto'] ?? '',
                "sucursales" => $sucursales
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

    if(isset($_GET['get_sucursales']) && $_GET['get_sucursales'] === $clav){
        $cons = $con -> prepare("SELECT sucursal FROM sucursales");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0){
            $sucursales = "";
            $sucs = [];
            while($sc = $Rcons -> fetch_assoc()){
                $sucursales .= '
                    <option value="'.$sc['sucursal'].'">'.$sc['sucursal'].'</option>
                ';
                $sucs[] = [
                    "sucursal" => $sc['sucursal']
                ];
            }
            echo json_encode([
                "status" => "success",
                "title" => "ok",
                "message" => [
                    "sucursaleshtml" => $sucursales,
                    "sucursaleslist" => $sucs
                ]
            ]);
        }
        else {
            echo json_encode([
                "status" => "empty",
                "title" => "ok",
                "message" => [
                    "sucursaleshtml" => "",
                    "sucursaleslist" => ""
                ]
            ]);
        }
    }

    if(isset($_GET['set_sucursal']) && $_GET['set_sucursal'] === $clav) {
        if(isset($_GET['del'])){
            $id = $_POST['id'];
            $im = $con -> prepare("SELECT foto FROM sucursales WHERE id = ?");
            $im -> bind_param('i',$id);
            $im -> execute();
            $Rim = $im -> get_result();
            if($Rim -> num_rows > 0){
                $ft = $Rim -> fetch_assoc()['foto'] ?? '';
                if(strlen($ft) > 0){
                    unlink($ft);
                }
            }
            $dl = $con -> prepare("DELETE FROM sucursales WHERE id = ?");
            $dl -> bind_param('i',$id);
            if($dl -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "Sucursal eliminada!",
                    "message" => "Se ha eliminado la sucursal"
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error",
                    "message" => "No se ha podido eliminar la sucursal!"
                ]);
            }
            exit;
        }

        $sucursal = strtolower(htmlspecialchars($_POST['sucursal']));
        $ubicacion = htmlspecialchars($_POST['ubicacion']);
        $direccion = htmlspecialchars($_POST['direccion']);
        $telefono = htmlspecialchars($_POST['telefono']);

        if(isset($_FILES['foto_sucursal']) && $_FILES['foto_sucursal']['error'] === UPLOAD_ERR_OK) {
            $dir = "../res/images/sucursales/" . $sucursal;
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $sucursal_image = guardarFoto("foto_sucursal", $sucursal, $dir);
        }
        else {
            $sucursal_image = $_POST['old_photo'] ?? "../res/icons/image.svg";
        }

        if(isset($_GET['mod'])){
            $id = $_POST['id'];
            $ins = $con -> prepare("UPDATE sucursales SET 
            sucursal = ?,
            ubicacion = ?,
            direccion = ?,
            telefono = ?,
            foto = ? WHERE id = ?");
            $ins -> bind_param('sssssi',$sucursal,$ubicacion,$direccion,$telefono,$sucursal_image,$id);
            if($ins -> execute()){
                echo json_encode([
                    "status" => "success",
                    "title" => "Sucursal registrada!",
                    "message" => "Se ha actualizado " . $sucursal . " correctamente"
                ]);
            }
            else {
                echo json_encode([
                    "status" => "error",
                    "title" => "Error",
                    "message" => "No se ha podido actualizar la sucursal!"
                ]);
            }
            exit;
        }

        $cons = $con -> prepare("SELECT * FROM sucursales WHERE sucursal = ?");
        $cons -> bind_param('s',$sucursal);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "title" => "Sucursal duplicada",
                "message" => "Esta sucursal ya ha sido registrada antes!"
            ]);
            exit;
        }
        $ins = $con -> prepare("INSERT INTO sucursales (sucursal,ubicacion,direccion,telefono,foto) VALUES (?,?,?,?,?)");
        $ins -> bind_param('sssss',$sucursal,$ubicacion,$direccion,$telefono,$sucursal_image);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Sucursal registrada!",
                "message" => "Se ha registrado " . $sucursal . " correctamente"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha podido registrar la sucursal!"
            ]);
        }
    }

    if(isset($_GET['set_us_info']) && $_GET['set_us_info'] === $clav) {
        $type = $_GET['type'];
        
        $text = htmlspecialchars($_POST['texttopublish']);
        $cons = $con -> prepare("SELECT * FROM about_section");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $sent = "INSERT INTO about_section ({$type}) VALUES (?)";
        if($Rcons -> num_rows > 0){
            $sent = "UPDATE about_section SET {$type} = ?";
        }
        if(isset($_GET['action']) && $_GET['action'] == 'del'){
            $text = '';
        }
        $abo = $con -> prepare($sent);
        $abo -> bind_param('s',$text);
        if($abo -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha publicado el texto para la sección de " . $type
            ]);
        }
        else {
            echo json_encode([
                "status" => "exit",
                "title" => "Error!",
                "message" => "No se ha podido almacenar el texto: " . $con -> error
            ]);
        }
    }

    if(isset($_GET['set_publicy_img']) && $_GET['set_publicy_img'] === $clav) {
        if(isset($_FILES['foto_sucursal']) && $_FILES['foto_sucursal']['error'] === UPLOAD_ERR_OK) {
            $dir = "../res/images/publicidades/" . $sucursal;
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $sucursal_image = guardarFoto("foto_sucursal", $sucursal, $dir);
        }
        else {
            $sucursal_image = $_POST['old_photo'] ?? "../res/icons/image.svg";
        }
        $cons = $con -> prepare("SELECT faqs FROM about_section");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        $aop = $con -> prepare("INSERT INTO about_section (faqs) VALUES (?)");
        if($Rcons -> num_rows > 0) {
            $aop = $con -> prepare("UPDATE about_section SET faqs = ?");
        }
        $aop -> bind_param('s',$sucursal_image);
        if($aop -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha publicado la imagen para publicidad "
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error!",
                "message" => "No se ha podido almacenar la imagen: " . $con -> error
            ]);
        }
    }

?>