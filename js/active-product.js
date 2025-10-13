import * as source from '../utils/uripage.js';

document.addEventListener("DOMContentLoaded",()=>{

    AOS.init();
    const categ = document.querySelector("#category");
    const pagetitle = document.querySelector("#page-title");
    const shopingcart = document.querySelector("#shopping_cart");
    let cartstate = 0;
    (()=>{
        pagetitle.textContent = categ.value;
    })();
    prods();

    (async ()=>{
        const ts = localStorage.getItem('tempses');
        if(ts === null){
            localStorage.setItem('tempses',temps);
        }
    })();

    function validaimage(ima) {
        if(ima.length == 0 || ima == ''){
            return false;
        }
        else {
            return true;
        }
    }

    window.get_mycart = async () => {
        const mycart = source.getmycart;
        const dta = new FormData();
        try {
            const sesi = localStorage.getItem('tempses') || '';
            dta.append('idsesion',sesi);
            const conscart = await fetch(mycart,{
                method: "POST",
                body: dta
            });
            const rps = await conscart.json();
            if(document.querySelector("#shopping_cart")){
                if(rps.status !== 'empty'){
                    document.querySelector("#shopping_cart").innerHTML = `
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Precio</th>
                                        <th>Sub-Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="myprods">
                                    ${rps.message.products}
                                </tbody>
                            </table>
                        </div>
                        <div class="caroptions">
                            <div class="cartotal">
                                <span>Total: $</span>
                                <span>${rps.message.total}</span>
                            </div>
                            <div class="btcart">
                                <button class="vaciarbtn" onclick="clean_cart()"></button>
                                <button class="checkoutbtn" onclick="checkoutcart()">Realizar pedido</button>
                            </div>
                        </div>
                    `;
                }
                else {
                    document.querySelector("#shopping_cart").innerHTML = rps.message;
                }
            }
        }
        catch (err) {
            console.error(err);
        }
    }

    window.numelems = async () => {
        const mycart = source.getmycart;
        const dta = new FormData();
        try {
            const sesi = localStorage.getItem('tempses') || '';
            dta.append('idsesion',sesi);
            const conscart = await fetch(mycart,{
                method: "POST",
                body: dta
            });
            const rps = await conscart.json();
            if(rps.count > 0){
                document.querySelector("#cartCount").style.display = "flex";
                document.querySelector("#cartCount").textContent = rps.count;
            }
            else {
                document.querySelector("#cartCount").style.display = "none";
            }
        }
        catch (err) {
            console.error(err);
        }
    }
    numelems();

    fitty('#pizza_title', {
        minSize: 20,
        maxSize: 25
    });


    if(document.querySelector('.org-name')){
        document.querySelector('.org-name').addEventListener('click',()=>{
            location.reload();
        });
    }

    let band = 0;
    if(document.querySelector("#my_cart")){
        const mycart = document.querySelector("#my_cart");
        mycart.addEventListener('click', async () => {
            if(cartstate === 0){
                shopingcart.style.display = "flex";
                shopingcart.classList.remove("hidde-shopping-cart");
                cartstate = 1;
                get_mycart();
                const mycart = source.getmycart;
                const dta = new FormData();
                try {
                    const sesi = localStorage.getItem('tempses') || '';
                    dta.append('idsesion',sesi);
                    const conscart = await fetch(mycart,{
                        method: "POST",
                        body: dta
                    });
                    const rps = await conscart.json();
                    get_mycart();
                }
                catch (err) {
                    console.error(err);
                }
            }
            else {
                shopingcart.classList.add("hidde-shopping-cart");
                setTimeout(()=>{
                    shopingcart.style.display = "none";
                },500);
                cartstate = 0;
            }
            document.addEventListener("click", (e) => {
                if (shopingcart && !shopingcart.contains(e.target) && band == 0) {
                    shopingcart.classList.add("hidde-shopping-cart");
                    setTimeout(()=>{
                        shopingcart.style.display = "none";
                    },500);
                    cartstate = 0;
                    band = 1;
                }
            });
            band = 0;
        });
    }

    if(document.querySelector("#burger")){
        const burger = document.querySelector("#burger");
        burger.addEventListener('change',()=>{
            if(burger.checked){
                const modal = document.createElement("div");
                modal.classList.add("modal");
                modal.id = "modal";
                modal.setAttribute("data-aos","fade-right");
                modal.setAttribute("data-aos-offset","0");
                modal.setAttribute("data-aos-delay","500");
                document.querySelector('body').appendChild(modal);
                AOS.refresh();
                if(document.querySelector("#modal")){
                    const mdl = document.querySelector("#modal");
                    mdl.innerHTML = `
                        <button class="modal-option">Inicio</button>
                        <button class="modal-option">Menú</button>
                        <button class="modal-option">Ubicaciones</button>
                        <button class="modal-option">Nosotros</button>
                        <button class="modal-option">FAQs</button>
                        <button class="modal-option">Contacto</button>
                    `;
                }
            }
            else {
                if(document.querySelector("#modal")){
                    const modal = document.querySelector("#modal");
                    modal.classList.add("exit");
                    setTimeout(()=>{
                        modal.remove();
                    },1000);
                }
            }
        })
    }

    window.addToCart = async (id, cant) => {
        const cart = `${source.addtocart}`;
        if(localStorage.getItem('tempses') === null){
            iziToast.error({
                title: "Error!",
                message: `No se ha podido agregar, por favor intente nuevamente!`,
                position: "topCenter",
                onClosed: () => {
                    location.reload();
                }
            });
            return;
        }
        const ses = localStorage.getItem('tempses');
        const data = new FormData();
        try {
            data.append("idsesion",ses);
            data.append("idproducto",id);
            data.append("cantidad",cant);
            const addtocart = await fetch(cart,{
                method: "POST",
                body: data
            });
            const response = await addtocart.json();
            if(response.status == 'success'){
                iziToast.success({
                    title: response.title,
                    position: "topCenter",
                    timeout: 1000
                });
                numelems();
                get_mycart();
            }
            else {
                iziToast.error({
                    title: response.title,
                    message: response.message,
                    position: "topCenter"
                });
                return;
            }
        }
        catch (err) {
            console.error(err);
        } 
    };

    window.removeFromCart = async (id) => {
        const rem = source.delfromcart;
        const dta = new FormData();
        try {
            dta.append('id',id);
            const dl = await fetch(rem,{
                method: "POST",
                body: dta
            });
            const rdl = await dl.json();
            if(rdl.status !== 'success'){
                iziToast.error({
                    title: rdl.title,
                    message: rdl.message,
                    position: "topCenter"
                });
            }
            numelems();
            get_mycart();
        }
        catch (err) {
            console.error(err);
        }
    }

    window.clean_cart = async () => {
        const rem = source.cleancart;
        const dta = new FormData();
        const idsesion = localStorage.getItem('tempses');
        try {
            dta.append('idsesion',idsesion);
            const dl = await fetch(rem,{
                method: "POST",
                body: dta
            });
            const rdl = await dl.json();
            if(rdl.status == 'success'){
                iziToast.success({
                    title: rdl.title,
                    message: rdl.message,
                    position: "topCenter"
                });
                numelems();
                get_mycart();
            }
            else {
                iziToast.error({
                    title: rdl.title,
                    message: rdl.message,
                    position: "topCenter"
                });
            }
        }
        catch (err) {
            console.error(err);
        }
    }

    window.this_product = async (id) => {
        const uip = source.getproductinfo;
        const pid = new FormData();
        try {
            pid.append("idprod",id);
            const inprod = await fetch(uip,{
                method: "POST",
                body: pid
            });
            const rprod = await inprod.json();
            Swal.fire({
                title: rprod.message.producto,
                html: `
                    <div class="infoprod-w">
                        <div class="photos_container">
                            <div class="portada_cont">
                                <img src="${rprod.message.portada}" alt="product img" id="portada_product"/>
                            </div>
                            <div class="otherphotos">
                                <div class="fotocolumns" style="${!validaimage(rprod.message.foto_1) ? 'display:none;' : ''}">
                                    <img  src="${rprod.message.foto_1}" alt="product img" id="foto_product_1"/>
                                </div>
                                <div class="fotocolumns" style="${!validaimage(rprod.message.foto_2) ? 'display:none;' : ''}">
                                    <img src="${rprod.message.foto_2}" alt="product img" id="foto_product_2"/>
                                </div>
                                <div class="fotocolumns" style="${!validaimage(rprod.message.foto_3) ? 'display:none;' : ''}">
                                    <img src="${rprod.message.foto_3}" alt="product img" id="foto_product_3"/>
                                </div>
                            </div>
                        </div>
                        <div class="descript">
                            <h2><b>$</b>${rprod.message.precio}</h2>
                            <span>${rprod.message.descripcion}</span>
                        </div>
                        <button class="addbt" onclick="addToCart('${id}',1)">Agregar al carrito</button>
                    </div>
                `,
                color: "#fff",
                background: "#1D2026",
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false
            });
        }
        catch (err) {
            console.error(err);
        }
    }

    window.checkoutcart = () => {
        const sesi = localStorage.getItem('tempses') || '';
        if(sesi == ''){
            iziToast.error({
                title: "Error",
                text: "No es posible realizar su pedido, por favor recargue la página",
                position: "topCenter"
            });
            return;
        }
        Swal.fire({
            html: `
                <h4>Por favor, diligencie el siguiente formulario</h4>
                <div class="form-container">
                    <form id="checkoutForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(res/icons/user-white.svg) 5px / 20px no-repeat;">
                                <input type="text" id="nombre" name="nombre" required="" class="inputField" autocomplete="off">
                                <label for="nombre">Nombre</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(res/icons/whatsapp-white.svg) 5px / 20px no-repeat;">
                                <input type="text" id="telefono" name="telefono" class="inputField" autocomplete="off">
                                <label for="telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(res/icons/address-white.svg) 5px / 20px no-repeat;">
                                <input type="text" id="direccion" name="direccion" class="inputField" autocomplete="off">
                                <label for="direccion">Dirección</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer textarea-container" style="background: url(res/icons/chat-white.svg) 5px / 20px no-repeat;">
                                <textarea class="prduct-desc" name="comentario" id="txtarea"></textarea>
                                <label for="txtarea">Comentario</label>
                            </div>
                        </div>
                        <input type="hidden" name="coordenadas" value="" id="coords">
                        <input type="hidden" name="idsesion" value="${sesi}">
                        <div class="oneInput">
                            <button type="submit" class="send-button">Realizar pedido</button>
                        </div>
                    </form>
                </div>
            `,
            color: "#fff",
            background: "#1D2026",
            showCloseButton: true,
            showCancelButton: false,
            showConfirmButton: false
        });
        if(document.querySelector(".inputField")){
            const inputs = document.querySelectorAll('.inputField');
            inputs.forEach(inp => {
                inp.addEventListener('focus',()=>{
                    inp.classList.add('active-input-field');
                });
                inp.addEventListener('focusout', ()=>{
                    let inpval = inp.value;
                    if(inpval.length == 0 || inpval == ''){
                        inp.classList.remove('active-input-field');
                    }
                });
            })
        }
        (function(){
            let lat = "";
            let lon = "";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(mostrarUbicacion, mostrarError);
            } else {
                //alert("Tu navegador no soporta geolocalización.");
            }
            function mostrarUbicacion(position) {
                const latitud = position.coords.latitude;
                const longitud = position.coords.longitude;
                lat = latitud;
                lon = longitud;
                document.querySelector("#coords").value = `${lat},${lon}`;
            }          
            function mostrarError(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.log("No se pudo determinar la ubicación.");
                    break;
                case error.TIMEOUT:
                    console.log("La petición de geolocalización se demoró demasiado.");
                    setTimeout(()=>{
                        location.reload();
                    },5000);
                    break;
                case error.UNKNOWN_ERROR:
                    console.log("Se produjo un error desconocido.");
                    break;
                }
            }
            //ua = navigator.userAgent;
        })();

        const checkoutform = document.querySelector("#checkoutForm");
        checkoutform.addEventListener('submit', async (e) => {
            e.preventDefault();
            const uric = source.setcheckout;
            const data = new FormData(e.target);
            try {
                const chk = await fetch(uric,{
                    method: "POST",
                    body: data
                });
                const rpa = await chk.json();
                if(rpa.status == "success"){
                    Swal.fire({
                        title: rpa.title,
                        text: rpa.message.text,
                        icon: rpa.status,
                        color: "#fff",
                        background: "#1D2026",
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(()=>{
                        let wsp = '';
                        let texto = "┗━━━━━━⊱ 𝑵𝒖𝒆𝒗𝒐 𝒑𝒆𝒅𝒊𝒅𝒐 ⊰━━━━━━┛";
                        if(esmovil){
                            wsp =`
                                https://wa.me/+573106574835?text=%0A${texto}%0A%0A➢%20*Nombre:*%20%20_${rpa.message.nombre}_%0A%0A➢%20*Número%20de%20teléfono:*%20%20_${rpa.message.telefono}_%0A%0A➢%20*Dirección:*%20%20_${rpa.message.direccion}_%0A%0A➢%20*Pedido:*%0A${rpa.message.pedido}%0A%0A➢%20*Total:%20$${rpa.message.total}*%0A%0A➢%20*Nota:*%0A_${rpa.message.comentario}_%0A%0A➢%20*Realizado%20el:*%20_${rpa.message.fecha}_%0A%0A%0A%0Aᴾᵒʷᵉʳᵉᵈ ᵇʸ ᶜʳʸᵖᵗᵒᶜᵒʳᵉ
                            `;
                        }
                        else {
                            wsp =`
                                https://web.whatsapp.com/send/?phone=%2B573106574835&text=%0A${texto}%0A%0A➢%20*Nombre:*%20%20_${rpa.message.nombre}_%0A%0A➢%20*Número%20de%20teléfono:*%20%20_${rpa.message.telefono}_%0A%0A➢%20*Dirección:*%20%20_${rpa.message.direccion}_%0A%0A➢%20*Pedido:*%0A${rpa.message.pedido}%0A%0A➢%20*Total:%20$${rpa.message.total}*%0A%0A➢%20*Nota:*%0A_${rpa.message.comentario}_%0A%0A➢%20*Realizado%20el:*%20_${rpa.message.fecha}_%0A%0A%0A%0Aᴾᵒʷᵉʳᵉᵈ ᵇʸ ᶜʳʸᵖᵗᵒᶜᵒʳᵉ&type=phone_number&app_absent=0
                            `;
                        }
                        numelems();
                        get_mycart();
                        window.open(wsp,"_blank");
                        window.focus();
                    });
                }
                else {
                    console.log(rpa);
                }
            }
            catch (err) {
                console.error(err);
            }
        });

    }


});
async function prods() {
    const categ = document.querySelector("#category");
    const uriprod = `${source.makemenu}&c=${categ.value}`;
    try {
        const mimenu = await fetch(uriprod);
        if(!mimenu.ok){
            throw new Error(`Error: ${mimenu.status} / ${mimenu.statusText}`);
        }
        const resp = await mimenu.json();
        if(resp.status == "success"){
            document.querySelector("#myMenu").innerHTML = resp.message;
            fitty('.card-title', {
                minSize: 10,
                maxSize: 16
            });
        }
        else {
            document.querySelector("#myMenu").innerHTML = resp.message;
        }
    }
    catch (err) {
        console.error(err);
    }
}