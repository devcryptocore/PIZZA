<?php
    $version = time();
    $cat = "";
    if(isset($_GET['c'])){
        $cat = $_GET['c'];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextFlow App</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#545454">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NextFlow">
    <meta name="description" content="NextFlow App">
    <meta name="theme-color" content="#d6d6d6">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="NextFlow" />
    <meta property="og:description" content="Food delivery a un click!" />
    <meta property="og:image" class="versionizedog" content="https://cryptocoreapp.com/nextflow/demospp.jpg" />
    <meta property="og:image:width" content="1024" />
    <meta property="og:image:height" content="632" />
    <meta property="og:url" content="https://cryptocoreapp.com/nextflow/" />
    <meta property="og:site_name" content="NextFlow" />
    <meta property="fb:app_id" content="368569449013481" />
    <link rel="apple-touch-icon" href="res/icons/icon-192x192.png">
    <link rel="shortcut icon" href="res/icons/pizza2.svg" type="image/x-icon">
    <link rel="stylesheet" href="css/ruleta.css" class="versionized">
    <link rel="stylesheet" href="css/style-mobile.css" class="versionized">
    <link rel="stylesheet" href="css/active-products.css?v=<?=$version;?>">
    <link rel="stylesheet" href="css/style-mobile.css?v=<?=$version;?>">
    <link rel="stylesheet" href="css/sweetAlert.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="res/izi/css/iziToast.css">
    <script src="js/aos.js"></script>
    <script src="js/sweetAlert.js"></script>
    <script src="js/fitty.min.js"></script>
    <script src="res/izi/js/iziToast.js"></script>
    <script type="module" src="js/active-product.js?v=<?=$version;?>"></script>
</head>
<style>
    body {
        background: #121619 url(res/images/pizafondo.webp) center / 100% repeat-y;
    }
</style>
<body>
    <div class="navBar">
        <div class="org-name">
            <img src="res/images/logotype.png" alt="Pizza Logo" id="companylogo">
            <h1 >NextFlow App</b></h1>
        </div>
        <div class="nav-left-cont">
            <button class="my-cart" id="my_cart"><span id="cartCount"></span></button>
            <input type="checkbox" id="burger">
            <label for="burger" class="burguer">
                <span class="part"></span>
                <span class="part"></span>
                <span class="part"></span>
            </label>
        </div>
    </div>
    <input type="hidden" id="nomcat" value="<?=$cat;?>">
    <div class="shopping-cart" id="shopping_cart"></div>
    <div class="banner-cont"></div>
    <h1 id="page-title">Categoría</h1>
    <div class="myIngredients" id="myMenu"></div>
    <input type="hidden" id="category" value="<?=$cat;?>">
    <section class="footer">
        <div class="footersquare">
            <div class="btcontent">
                <h3>Tu pedido está a un clic. ¡Ordéna ya!</h3>
                <span>¿Listo para ordenar? ¡Te esperamos!</span>
                <button class="dwapp" id="installPWA" data-aos="fade-up" data-aos-offset="290" data-aos-delay="100">Descarga la app</button>
            </div>
        </div>
        <div class="footercontent">
            <div class="org-name">
                <img data-aos="fade-down" data-aos-offset="0" src="res/images/logotype.png" alt="Pizza Logo" id="companylogo">
                <h1 data-aos="fade-right" data-aos-offset="0" data-aos-delay="290">MAX<b>PIZZA</b></h1>
            </div>
            <div class="powered">
                Developed by &copy; Cryptocore
            </div>
            <div class="sourcelist">
                <div class="sourcecontainer">
                    <a href="#" class="source">Menú</a>
                    <a href="#" class="source">Ubicaciones</a>
                    <a href="#" class="source">Nosotros</a>
                    <a href="#" class="source">FAQs</a>
                    <a href="#" class="source">Contacto</a>
                </div>
                <div class="socialcon">
                    <div class="datatext">
                        "Alimentos preparados con pasión. Servidos con cariño,
                        la auténtica pizza que mereces solo está aquí, somos
                        siempre la mejor elección, pruebanos!"
                    </div>
                    <div class="social">
                        <a href="#" class="socialsource facebook"></a>
                        <a href="#" class="socialsource whatsapp"></a>
                        <a href="#" class="socialsource instagram"></a>
                        <a href="#" class="socialsource tiktok"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>