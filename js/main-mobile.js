import * as source from '../utils/uripage.js';
document.addEventListener('DOMContentLoaded',()=>{
    AOS.init();
    const wrapper = document.querySelector(".main-section");
    const animatedDiv = document.querySelectorAll(".big-pizza");
    const pizzaTittle = document.querySelector("#pizza_title");
    const btnLeft  = document.querySelector("#ctrl-left");
    const btnRight = document.querySelector("#ctrl-right");
    const pzz = ["p1.png","p2.png","p3.png","p4.png","p2.png","p3.png"];
    let v = Date.now();

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

    let cartstate = 0;
    if(document.querySelector("#my_cart")){
        const mycart = document.querySelector("#my_cart");
        mycart.addEventListener('click',()=>{
            if(cartstate === 0){
                const thecart = document.createElement("div");
                thecart.classList.add("shopping-cart");
                thecart.id = "shopping_cart";
                thecart.setAttribute("data-aos","fade-down");
                thecart.setAttribute("data-aos-offset","0");
                thecart.setAttribute("data-aos-delay","100");
                document.querySelector("body").appendChild(thecart);
                cartstate = 1;
            }
            else {
                document.querySelector("#shopping_cart").classList.add("hiddecart");
                setTimeout(()=>{
                    document.querySelector("#shopping_cart").remove();
                },500);
                cartstate = 0;
            }
        });
    }

    setTimeout(async () => {
        const uri = `${source.getroulette}`;
        try {
            const getdata = await fetch(uri);
            if(!getdata.ok){
                throw new Error(`Error: ${getdata.status} / ${getdata.statusText}`);
            }
            const resdata = await getdata.json();
            if(resdata.status != 'empty' && resdata.message.estado == 1){
                Swal.fire({
                    title: `Participa por ${resdata.message.premio}`,
                    html: `
                        <link rel="stylesheet" href="css/ruleta.css">
                        <div class="ruleta-container">
                            <div class="flecha"></div>
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
                        <p id="resultado"></p>
                    `,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: true
                });
                const ruleta = document.getElementById("ruleta");
                const boton = document.getElementById("girar");
                const resultado = document.getElementById("resultado");
                let angulo = 0;
                const premiada = JSON.parse(resdata.message.premiada);
                boton.addEventListener("click", () => {
                    const extra = Math.floor(Math.random() * 360);
                    angulo += 1800 + extra;
                    ruleta.style.transform = `rotate(${angulo}deg)`;
                    setTimeout(() => {
                        const grados = angulo % 360;
                        let premioIndex = Math.floor((360 - grados) / 60) % 6;
                        const idGanador = "p" + (premioIndex + 1);
                        const nombrePremio = resdata.message.premio;
                        resultado.textContent = "Ganaste: " + nombrePremio;
                        if (premiada.includes(idGanador)) {
                            setTimeout(()=>{
                                Swal.fire({
                                    title: `¡Felicidades! has ganado ${nombrePremio}`,
                                    background: "url(res/images/dancing_pizza.webp) center / cover no-repeat",
                                    width: 600,
                                    html: `
                                        <div class="pizzaWinner"></div>
                                    `,
                                    confirmButtonColor: "#000000ff",
                                    confirmButtonText: "Reclamar mi premio"
                                });
                            },1000);//INTENTOS PARA GIRAR
                        } else {
                            /*Swal.fire({
                                icon: "info",
                                title: "Sigue participando 🍀",
                                text: "Este sector no tiene premio, intenta nuevamente.",
                                confirmButtonColor: "#007bff"
                            });*/
                        }
                    }, 4000);
                });
            }
        }
        catch (err){
            console.error(err);
        }
    },3000);

    const description = {
        "Champiñones": {
            "precio":"35.000",
            "ingredientes":"Queso,harina,champiñones frescos,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Pepperonni": {
            "precio":"45.000",
            "ingredientes":"Queso,harina,pepperonni,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Italiana": {
            "precio":"55.000",
            "ingredientes":"Queso,harina,Pasta,orégano,salsa,harina,Pasta,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar! Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Hawaiana": {
            "precio":"30.000",
            "ingredientes":"Queso,harina,Jamón,Piña,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Pollo": {
            "precio":"35.000",
            "ingredientes":"Queso,harina,Pollo,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Carne": {
            "precio":"45.000",
            "ingredientes":"Queso,harina,Carne,orégano,salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        }
    }
    const nms = Object.keys(description);
    
    animatedDiv.forEach((x, j) => {
        x.style.backgroundImage = `url(res/images/${pzz[j]})`;
    });
    let currentIndex = -1;
    let lastScroll = 0;
    function showTitle(index, direction) {
        const oldB = pizzaTittle.querySelector("b");
        if (oldB) oldB.remove();
        const newB = document.createElement("b");
        const pza = nms[index];
        newB.innerHTML = pza;
        const datos = description[pza];
        document.querySelector(".descontainer").innerHTML = `
            <div class="separator"></div>
            <div class="props" data-aos="" data-aos-offset="0">
                <b data-aos="fade-left" data-aos-offset="0"><span>$</span>${datos.precio}</b>
                <h2 data-aos="fade-up" data-aos-offset="0">Pizza ${pza}</h2>
                <ul id="ingredients" data-aos="fade-right" data-aos-offset="0"></ul>
                <p data-aos="fade-left" data-aos-offset="0">${datos.descripcion}</p>
            </div>
            <div class="separator srotat"></div>
        `;
        let  ings = datos.ingredientes.split(',');
        ings.forEach(ing => {
            document.querySelector("#ingredients").innerHTML += `<li>${ing}</li>`;
        })
        pizzaTittle.appendChild(newB);
        if (direction === "right") {
            newB.classList.add("enter-up");
            if (oldB) oldB.classList.add("exit-up");
        }
        else {
            newB.classList.add("enter-down");
            if (oldB) oldB.classList.add("exit-down");
        }
        if (oldB) {
            setTimeout(() => oldB.remove(), 400);
        }
        AOS.refresh();
    }

    function updateButtons() {
        if (currentIndex <= 0) {
            btnLeft.style.display = "none";
        } else {
            btnLeft.style.display = "block";
        }
        if (currentIndex === animatedDiv.length - 1) {
            btnRight.style.display = "none";
        } else {
            btnRight.style.display = "block";
        }
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

    btnLeft.addEventListener("click", () => {
        scrollToIndex(currentIndex - 1);
    });

    btnRight.addEventListener("click", () => {
        scrollToIndex(currentIndex + 1);
    });

    updateButtons();
    showTitle(0, "right");
    currentIndex = 0;
    wrapper.addEventListener("scroll", () => {
        const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
        const progress = wrapper.scrollLeft / maxScroll;
        animatedDiv.forEach(e => {
            e.style.transform = `rotate(${(progress * 360) * 4}deg)`;
        });
        const direction = wrapper.scrollLeft > lastScroll ? "right" : "left";
        lastScroll = wrapper.scrollLeft;
        const centerX = wrapper.scrollLeft + wrapper.clientWidth / 2;
        animatedDiv.forEach((div, index) => {
            const divStart = div.offsetLeft;
            const divEnd   = divStart + div.offsetWidth;
            if (centerX >= divStart && centerX <= divEnd) {
                if (index !== currentIndex) {
                    currentIndex = index;
                    showTitle(index, direction);
                    updateButtons();
                }
            }
        });
    });

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


})