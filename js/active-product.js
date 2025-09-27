import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    
    prods();

});
async function prods() {
    const uriprod = `../${uris.makemenu}`;
    try {
        const mimenu = await fetch(uriprod);
        if(!mimenu.ok){
            throw new Error(`Error: ${mimenu.status} / ${mimenu.statusText}`);
        }
        const resp = await mimenu.json();
        if(resp.status == "success"){
            resp.message.forEach(producto => {
                document.querySelector("#myMenu").innerHTML += `
                    <div class="prod-card">
                        <div class="up-to-card">
                            <h3 class="card-title">${producto['producto']}</h3>
                        </div>
                        <div class="prod-image" style="background-image:url(${producto['portada']})"></div>
                        <div class="price-cont">
                            <span>$${milesjs(producto['precio'])}</span>
                        </div>
                        <div class="prod-form">
                            <form id="sell_this">
                                <input type="hidden" name="id" value="${producto['id']}">
                                <div class="counter-cont">
                                    <span class="counter-bt minus">-</span>
                                    <input type="number" name="cantidad" value="1">
                                    <span class="counter-bt more">+</span>
                                </div>
                                <input type="submit" value="Agregar" class="send-button">
                            </form>
                        </div>
                    </div>
                `;
            });
            fitty('.card-title', {
                minSize: 10,
                maxSize: 16
            });
        }
        else {
            document.querySelector("#myMenu").innerHTML = resp.message;
        }
        consCart();
    }
    catch (err) {
        console.error(err);
    }
}

async function consCart() {
    const uric = `../${uris.conscart}`;
    try {
        const consu = await fetch(uric);
        if(!consu.ok){
            throw new Error(`Error: ${consu.status} / ${consu.statusText}`);
        }
        const cresp = await consu.json();
        if(cresp.status == "success"){
            const cart = document.createElement('div');
            cart.id = "my_cart";
            cart.classList.add('the_cart');
            document.body.appendChild(cart);
            if(document.querySelector("#my_cart")){
                const mycart = document.querySelector("#my_cart");
                mycart.innerHTML = `
                    
                        
                            ${cresp.message}
                        
                `;
            }
        }
        else {

        }
    }
    catch (err) {
        console.error(err);
    } 
}