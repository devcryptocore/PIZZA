<?php

    include('config/connector.php');
    /*$token = "8207105568:AAEG1yPhGRh3PJHPklzuWX7vndpjI77IM_Q";*/
    $apiURL = "https://api.telegram.org/bot";
    
    if(isset($_GET['get_bot_state'])) {
        $botinfo = $con -> prepare("SELECT * FROM telegram_bot");
        $botinfo -> execute();
        $Rbotinfo = $botinfo -> get_result();
        $databot = $Rbotinfo -> fetch_assoc();
        $token = $databot['tkn'];
        $apiURL = $apiURL . $token . "/";
        $chat_id = $databot['chatid'] ?? '';
        if (isset($_GET['set_bot']) && $_GET['set_bot'] === $clav) {
            $tkn = htmlspecialchars($_POST['token']) ?? '';
            $ins = $con->prepare("INSERT INTO telegram_bot (tkn) VALUES (?)");
            $ins->bind_param('s', $tkn);
            if ($ins->execute()) {
                $webhookURL = $dominio . "/nextflow_bot.php?get_bot_state";
                $info = file_get_contents("https://api.telegram.org/bot{$tkn}/getWebhookInfo");
                $info = json_decode($info, true);
                $webhookActual = $info["result"]["url"] ?? '';
                if ($webhookActual !== $webhookURL) {
                    $setWebhook = file_get_contents("https://api.telegram.org/bot{$tkn}/setWebhook?url={$webhookURL}");
                    $setWebhook = json_decode($setWebhook, true);
                    if (!empty($setWebhook["ok"]) && $setWebhook["ok"] === true) {
                        echo json_encode([
                            "status" => "success",
                            "title" => "Correcto!",
                            "message" => "Bot configurado y webhook registrado con éxito!"
                        ]);
                        exit;
                    } else {
                        echo json_encode([
                            "status" => "error",
                            "title" => "Error al configurar Webhook",
                            "message" => "Token guardado, pero no se pudo registrar el webhook. Verifique el token."
                        ]);
                        exit;
                    }
                } else {
                    echo json_encode([
                        "status" => "success",
                        "title" => "Bot activo",
                        "message" => "El bot ya estaba configurado con este dominio!"
                    ]);
                    exit;
                }
            }
            echo json_encode([
                "status" => "error",
                "title" => "Error",
                "message" => "No se pudo guardar el token en la base de datos."
            ]);
            exit;
        }

        if(isset($_GET['update_bot']) && $_GET['update_bot'] === $clav){
            $tkn = htmlspecialchars($_POST['token']) ?? '';
            $ins = $con -> prepare("UPDATE telegram_bot SET tkn = ?");
            $ins -> bind_param('s',$tkn);
            if($ins -> execute()){
                echo  json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Se ha reconfigurado su bot correctamente!"
                ]);
            }
            else {
                echo  json_encode([
                    "status" => "error",
                    "title" => "No configurado",
                    "message" => "Bot no configurado!"
                ]);
            }
            exit;
        }

        file_put_contents(__DIR__."/log.txt",
            date("Y-m-d H:i:s")." -> ".file_get_contents("php://input")."\n",
            FILE_APPEND
        );

        $update = json_decode(file_get_contents("php://input"), true);
        //$chat_id = null;

        if (isset($update["message"]["chat"]["id"])) {
            $chat_id = $update["message"]["chat"]["id"];
        } elseif (isset($update["callback_query"]["message"]["chat"]["id"])) {
            $chat_id = $update["callback_query"]["message"]["chat"]["id"];
        }

        $text = $update["message"]["text"] ?? null;
        $callback_data = $update["callback_query"]["data"] ?? null;

        $stateFile = __DIR__ . "/state_$chat_id.json";
        $state = file_exists($stateFile) ?
            json_decode(file_get_contents($stateFile), true) : [];

        function sendMessage($chat_id, $text, $keyboard = null) {
            global $apiURL;

            $data = [
                "chat_id" => $chat_id,
                "text" => $text,
                "parse_mode" => "HTML"
            ];
            if ($keyboard) {
                $data["reply_markup"] = json_encode([
                    "inline_keyboard" => $keyboard
                ]);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL . "sendMessage");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            file_put_contents(__DIR__."/log.txt",
                "sendMessage result: ".$result.PHP_EOL,
                FILE_APPEND
            );
            curl_close($ch);
            if ($result === false) {
                return false;
            }
            $json = json_decode($result, true);
            return isset($json["ok"]) && $json["ok"] === true;
        }

        function registrarWebhook($token, $url) {
            $endpoint = "https://api.telegram.org/bot{$token}/setWebhook?url={$url}";
            return file_get_contents($endpoint);
        }

        if ($text && strpos($text, "/start") === 0) {
            sendMessage($chat_id, "👋 ¡Hola!\n\nComandos disponibles:\n/setbot → Configurar bot");
            http_response_code(200);
            exit;
        }

        if ($text === "/setbot") {

            file_put_contents($stateFile, json_encode($state));

            if (!isset($con)) {
                sendMessage($chat_id, "❌ Error en conexión a la base de datos.");
                http_response_code(200);
                exit;
            }

            $cons = $con -> prepare("SELECT chatid FROM telegram_bot");
            $cons -> execute();
            $Rcons = $cons -> get_result();

            if($Rcons -> num_rows > 0) {
                $bid = $Rcons -> fetch_assoc()['chatid'];
                if(strlen($bid) > 0 || $bid != ""){
                    sendMessage($chat_id, "<b>El bot ya ha sido configurado!</b>");
                    http_response_code(200);
                    exit;
                }
            }

            $upd = $con -> prepare("UPDATE telegram_bot SET chatid = ?");
            $upd -> bind_param('s', $chat_id);

            if ($upd -> execute()) {
                sendMessage($chat_id, "<b>✅ Bot configurado correctamente</b>");
            } else {
                sendMessage($chat_id, "<b>❌ No se pudo configurar el bot.</b>");
            }

            http_response_code(200);
            exit;
        }

        if(isset($_GET['set_pedido'])){
            $nombre = $_POST['nombre'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $comentario = $_POST['comentario'] ?? '';
            $pedido = $_POST['pedido'] ?? '';
            $total = $_POST['total'] ?? 0;
            $fecha = $_POST['fecha'] ?? '';
            $coordenadas = $_POST['coordenadas'] ?? 'No definido';

            $mensaje = "";
            $mensaje .= "▁▂▃▅▆ ɴᴜᴇᴠᴏ ᴘᴇᴅɪᴅᴏ ▆▅▃▂▁\n\n\n";
            $mensaje .= "👥 <b>Nombre: </b> ". $nombre . "\n";
            $mensaje .= "📱 <b>Teléfono: </b> ". $telefono . "\n";
            $mensaje .= "📫 <b>Dirección: </b> ". $direccion . "\n";
            $mensaje .= "🧾 <b>Pedido: </b> " . "\n\n" . $pedido . "\n\n";
            $mensaje .= "💰 <b>Total:  $". $total . "</b>" . "\n\n";
            $mensaje .= "🕒 <i>Realizado el ". $fecha . "</i>\n\n";
            $mensaje .= "📍 <b>Ubicación: </b>". $coordenadas . "\n\n";
            $mensaje .= $dominio . "\nᴰᵉᵛᵉˡᵒᵖᵉᵈ ᵇʸ ᶜʳʸᵖᵗᵒᶜᵒʳᵉ";
            $keyboard = [];
            $keyboard[] = [
                [
                    "text" => "📲 Contactar por WhatsApp",
                    "url" => "https://wa.me/57" . preg_replace('/\D/', '', $telefono)
                ]
            ];
            if ($coordenadas != "No definido") {
                $coordsClean = str_replace(" ", "", $coordenadas);
                $keyboard[] = [
                    [
                        "text" => "📍 Ver Ubicación",
                        "url" => "https://www.google.com/maps?q=" . $coordsClean
                    ]
                ];
            }

            if(!sendMessage($chat_id, $mensaje, $keyboard)){
                echo json_encode([
                    "status" => "error",
                    "title" => "Error!",
                    "message" => "Lo sentimos, no podemos procesar su pedido en el momento\nPor favor, comuniquese directamente con nosotros - " . $chat_id
                ], JSON_UNESCAPED_UNICODE);
            }
            else {
                echo json_encode([
                    "status" => "success",
                    "title" => "Correcto!",
                    "message" => "Pedido realizado con éxito!\nNos pondrémos en contacto en un momento."
                ], JSON_UNESCAPED_UNICODE);
            }
        }

        if(isset($_GET['botdata'])) {
            $botinfo = $con -> prepare("SELECT * FROM telegram_bot");
            $botinfo -> execute();
            $Rbotinfo = $botinfo -> get_result();
            if($Rbotinfo -> num_rows > 0 && !isset($_GET['update_bot'])) {
                $databot = $Rbotinfo -> fetch_assoc();
                $token = $databot['tkn'];
                $apiURL = $apiURL . $token . "/";
                $chat_id = $databot['chatid'] ?? '';
                echo json_encode([
                    "status" => "success",
                    "title" => "ok",
                    "message" => [
                        "token" => $token
                    ]
                ]);
            }
            else {
                echo  json_encode([
                    "status" => "noconfigured",
                    "title" => "No configurado",
                    "message" => "Bot no configurado!"
                ]);
            }
            exit;
        }
    }


?>