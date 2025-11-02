<?php
    error_reporting(E_ALL);

    use Dotenv\Dotenv;

    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    //$dotenv = Dotenv::createImmutable(__DIR__ .'/../../');
    $dotenv -> load();

    $dbhost = $_ENV['DB_HOST'];
    $dbuser = $_ENV['DB_USER'];
    $dbpass = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];
    $clav = base64_encode($_ENV['WORD']);
    $exclav = base64_encode($_ENV['EXWORD']);

    $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $con->set_charset("utf8mb4");
    if($con -> connect_error) {
        die("Error de conexión: " . $con->connect_error);
    }
    
    date_default_timezone_set('America/Bogota');
    $fecha = date('Y-m-d');
    $fullfecha = date('Y-m-d H:i:s');
    $version = time();
    $default_image = "../res/icons/image.svg";
    $dominio = "https://".$_SERVER['HTTP_HOST'];

    function imprimirArchivosDirectorio($directorios) {
        $archivos = [];
        $exclusion_extensiones = ['html', 'json', 'txt'];
        $exclusion_archivos = ['users.php','errorhandler.php','page.php','nextflow_bot.php'];
        foreach ($directorios as $directorio) {
            if (!is_dir($directorio)) {
                continue;
            }
            if ($gestor = opendir($directorio)) {
                while (false !== ($archivo = readdir($gestor))) {
                    if ($archivo !== "." && $archivo !== "..") {
                        $extension = pathinfo($archivo, PATHINFO_EXTENSION);
                        if (
                            !in_array(strtolower($archivo), $exclusion_archivos) &&
                            !in_array(strtolower($extension), $exclusion_extensiones)
                        ) {
                            $archivos[] = $archivo;
                        }
                    }
                }
                closedir($gestor);
            }
        }
        return $archivos;
    }

    $plantilla_403 = '
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>403 Forbidden</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://fonts.googleapis.com/css2?family=Fira+Mono&display=swap" rel="stylesheet">
                <style>
                    html, body {
                        margin: 0;
                        padding: 0;
                        background-color: #000;
                        color: #fff;
                        font-family: "Fira Mono", monospace;
                        font-size: 12px;
                        line-height: 1.5;
                        overflow-x: hidden;
                    }
                    .container {
                        padding: 20px;
                        max-width: 100%;
                        box-sizing: border-box;
                    }
                    pre {
                        white-space: pre-wrap;
                        word-wrap: break-word;
                        font-family: "Fira Mono", monospace;
                        font-weight: 400;
                    }
                    .scontainer {
                        position: fixed;
                        display: flex;
                        flex-direction: column;
                        bottom: 20%;
                        right: 15%;
                        align-items: center;
                        justify-content: center;
                        line-height: 60px;
                    }
                    .glitch {
                        position: relative;
                        font-size: 8em;
                        font-weight: bold;
                        color: #ffffff;
                        letter-spacing: 3px;
                        z-index: 1;
                    }
                    .small {
                        font-size: 2em;
                        letter-spacing: 5px;
                    }
                    .glitch:before,
                    .glitch:after {
                        display: block;
                        content: attr(data-text);
                        position: absolute;
                        top: 0;
                        left: 0;
                        opacity: 0.8;
                    }
                    .glitch:before {
                        animation: glitch-it 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) both infinite;
                        color: #00ffff;
                        z-index: -1;
                    }
                    .glitch:after {
                        animation: glitch-it 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) reverse both
                            infinite;
                        color: #ff00ff;
                        z-index: -2;
                    }
                    @keyframes glitch-it {
                        0% {
                            transform: translate(0);
                        }
                        20% {
                            transform: translate(-2px, 2px);
                        }
                        40% {
                            transform: translate(-2px, -2px);
                        }
                        60% {
                            transform: translate(2px, 2px);
                        }
                        80% {
                            transform: translate(2px, -2px);
                        }
                        to {
                            transform: translate(0);
                        }
                    }
                </style>
            </head>
            <body>
            <div class="container">
                <pre id="terminal-output"></pre>
                <div class="scontainer">
                    <div class="glitch" data-text="403">403</div>
                    <div class="glitch small" data-text="FORBIDDEN">FORBIDDEN</div>
                </div>
            </div>
            <script>
                const texto = `
                    [  403.000000] KERNEL PANIC - ACCESS DENIED
                    [  403.000001] Unauthorized attempt to access protected resource
                    [  403.000006] --- STOPPING ALL PROCESSES ---
                    [  403.000007] ide1: BM-DMA at 0x006-0xc00f, BIOS settings: hdc:pio, hdd:pio
                    neZk-pci.c:v1.03 '.date('d/m/Y').' D. Becker/p. Gortmaker
                    hda: QUEMU HARDDISK, ATA DISK drive
                    ide0 at 0x1f0-0x1f7,0x3f6 on irq 14
                    hdc: QUEMU CD-ROM, ATAPI CD/DVD-ROM drive
                    ide1: ad 0x170-0x177,0x376 on irq 15
                    ACPI: PCI Interrupt Link [LNCK] enabled ar IRQ 10
                    ACPI: PCI Interrupt 0000:00:03.0[A] -> Link [LNCK] -> GSI 10 (level1, low) -> IRQ
                    10
                    eth0: RealTek RTL-8029 found at 0xc100, IRQ 10, 52:54:00:12:34:56
                    hda: max request size: 512KiB
                    hda: 180224 sectors (92 MB) w/256KiB Cache, CHS=178/255/63, (U)DMA
                    hda: set_multimode: status=0x41 { DriveReady Error }
                    hda: set_multimode: error-0x04 { DriveStatusError }
                    ide: failed opcode was: 0xef
                    hda: cache flushes supported
                      hda: hda1
                    hdc: ATAPI 4X CD-ROM drive, 512kB Cache, (U)DMA
                    Uniform CD-ROM driver Revision 3.20
                    Done.
                    Begin: Mounting root file system... ...
                    /init /init: 151: Syntax error: 0xforce=panic
                    Kernel panic - not syncing: Attempted to kill init!
                    Developed by ©Cryptocore
                `;
                const pre = document.getElementById("terminal-output");
                let i = 0;
                function escribir() {
                    if (i < texto.length) {
                        pre.textContent += texto.charAt(i);
                        i++;
                        setTimeout(escribir, 5);
                    }
                }
                escribir();
                const asciiart = `
                
                    ██   ██  ██████  ██████  
                    ██   ██ ██  ████      ██ 
                    ███████ ██ ██ ██  █████  
                         ██ ████  ██      ██ 
                         ██  ██████  ██████  
                `;

                console.log("%cDatos de dispositivo almacenados", "color: lightgreen; background: black; font-family: monospace;");
                console.log("%cNO PERMITIDO: No tienes permiso para estar en esta sección!",
                            "color: red; background: black; font-family: monospace; font-weight: bold;");
                console.log(`%c${asciiart}`, "font-family: monospace;color: lightgreen; font-size:6px;line-height: 1;text-align: left;");
                (function () {
                    const isProduction = location.hostname !== "localhost";
                    if (isProduction) {
                        document.addEventListener("keydown", function(event) {
                            if (event.key === "F12" || (event.ctrlKey && event.shiftKey && event.key === "I")) {
                                event.preventDefault();
                                return false;
                            }
                        });
                        document.addEventListener("contextmenu", function(event) {
                            event.preventDefault();
                        });
                    }
                })();
            </script>
            </body>
        </html>
    ';

    $current_script_path = realpath($_SERVER['SCRIPT_FILENAME']);
    $current_script = basename($current_script_path);
    $script_dir     = dirname($current_script_path);

    $exclusiones_completas = [
        realpath(__DIR__ . '/../dashboard/index.php'),
        realpath(__DIR__ . '/../4/index.php')
    ];
    if (!in_array($current_script_path, $exclusiones_completas)) {
        $public_scripts = imprimirArchivosDirectorio([$script_dir]);
        if (in_array($current_script, $public_scripts)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['usuario'])) {
                http_response_code(403);
                echo $plantilla_403;
                exit;
            }
        }
    }

?>