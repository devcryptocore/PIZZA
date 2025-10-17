<?php
    $version = time();
    $cat = "";
    if(isset($_GET['c'])){
        $cat = $_GET['c'];
        switch ($cat) {
            case 'pizzas':
                $banner = "background: url(res/images/pizzas_banner.webp) center / 100% no-repeat;";
                break;
            case 'hamburguesas':
                $banner = "background: url(res/images/hamburguesas_banner.webp) center / 100% no-repeat;";
                break;
            case 'perros calientes':
                $banner = "background: url(res/images/perros_banner.webp) center / 100% no-repeat;";
                break;
            case 'salchipapas':
                $banner = "background: url(res/images/salchis_banner.webp) center / 100% no-repeat;";
                break;
            case 'pollo broaster':
                $banner = "background: url(res/images/broaster_banner.webp) center / 100% no-repeat;";
                break;
            default:
                $banner = "background: url(res/images/pizzas_banner.webp) center / 100% no-repeat;";
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="res/icons/pizza2.svg" type="image/x-icon">
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
            <h1 >MAX<b>PIZZA</b></h1>
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
    <div class="shopping-cart" id="shopping_cart"></div>
    <div class="banner-cont" style="<?=$banner;?>"></div>
    <h1 id="page-title">Categoría</h1>
    <div class="myIngredients" id="myMenu"></div>
    <input type="hidden" id="category" value="<?=$cat;?>">
    <section class="footer">
        <div class="footersquare" data-aos="fade-down" data-aos-offset="290">
            <div class="btcontent">
                <h3>La pizza está a un clic. ¡Pídela ya!</h3>
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