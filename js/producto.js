import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    const fecha = new Date();
    const body = document.querySelector("body");
    let ingredients = [];

    (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.elem-busqueda').hide();
            $('.elem-busqueda').filter(function () {
                return ValorBusqueda.test($(this).text());
            }).show();
        })
    }(jQuery));

    if(document.querySelector("#add_ingredient")){
        const addingredient =  document.querySelector("#add_ingredient");
        addingredient.addEventListener("click",()=>{
            Swal.fire({
                title: "Nuevo producto",
                html: `
                    <div class="form-container">
                        <form id="newProduct" novalidate>
                            <div id="chkingredeints" class="chklist">
                                <h2>Ingredientes</h2>
                                <div class="search-container">
                                    <input type="text" id="FiltrarIngredientes" placeholder="Buscar ingrediente" class="search-bar">
                                </div>
                                <div class="selected-ings"></div>
                                <ul id="ingr_list"></ul>
                                <div class="ing-buttons">
                                    <span id="closeIngChk">Integrar</span>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/meat.svg)">
                                    <input type="text" name="producto" class="inputField" id="producto" required autocomplete="off">
                                    <label for="producto">Producto</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/category.svg)">
                                    <select name="categoria" id="category" class="inputField" required autocomplete="off">
                                        <option value="pizza" selected>Pizzas</option>
                                        <option value="hamburguesa">Hamburguesas</option>
                                        <option value="perrocaliente">Perros calientes</option>
                                        <option value="salchipapa">Salchipapas</option>
                                        <option value="jugo">Jugos</option>
                                    </select>
                                    <label for="category">Categoría</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/ingredient.svg)">
                                    <div class="inputField" id="ingredientSelect">
                                        <span>Seleccionar ingredientes</span>
                                    </div>
                                    <input type="hidden" name="ingredients" id="selected_ingredients" value="">
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/dollar.svg)">
                                    <input type="text" name="precio" id="precio" class="inputField" required autocomplete="off" onkeyup="moneyFormat(this)">
                                    <label for="precio">Precio</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer special-input-cont" id="constatus" style="background-image:url(../res/icons/status-active.svg)">
                                    <input type="checkbox" name="estado" id="estado" class="inputField" autocomplete="off" checked>
                                    <label for="estado" class="pstatus active_product">Activo</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer special-input-cont" id="conoffer" style="background-image:url(../res/icons/offer-grey.svg)">
                                    <input type="checkbox" name="oferta" id="oferta" class="inputField" autocomplete="off">
                                    <label for="oferta" class="poffer active_offer">Oferta: No</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <input type="submit" value="Registrar" class="send-button">
                            </div>
                        </form>
                    </div>
                `,
                showCancelButton: false,
                showConfirmButton: false,
                showCloseButton: true
            });
            if(document.querySelector("#estado")){
                document.querySelector("#estado").addEventListener('change',(e)=>{
                    document.querySelector(".pstatus").classList.add("active_product");
                    document.querySelector(".pstatus").innerText = "Activo";
                    document.querySelector("#constatus").style.backgroundImage = 'url(../res/icons/status-active.svg)';
                    if(!document.querySelector("#estado").checked){
                        document.querySelector(".pstatus").classList.remove("active_product");
                        document.querySelector(".pstatus").innerText = "Inactivo";
                        document.querySelector("#constatus").style.backgroundImage = 'url(../res/icons/status-error.svg)';
                    }
                });

                document.querySelector("#oferta").addEventListener('change',(e)=>{
                    document.querySelector(".poffer").classList.add("active_offer");
                    document.querySelector(".poffer").innerText = "Oferta: Si";
                    document.querySelector("#conoffer").style.backgroundImage = 'url(../res/icons/offer-yellow.svg)';
                    if(!document.querySelector("#oferta").checked){
                        document.querySelector(".poffer").classList.remove("active_offer");
                        document.querySelector(".poffer").innerText = "Oferta: No";
                        document.querySelector("#conoffer").style.backgroundImage = 'url(../res/icons/offer-grey.svg)';
                    }
                });
            }
            if(document.querySelector("#ingredientSelect")){
                const ingselect = document.querySelector("#ingredientSelect");
                (async ()=>{
                    const urc = `../${uris.ingredientsforcheck}`;
                    try {
                        const ching = await fetch(urc);
                        if(!ching.ok){
                            throw new Error(`Error en la consulta de datos: ${ching.status} / ${ching.statusText}`);
                        }
                        const rest = await ching.json();
                        document.querySelector("#ingr_list").innerHTML = rest.message;
                        
                        (function($) {
                            $('#FiltrarIngredientes').keyup(function () {
                                var ValorBusqueda = new RegExp($(this).val(), 'i');
                                $('.elem-ingrediente').hide();
                                $('.elem-ingrediente').filter(function () {
                                    return ValorBusqueda.test($(this).text());
                                }).show();
                            })
                        }(jQuery));

                        document.querySelector("#closeIngChk").addEventListener('click',()=>{
                            document.querySelector("#chkingredeints").style.display = "none";
                        });
                        document.querySelectorAll('.chking').forEach(e => {
                            e.addEventListener('change',()=>{
                                if(e.checked){
                                    let ingr = e.getAttribute("data-name");
                                    let ind = e.value;
                                    let iding = ingr.replace(/ /g,"");
                                    $(".selected-ings").append(`<span id="nm_${iding}">${ingr}</span>`);
                                    ingredients.push(ind);
                                    document.querySelector("#selected_ingredients").value = ingredients;
                                    document.querySelector(`#cant_${ind}`).setAttribute("required",true);
                                    document.querySelector(`#nm_${iding}`).addEventListener('click',()=>{
                                        e.checked = false;
                                        $(`#nm_${iding}`).remove();
                                        ingredients = ingredients.filter(f => f !== ind);
                                        document.querySelector("#selected_ingredients").value = ingredients;
                                        document.querySelector(`#cant_${ind}`).setAttribute("required",false);
                                    });
                                }
                                else {
                                    let ingr = e.getAttribute("data-name");
                                    let iding = ingr.replace(/ /g,"");
                                    let ind = e.value;
                                    $(`#nm_${iding}`).remove();
                                    ingredients = ingredients.filter(f => f !== ind);
                                    document.querySelector("#selected_ingredients").value = ingredients;
                                    document.querySelector(`#cant_${ind}`).setAttribute("required",false);
                                }
                            });
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                })();
                ingselect.addEventListener("click",()=>{
                    $(`#chkingredeints`).css("display","flex");
                });
            }
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
            if(document.querySelector("#newProduct")){
                const ingredientForm = document.querySelector("#newProduct");
                ingredientForm.addEventListener('submit',async (e) => {
                    e.preventDefault();
                    let errores = [];
                    document.querySelectorAll(".chking:checked").forEach(chk => {
                        let id = chk.value;
                        let inputcantidad = document.querySelector(`#cant_${id}`);
                        if(!inputcantidad.value || inputcantidad.value <= 0){
                            errores.push(`Falta diligenciar la cantidad para ${chk.getAttribute("data-name")}`);
                        }
                    });
                    if(errores.length > 0){
                        iziToast.error({
                            title: "Campos incompletos!",
                            message: `${errores.join("/")}`,
                            position: "topCenter"
                        });
                    }
                    else {
                        const url = `../${uris.setnewproduct}`;
                        const form = e.target;
                        const data = new FormData(form);
                        //try {
                            const sendata = await fetch(url,{
                                method: "POST",
                                body: data
                            });
                            let response = await sendata.json();
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: response.status
                            }).then(()=>{
                                if(response.status == "error"){
                                    location.reload();
                                }
                                get_ingredients();
                            });
                        /*}
                        catch (error){
                            console.error(`Ha ocurrido un error: ${error}`);
                        }*/
                    }
                });
            }
        })
    }

    get_ingredients();

    window.handleIngredient = (action, id) => {
        const uri = `../${uris.getthisingredient}`;
        const dta = new FormData();
        dta.append("id",id);
        (async () =>{
            try {
                const sdata = await fetch(uri,{
                    method: "POST",
                    body: dta
                });
                const rta = await sdata.json();
                if(action === 'add'){
                    Swal.fire({
                        title: `Stock ${rta.nombre}`,
                        html: `
                            <form id="moreminusing">
                                <div class="numerator">
                                    <span class="actioner" id="minus">-</span>
                                    <input type="number" id="cantid" required name="cantid" value="0">
                                    <span class="actioner" id="more">+</span>
                                </div>
                                <input type="hidden" name="id" value="${id}">
                                <input type="submit" value="Agregar" class="send-button">
                            </form>
                        `,
                        showCancelButton: false,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                    let cantid = document.querySelector("#cantid");
                    const cantidform = document.querySelector("#moreminusing");
                    document.querySelector("#minus").addEventListener("click",()=>{
                        let vcantid = parseFloat(cantid.value);
                        let ncantid = parseFloat(vcantid - 1000.00);
                        if(vcantid > 0){
                            cantid.value = ncantid;
                        }
                    });
                    document.querySelector("#more").addEventListener("click",()=>{
                        let vcantid = parseFloat(cantid.value);
                        let ncantid = parseFloat(vcantid + 1000.00);
                        cantid.value = ncantid;
                    });
                    cantidform.addEventListener('submit',async (x)=>{
                        x.preventDefault();
                        const uric = `../${uris.cantidingredient}`;
                        const can = new FormData(x.target);
                        const scant = await fetch(uric,{
                            method: "POST",
                            body: can
                        });
                        const rssp = await scant.json();
                        Swal.fire({
                            title: rssp.title,
                            text: rssp.message,
                            icon: rssp.status
                        }).then(()=>{
                            if(rssp.status == "error"){
                                location.reload();
                            }
                            get_ingredients();
                        });
                    });
                }
                if(action === 'modify') {
                    Swal.fire({
                        title: `Modificar ${rta.nombre}`,
                        html: `
                            <div class="form-container">
                                <form id="modIngredient">
                                    <input type="hidden" name="id" value="${id}">
                                    <div class="oneInput">
                                        <div class="inputContainer" style="background-image:url(../res/icons/meat.svg)">
                                            <input type="text" name="ingrediente" value="${rta.nombre}" class="inputField active-input-field" id="ingrediente" required autocomplete="off">
                                            <label for="ingrediente">Insumo</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer"  style="background-image:url(../res/icons/weight.svg)">
                                            <input type="number" value="${rta.stock}" name="stock" id="stock" class="inputField active-input-field" required autocomplete="off">
                                            <label for="stock">Cantidad (gr)</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer"  style="background-image:url(../res/icons/weight.svg)">
                                            <input type="number" value="${rta.minimo}" name="stock_minimo" id="stock_min" class="inputField active-input-field" required autocomplete="off">
                                            <label for="stock_min">Mínimo (gr)</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer"  style="background-image:url(../res/icons/pesa.svg)">
                                            <select name="unidad" id="unidad" class="inputField active-input-field" required autocomplete="off">
                                                ${rta.unidad_select}
                                            </select>
                                            <label for="unidad">Medida</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer"  style="background-image:url(../res/icons/dollar.svg)">
                                            <input type="text" value="${milesjs(rta.costo_total)}" name="costo" id="costo" class="inputField active-input-field" required autocomplete="off" onkeyup="moneyFormat(this)">
                                            <label for="costo">Costo</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer"  style="background-image:url(../res/icons/time.svg)">
                                            <input type="date" value="${rta.vencimiento.split(" ")[0]}" name="vencimiento" id="vencimiento" class="inputField" autocomplete="off">
                                            <label for="Vencimiento">F. Vencimiento</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <input type="submit" value="Registrar" class="send-button">
                                    </div>
                                </form>
                            </div>
                        `,
                        showCancelButton: false,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                    if(document.querySelector("#modIngredient")){
                        const fomrmod = document.querySelector("#modIngredient");
                        const urm = `../${uris.modifyingredient}`;
                        fomrmod.addEventListener('submit',async (m)=>{
                            m.preventDefault();
                            const modata = new FormData(m.target);
                            try {
                                const sendmod = await fetch(urm,{
                                    method: "POST",
                                    body: modata
                                });
                                const rmod = await sendmod.json();
                                Swal.fire({
                                    title: rmod.title,
                                    text: rmod.message,
                                    icon: rmod.status
                                }).then(()=>{
                                    if(rmod.status == "error"){
                                        location.reload();
                                    }
                                    get_ingredients();
                                });
                            }
                            catch (err) {
                                console.error(`Error: ${err}`);
                            }
                        });
                    }
                }
                if(action === 'delete') {
                    Swal.fire({
                        title: "Eliminar insumo",
                        text: "Desea eliminar los registros de este elemento?",
                        icon: "question",
                        showCancelButton: true,
                        showConfirmButton: true,
                        cancelButtonText: "Cancelar",
                        confirmButtonText: "Sí, eliminar",
                        cancelButtonColor: "#959595",
                        confirmButtonColor: "#e91e63"
                    }).then((elec) => {
                        if(elec.isConfirmed){
                            const urd = `../${uris.deleteingredient}`;
                            const dldata = new FormData();
                            (async ()=>{
                                dldata.append("id",id);
                                const dlsend = await fetch(urd,{
                                    method: "POST",
                                    body: dldata
                                });
                                const dlres = await dlsend.json();
                                Swal.fire({
                                    title: dlres.title,
                                    text: dlres.message,
                                    icon: dlres.status
                                }).then(()=>{
                                    if(dlres.status == "error"){
                                        location.reload();
                                    }
                                    get_ingredients();
                                });
                            })();

                        }
                    });
                }
            }
            catch (err) {
                console.error(`Error: ${err}`);
            }
        })();
    }

    window.openIngredientOptions = (name,id) => {
        Swal.fire({
            title: `Opciones ${name}`,
            html: `
                <div class="opscontainer">
                    <button class="stkbutton add" onclick="handleIngredient('add',${id})">Disponible</button>
                    <button class="stkbutton modify" onclick="handleIngredient('modify',${id})">Modificar</button>
                    <button class="stkbutton del" onclick="handleIngredient('delete',${id})">Eliminar</button>
                </div>
            `,
            showCancelButton: false,
            showConfirmButton: false,
            showCloseButton: true
        });
    }

});

async function get_ingredients(){
    const url = `../${uris.getproducts}`;
    try {
        const ings = await fetch(url);
        if(!ings.ok){
            throw new Error(`Error: ${ings.status} ${ings.statusText}`);
        }
        const res = await ings.json();
        document.querySelector("#ingredients").innerHTML = `${res.message}`;
        gettotal();
    }
    catch (err) {
        console.error(`Error: ${err}`);
    }
}

async function gettotal() { 
    const urt = `../${uris.gettotal}`;
    try {
        const ttls = await fetch(urt);
        if(!ttls.ok){
            throw new Error(`Error: ${ttls.status} ${ttls.statusText}`);
        }
        const restotal = await ttls.json();
        document.querySelector(".total-container").innerHTML = `<span><b>Total en stock: </b>$${milesjs(restotal.message)}</span>`;
    }
    catch (err) {
        console.error(`Error: ${err}`);
    }
}   