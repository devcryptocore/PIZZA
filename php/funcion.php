<?php
	function miles($mil) {
		if (strlen($mil) == 4) {
			$mil = substr($mil, 0, strlen($mil) - 3) . '.' . substr($mil, -3);
		}
		elseif (strlen($mil) == 5) {
			$mil = substr($mil, 0, strlen($mil) - 3) . '.' . substr($mil, -3);
		}
		elseif (strlen($mil) == 6) {
			$mil = substr($mil, 0, strlen($mil) - 3) . '.' . substr($mil, -3);
		}
		elseif (strlen($mil) == 7) {
			$mil = substr($mil, 0, strlen($mil) - 6) . ',' . substr($mil, 1, strlen($mil) - 4) . "." .  substr($mil, -3);
		}
		elseif (strlen($mil) == 8) {
			$mil = substr($mil, 0, strlen($mil) - 6) . ',' . substr($mil, 2, strlen($mil) - 5) . "." .  substr($mil, -3);
		}
		elseif (strlen($mil) == 9) {
			$mil = substr($mil, 0, strlen($mil) - 6) . ',' . substr($mil, 3, strlen($mil) - 6) . "." .  substr($mil, -3);
		}
		return $mil;
	}

	function utilidad($valor1,$valor2) {
		$resta = $valor1-$valor2;
		if($resta <= 0){
			$divi = 0;
		}
		else {
			$divi = $resta/$valor2;
		}
		$utilidad = $divi*100;
		if ($valor2 != 0) {
			return $utilidad;
		}
		else {
			return 0;
		}
	}

	function meses($num) {
		/*if ($num < 9) {
			$num = substr($num, 1);
		}*/
		$meses = array("Enero","Febrero","Marzo",
						"Abril","Mayo","Junio",
						"Julio","Agosto","Septiembre",
						"Octubre","Noviembre","Diciembre");
		return $meses[$num - 1];
	}

    function sanear_string($string){
        $string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
        $string = str_replace(
            array("¨", "º", "-", "~",
	                "#", "@", "|", "!", " ",
                    "·", "$", "%", "&", "/",
                    "(", ")", "?", "'", "¡",
                    "¿", "[", "^", "<code>", "]",
                    "+", "}", "{", "¨", "´",
                    ">", "< ", ";", ",", ":",
                    "."),
                '',
            $string
        );    
        return $string;
    }

    function format_fecha($fecha) {
    	$mes = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
    	$year = substr($fecha,0,4);
    	$month = substr($fecha,5,-3);
    	if ($month <= 9) {
    		$month = str_replace("0","",$month);
    	}
    	$day = substr($fecha, 8,9);
    	$mnt = $mes[$month-1];
    	return $day."/".$mnt."/".$year;
    }

    function zero($value) {
    	$limite = 7;
    	if (strlen($value) > $limite) {
    		$limite= $limite+1;
    	}
    	$valor = str_repeat("0",$limite-strlen($value)).$value;
    	return $valor;
    }

	function styleUtility($typ,$value){
		$svg = "";
		$color = "";
		$values = "0%";
		if ($value >= 76){
			$color = "#00cdb9";
			$svg = 'up';
		}
		if ($value <=75){
			$color = "#9dff00";
			$svg = 'up';
		}
		if ($value <= 50){
			$color = "#ff9800";
			$svg = 'middle';
		}
		if ($value <= 25){
			$color = "#ff5200";
			$svg = 'middle';
		}
		if ($value <= 10){
			$color = "#ff2b3e";
			$svg = 'down';
		}
		if($typ == 'small'){
			$values = "<b style='color:$color;background: url(../../resources/assets/imagenes/svgs/$svg.svg) left no-repeat;background-size: 15px;'>".$value."%</b>";
		}
		else {
			$values = "<b style='color:$color;background: url(../../resources/assets/imagenes/svgs/$svg.svg) left no-repeat;background-size: 25px;'>".$value."%</b>";
		}
		return $values;
	}

	function aLetras($numero) {
		$unidades = ["", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
		$decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
		$centenas = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
		if ($numero == 0) {
			return "cero";
		}
		if ($numero < 0) {
			return "menos " . aLetras(abs($numero));
		}
		$letras = "";
		if ($numero >= 1000) {
			$miles = intval($numero / 1000);
			$letras .= ($miles == 1 ? "mil" : aLetras($miles) . " mil");
			$numero %= 1000;
		}
		if ($numero >= 100) {
			$centena = intval($numero / 100);
			$letras .= " " . ($centena == 1 && $numero % 100 == 0 ? "cien" : $centenas[$centena]);
			$numero %= 100;
		}
		if ($numero >= 20) {
			$decena = intval($numero / 10);
			$letras .= " " . $decenas[$decena];
			$numero %= 10;
	
			if ($numero > 0) {
				$letras .= " y " . $unidades[$numero];
			}
		} else if ($numero > 0) {
			$especiales = [
				10 => "diez", 11 => "once", 12 => "doce", 13 => "trece", 14 => "catorce", 15 => "quince",
				16 => "dieciséis", 17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve"
			];
			$letras .= " " . ($numero < 10 ? $unidades[$numero] : $especiales[$numero]);
		}
		return trim($letras);
	}

	function dominio($url) {
		$codUrl = parse_url($url);
		$dominio = $codUrl['host'];
		$www = explode('.',$dominio);
		return implode('.', array_slice($www, -2));
	}

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

	function guardarFoto($campo, $producto, $dir) {
        if (!empty($_FILES[$campo]['name'])) {
            $idFoto = uniqid();
            $ext = pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION);
            $nombreArchivo = preg_replace('/[^a-zA-Z0-9-_]/', '_', $producto) . "_{$idFoto}." . $ext;
            $ruta = $dir . "/" . $nombreArchivo;

            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $ruta)) {
                return $ruta;
            }
        }
        return null;
    }

?>