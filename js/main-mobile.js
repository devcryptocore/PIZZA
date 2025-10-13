import * as source from '../utils/uripage.js';
document.addEventListener('DOMContentLoaded',()=>{
    AOS.init();
    const wrapper = document.querySelector(".main-section");
    const pizzaTittle = document.querySelector("#pizza_title");
    const btnLeft  = document.querySelector("#ctrl-left");
    const btnRight = document.querySelector("#ctrl-right");
    const shopingcart = document.querySelector("#shopping_cart");
    const somepr = document.querySelector("#someproducts");
    const installBtn = document.getElementById('installPWA');
    let deferredPrompt;
    let v = Date.now();
    let temps = "session_" + v;
    let cartstate = 0;
    let fechax = new Date();
    let year = fechax.getFullYear();
    const esmovil = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);


    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installBtn.hidden = false;
    });
    installBtn.addEventListener('click', async () => {
        installBtn.hidden = true;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('✅ Usuario instaló la app');
        }
        deferredPrompt = null;
    });

    //localStorage.removeItem('tempses')
    (async ()=>{
        const ts = localStorage.getItem('tempses');
        if(ts === null){
            localStorage.setItem('tempses',temps);
        }
        const smpz = source.someproducts;
        try {
            const pet = await fetch(smpz);
            if(!pet.ok){
                throw new Error(`${pet.status} / ${pet.statusText}`);
            }
            const pres = await pet.json();
            somepr.innerHTML = pres.message;
        }
        catch (err) {
            console.error(err);
        }
    })();

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

    if(document.querySelector(".versionized")){
        document.querySelectorAll('.versionized').forEach(ver => {
            let sc = ver.getAttribute("href");
            ver.setAttribute("href",`${sc}?${v}`);
        });
    }

    if(document.querySelector(".versionizedjs")){
        document.querySelectorAll('.versionizedjs').forEach(verjs => {
            let scjs = verjs.getAttribute("src");
            verjs.setAttribute("src",`${scjs}?${v}`);
        });
    }

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

    setTimeout(async () => {
        const uri = `${source.getroulette}`;
        try {
            const getdata = await fetch(uri);
            if (!getdata.ok) {
                throw new Error(`Error: ${getdata.status} / ${getdata.statusText}`);
            }
            const resdata = await getdata.json();
            if (resdata.status !== "empty" && resdata.message.estado == 1) {
                const uniqid = resdata.message.uniqid;
                const intentosDB = parseInt(resdata.message.intentos) || 1; // Intentos desde la BD
                const keyStorage = `roulette_${uniqid}`;     // Clave única en localStorage
                const savedData = JSON.parse(localStorage.getItem(keyStorage)) || null;
                if (savedData) {
                    if (savedData.gano === true) {
                        console.log("Winner session");
                        return;
                    }
                    if (savedData.intentosRestantes <= 0) {
                        console.log("Out of game");
                        return;
                    }
                } else {
                    localStorage.setItem(
                        keyStorage,
                        JSON.stringify({ intentosRestantes: intentosDB, gano: false })
                    );
                }
                Swal.fire({
                    title: `Participa por ${resdata.message.premio}`,
                    html: `
                        <div class="ruleta-container">
                            <div class="flecha"><img src="res/icons/arrow-red.svg"/></div>
                            <div class="ruleta" id="ruleta">
                                <div class="sector-text sector1" id="p1">${resdata.message.premio1}</div>
                                <div class="sector-text sector2" id="p2">${resdata.message.premio2}</div>
                                <div class="sector-text sector3" id="p3">${resdata.message.premio3}</div>
                                <div class="sector-text sector4" id="p4">${resdata.message.premio4}</div>
                                <div class="sector-text sector5" id="p5">${resdata.message.premio5}</div>
                                <div class="sector-text sector6" id="p6">${resdata.message.premio6}</div>
                            </div>
                        </div>
                        <button id="girar">Girar</button>
                    `,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: true,
                    color: "#fff",
                    background: "#1D2026"
                });
                const ruleta = document.getElementById("ruleta");
                const boton = document.getElementById("girar");
                let angulo = 0;
                const premiada = JSON.parse(resdata.message.premiada);
                boton.addEventListener("click", () => {
                    boton.classList.add("hiddebtn");
                    let data = JSON.parse(localStorage.getItem(keyStorage));
                    if (!data) return;
                    if (data.intentosRestantes <= 0) {
                        Swal.fire({
                            icon: "info",
                            title: "Sin intentos restantes",
                            text: "Ya no puedes girar más esta ruleta.",
                            color: "#fff",
                            background: "#1D2026",
                            confirmButtonColor: "#f9d000"
                        });
                        return;
                    }
                    data.intentosRestantes--;
                    localStorage.setItem(keyStorage, JSON.stringify(data));
                    const extra = Math.floor(Math.random() * 360);
                    angulo += 1800 + extra;
                    ruleta.style.transform = `rotate(${angulo}deg)`;
                    setTimeout(() => {
                        const grados = angulo % 360;
                        let premioIndex = Math.floor((360 - grados) / 60) % 6;
                        const idGanador = "p" + (premioIndex + 1);
                        const nombrePremio = resdata.message.premio;
                        if (premiada.includes(idGanador)) {
                            data.gano = true;
                            localStorage.setItem(keyStorage, JSON.stringify(data));
                            setTimeout(() => {
                                Swal.fire({
                                    title: `¡Felicidades!`,
                                    text: `Has ganado ${nombrePremio}`,
                                    background: "url(res/images/dancing_pizza.webp) center / cover no-repeat",
                                    width: 600,
                                    confirmButtonColor: "#f9d000",
                                    confirmButtonText: "Reclamar mi premio"
                                });
                            }, 1000);
                        } else if (data.intentosRestantes <= 0) {
                            Swal.fire({
                                icon: "info",
                                title: "¡Último intento usado!",
                                text: "Ya no te quedan más giros.",
                                color: "#fff",
                                background: "#1D2026",
                                confirmButtonColor: "#f9d000"
                            });
                        }
                        boton.classList.remove("hiddebtn");
                    }, 4000);
                });
            }
        } catch (err) {
            console.error(err);
        }
    }, 3000);

    function validaimage(ima) {
        if(ima.length == 0 || ima == ''){
            return false;
        }
        else {
            return true;
        }
    }

    (async () => {
        try {
            const urip = `${source.getpizzas}`;
            const response = await fetch(urip);
            if (!response.ok) throw new Error(`Error: ${response.status} / ${response.statusText}`);
            const data = await response.json();
            if (data.status !== "success") throw new Error("Error en datos de pizzas");
            const pzz = data.pzz;
            const description = data.description;
            const nms = Object.keys(description);
            pzz.forEach(e => {
                if(document.querySelector("#mainSect")){
                    document.querySelector("#mainSect").innerHTML += `
                        <section class="img-container">
                            <div>
                                <div class="big-pizza"></div>
                            </div>
                        </section>
                    `;
                }
            });
            const animatedDiv = document.querySelectorAll(".big-pizza");
            animatedDiv.forEach((x, j) => {
                x.style.backgroundImage = `url(${pzz[j]})`;
                x.addEventListener('click', async () => {
                    const produc = description[nms[j]].id;
                    const uip = source.getproductinfo;
                    const pid = new FormData();
                    try {
                        pid.append("idprod",produc);
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
                                    <button class="addbt" onclick="addToCart('${produc}',1)">Agregar al carrito</button>
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
                });
            });
            let currentIndex = -1;
            let lastScroll = 0;
            function showTitle(index, direction) {
                const datadesc = description[nms[index]];
                const oldB = pizzaTittle.querySelector("b");
                if (oldB) oldB.remove();
                const newB = document.createElement("b");
                const pza = nms[index];
                newB.innerHTML = pza;
                const datos = description[pza];
                const container = document.querySelector(".descontainer");
                let talla = datadesc.talla;
                let porciones = datadesc.porciones;
                let preciopizza = talla == 'L' ? `${milesjs(datadesc.precioporcion)}<span class="smlt"> * porción</span>` : milesjs(datos.precio);
                container.innerHTML = `
                    <div class="separator"></div>
                    <div class="props" data-aos="" data-aos-offset="0">
                        <div class="up_scont">
                            <b data-aos="fade-left" data-aos-offset="0"><span>$</span>${preciopizza}</b>
                            <button onclick="addToCart('${datos.id}',1)" class="add_cart_btn" data-aos="fade-right" data-aos-offset="0"></button>
                        </div>
                        <h2 data-aos="fade-up" data-aos-offset="0">${pza}</h2>
                        <ul id="ingredients" data-aos="fade-right" data-aos-offset="0"></ul>
                        <p data-aos="fade-left" data-aos-offset="0">${datos.descripcion}</p>
                    </div>
                    <div class="separator srotat"></div>
                `;
                pizzaTittle.appendChild(newB);
                if (direction === "right") {
                    newB.classList.add("enter-up");
                    if (oldB) oldB.classList.add("exit-up");
                } else {
                    newB.classList.add("enter-down");
                    if (oldB) oldB.classList.add("exit-down");
                }
                if (oldB) setTimeout(() => oldB.remove(), 400);
                AOS.refresh();
            }
            function updateButtons() {
                btnLeft.style.display = currentIndex <= 0 ? "none" : "block";
                btnRight.style.display = currentIndex === animatedDiv.length - 1 ? "none" : "block";
            }
            function scrollToIndex(index) {
                if (index >= 0 && index < animatedDiv.length) {
                    const target = animatedDiv[index];
                    wrapper.scrollTo({
                        left: target.offsetLeft - wrapper.clientWidth / 2 + target.offsetWidth / 2,
                        behavior: "smooth"
                    });
                }
            }
            btnLeft.addEventListener("click", () => scrollToIndex(currentIndex - 1));
            btnRight.addEventListener("click", () => scrollToIndex(currentIndex + 1));
            updateButtons();
            showTitle(0, "right");
            currentIndex = 0;
            wrapper.addEventListener("scroll", () => {
                const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
                const progress = wrapper.scrollLeft / maxScroll;
                animatedDiv.forEach(e => {
                    e.style.transform = `rotate(${(progress * 360)}deg)`;
                });
                const direction = wrapper.scrollLeft > lastScroll ? "right" : "left";
                lastScroll = wrapper.scrollLeft;
                const centerX = wrapper.scrollLeft + wrapper.clientWidth / 2;
                animatedDiv.forEach((div, index) => {
                    const divStart = div.offsetLeft;
                    const divEnd = divStart + div.offsetWidth;
                    if (centerX >= divStart && centerX <= divEnd) {
                        if (index !== currentIndex) {
                            currentIndex = index;
                            showTitle(index, direction);
                            updateButtons();
                        }
                    }
                });
            });

        } catch (error) {
            console.error("Error al cargar las pizzas:", error);
        }
    })();

    (async () => {
        const uricat = `${source.getcategories}`;
        try {
            const cat = await fetch(uricat);
            if (!cat.ok) throw new Error(`Error: ${cat.status} / ${cat.statusText}`);
            const resp = await cat.json();
            if(document.querySelector("#categoriesContainer")){
                document.querySelector("#categoriesContainer").innerHTML = resp.message;
            }
        }
        catch (err) {
            console.error(err);
        }
    })();

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

    window.loadPage = async (page) => {
        location.href = page;
    }

})


function milesjs(mil){
	var mlts = mil.toString();
	var miles = mlts.length;
	if (miles === 4) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 5) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 6) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 7) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(1, miles - 4)+"."+mlts.substr(-3);
	}
	else if (miles === 8) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(2, miles - 5)+"."+mlts.substr(-3);
	}
	else if (miles === 9) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(3, miles - 6)+"."+mlts.substr(-3);
	}
	return mil;
}