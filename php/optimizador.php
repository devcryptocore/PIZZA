<?php
    include("funcion.php");
    function imagen($directorio,$names){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nuevoNombre = "subida-" . substr(str_shuffle($permitted_chars), 0, 16);
        $patch=$directorio;
        $max_ancho = 1280;
        $max_alto = 900;
        $name = $names;
        $nombrearchivo=sanear_string($name['name']).str_replace("/",".",$name['type']);
        $rutaDeGuardado="";
        if($name['type']=='image/png' || $name['type']=='image/jpg' || $name['type']=='image/jpeg' || $name['type']=='image/gif'){    
            $medidasimagen= getimagesize($name['tmp_name']);
            if($medidasimagen[0] < 1280 && $name['size'] < 100000){       
                $foto = $patch.'/'.$nuevoNombre.$nombrearchivo;
                move_uploaded_file($name['tmp_name'], $patch.'/'.$nuevoNombre.$nombrearchivo);      
            }
            else {
                $rtOriginal=$name['tmp_name'];
                if($name['type']=='image/jpeg' || $name['type']=='image/jpg'){
                    $original = imagecreatefromjpeg($rtOriginal);
                }
                else if($name['type']=='image/png'){
                    $original = imagecreatefrompng($rtOriginal);
                }
                else if($name['type']=='image/gif'){
                    $original = imagecreatefromgif($rtOriginal);
                }  
                list($ancho,$alto)=getimagesize($rtOriginal);
                $x_ratio = $max_ancho / $ancho;
                $y_ratio = $max_alto / $alto;
                if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
                    $ancho_final = $ancho;
                    $alto_final = $alto;
                }
                elseif (($x_ratio * $alto) < $max_alto){
                    $alto_final = ceil($x_ratio * $alto);
                    $ancho_final = $max_ancho;
                }
                else{
                    $ancho_final = ceil($y_ratio * $ancho);
                    $alto_final = $max_alto;
                }
                $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 
                imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
                $cal=8;
                if($name['type']=='image/jpeg' || $name['type']=='image/jpg'){
                    imagejpeg($lienzo,$patch."/".$nuevoNombre.$nombrearchivo);
                    $rutaDeGuardado = $patch."/".$nuevoNombre.$nombrearchivo;
                }
                else if($name['type']=='image/png'){
                    imagepng($lienzo,$patch."/".$nuevoNombre.$nombrearchivo);
                    $rutaDeGuardado = $patch."/".$nuevoNombre.$nombrearchivo;
                }
                else if($name['type']=='image/gif'){
                    imagegif($lienzo,$patch."/".$nuevoNombre.$nombrearchivo);
                    $rutaDeGuardado = $patch."/".$nuevoNombre.$nombrearchivo;
                }
                $foto = $rutaDeGuardado;
            }
        }
        else {
            echo 'nosoportado';
        }
        return $foto;
    }
?>