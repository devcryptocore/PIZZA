document.addEventListener('DOMContentLoaded',()=>{
    AOS.init();
    const wrapper = document.querySelector(".main-section");
    const animatedDiv = document.querySelectorAll(".big-pizza");
    const pizzaTittle = document.querySelector("#pizza_title");
    const pzz = ["p1.png","p2.png","p3.png","p4.png","p2.png","p3.png"];
    const tx  = ["Champiñones", "Pepperonni", "Italiana", "Hawainana", "Pollo", "Carne"];
    const description = {
        "Champiñones": {
            "precio":"35.000",
            "ingredientes":"Queso, harina, champiñones frescos, orégano, salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Pepperonni": {
            "precio":"45.000",
            "ingredientes":"Queso, harina, pepperonni, orégano, salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Italiana": {
            "precio":"55.000",
            "ingredientes":"Queso, harina, Pasta, orégano, salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Hawaiana": {
            "precio":"30.000",
            "ingredientes":"Queso, harina, Jamón, Piña, orégano, salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Pollo": {
            "precio":"35.000",
            "ingredientes":"Queso, harina, Pollo, orégano, salsa",
            "descripcion":"Deliciosa pizza con los mejores sabores que te puedas imaginar!"
        },
        "Carne": {
            "precio":"45.000",
            "ingredientes":"Queso, harina, Carne, orégano, salsa",
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
        document.querySelector(".descript").innerHTML = `
            <h2 data-aos="fade-down" data-aos-offset="0">Pizza ${pza}</h2>
            <b data-aos="fade-right" data-aos-offset="0">$${datos.precio}</b>
            <span data-aos="fade-left" data-aos-offset="0">${datos.ingredientes}</span>
            <p data-aos="fade-up" data-aos-offset="0">${datos.descripcion}</p>
        `;
        AOS.refresh();
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
    }
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
                }
            }
        });
    });

    if(document.querySelector("#burger")){
        const burger = document.querySelector("#burger");
        burger.addEventListener('change',()=>{
            if(burger.checked){
                console.log("MARCADO");
            }
            else {
                console.log("DESMARCADO");
            }
        })
    }


})