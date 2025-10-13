<?php

    include('../config/connector.php');
    include('../config/errorhandler.php');
    include('../php/optimizador.php');

    if(isset($_GET['set_principal']) && $_GET['set_principal'] === $clav) {
        $cat = $_POST['categoria'];
        $cons = $con -> prepare("SELECT * FROM categoria_principal");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $smt = "UPDATE categoria_principal SET categoria = ?";
        }
        else {
            $smt = "INSERT INTO categoria_principal (categoria) VALUES (?)";
        }
        $ins = $con -> prepare($smt);
        $ins -> bind_param('s',$cat);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha establecido " . $cat . " como categoría principal"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha podido establecer la categoría principal!"
            ]);
        }
    }

    if(isset($_GET['get_categories']) && $_GET['get_categories'] === $clav) {
        $cons = $con -> prepare("SELECT * FROM categorias");
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            $cats = "";
            while($c = $Rcons -> fetch_assoc()) {
                $state = $c['estado'] == 1 ? 'Activo' : 'Inactivo';
                if(isset($_GET['table'])){
                    $cats .= '
                        <tr>
                            <td style="font-size:10px;text-transform:capitalize;">'.$c['categoria'].'</td>
                            <td style="text-align:center;"><img src="'.$c['imagen'].'" alt="category image" class="category-image"/></td>
                            <td style="font-size:10px;">'.$state.'</td>
                            <td><button onclick="deleteCat(\''.$c['id'].'\')" class="delButton"></button></td>
                        </tr>
                    ';
                }
                else {
                    $cats .= '
                        <option value="'.$c['categoria'].'" style="text-transform:capitalize;">'.$c['categoria'].'</option>
                    ';
                }
            }
        }
        else {
            $cats = "Sin categorías registradas";
        }
        echo json_encode([
            "status" => "success",
            "title" => "Ok",
            "message" => $cats
        ]);
    }

    if(isset($_GET['set_category']) && $_GET['set_category'] === $clav) {
        $estado = 1;
        $categoria = strtolower(htmlspecialchars($_POST['categoria']));
        if(isset($_FILES['foto_category'])) {
            $dir = "../res/images/categories/" . $categoria;
            if(!is_dir($dir)){
                mkdir($dir, 0777, true);
            }
            $category_image = guardarFoto("foto_category", $categoria, $dir);
        }
        else {
            $category_image = "../res/icons/image.svg";
        }
        $cons = $con -> prepare("SELECT * FROM categorias WHERE categoria = ?");
        $cons -> bind_param('s',$categoria);
        $cons -> execute();
        $Rcons = $cons -> get_result();
        if($Rcons -> num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "title" => "Categoría duplicada",
                "message" => "Esta categoría ya ha sido registrada antes!"
            ]);
            exit;
        }
        $ins = $con -> prepare("INSERT INTO categorias (categoria,imagen,estado) VALUES (?,?,?)");
        $ins -> bind_param('ssi',$categoria,$category_image,$estado);
        if($ins -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Categoría registrada!",
                "message" => "Se ha registrado " . $categoria . " correctamente"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha podido registrar la categoría!"
            ]);
        }
    }

    if(isset($_GET['del_category']) && $_GET['del_category'] === $clav) {
        $id = $_POST['id'];
        $cn = $con -> prepare("SELECT categoria FROM productos WHERE categoria = (SELECT categoria FROM categorias WHERE id = ?)");
        $cn -> bind_param('i',$id);
        $cn -> execute();
        $Rcn = $cn -> get_result();
        if($Rcn -> num_rows > 0){
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "Hay productos dentro de esta categoría!"
            ]);
            exit;
        }
        $del = $con -> prepare("DELETE FROM categorias WHERE id = ?");
        $del -> bind_param('i',$id);
        if($del -> execute()){
            echo json_encode([
                "status" => "success",
                "title" => "Correcto!",
                "message" => "Se ha eliminado la categoría"
            ]);
        }
        else {
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se ha podido eliminar la categoría!"
            ]);
        }
    }

?>