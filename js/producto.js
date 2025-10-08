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
                        <form id="newProduct" enctype="multipart/form-data" novalidate>
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
                                <div class="inputContainer con-image">
                                    <input type="file" name="portada" id="portada" class="form-image" required>
                                    <label for="portada" id="forPortada" class="fore-photo"></label>
                                    <div class="ot-image">
                                        <input type="file" name="photo1" id="photo1" class="form-image">
                                        <label for="photo1" id="forPhoto1" class="fore-photo"></label>
                                        <input type="file" name="photo2" id="photo2" class="form-image">
                                        <label for="photo2" id="forPhoto2" class="fore-photo"></label>
                                        <input type="file" name="photo3" id="photo3" class="form-image">
                                        <label for="photo3" id="forPhoto3" class="fore-photo"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="tel" name="idcode" class="inputField" id="idcode" minlength="4" maxlength="5" required autocomplete="off">
                                    <label for="idcode">Código</label>
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
                                        <option value="refresco">Refrescos</option>
                                    </select>
                                    <label for="category">Categoría</label>
                                </div>
                            </div>
                            <div class="oneInput" id="ing_select">
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
                            <!--div class="oneInput">
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
                            </div-->
                            <div class="oneInput" id="sizeOfPizza">
                                <div class="inputContainer special-input-cont" style="background-image:url(../res/icons/size.svg)">
                                    <input type="radio" name="size" value="S" id="pizza_S">
                                    <label for="pizza_S">S</label>
                                    <input type="radio" name="size" value="M" id="pizza_M">
                                    <label for="pizza_M">M</label>
                                    <input type="radio" name="size" value="L" id="pizza_L">
                                    <label for="pizza_L">L</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer textarea-container">
                                    <textarea class="prduct-desc" name="descripcion" id="txtarea"></textarea>
                                    <label for="txtarea">Descripción</label>
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
            if(document.querySelector(".form-image")){
                const pimage = document.querySelectorAll(".form-image");
                pimage.forEach(f => {
                    f.addEventListener("change",()=>{
                        let inpid = f.id;
                        let inpimg = f.files[0];
                        if(inpimg && inpimg.type.startsWith("image/")){
                            let bkg = URL.createObjectURL(inpimg);
                            document.querySelectorAll(".fore-photo").forEach(l =>  {
                                let lbl = l.getAttribute("for");
                                if(lbl == inpid){
                                    l.style.background = `url(${bkg}) center / cover no-repeat`;
                                }
                            });
                        }
                        else {
                            iziToast.error({
                                title: "Seleccione un archivo válido!",
                                message: `Debe elegir un archivo en formato .jpg, .png o .webp`,
                                position: "topCenter"
                            });
                        }
                    });
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

    window.handleProduct = (action, id, talla) => {
        let espizza = "";
        const uri = `../${uris.getthisproduct}`;
        const dta = new FormData();
        dta.append("id",id);
        (async () =>{
            try {
                const sdata = await fetch(uri,{
                    method: "POST",
                    body: dta
                });
                const rta = await sdata.json();
                if(action === 'disponible'){
                    if(rta.message.categoria == 'pizza' && talla == 'L'){
                        espizza = `
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/pizza-dark.svg)">
                                    <input type="number" name="porciones" id="porciones" class="inputField" autocomplete="off">
                                    <label for="porciones">Porciones</label>
                                </div>
                            </div>
                        `;
                    }
                    else {
                        espizza = `
                            <div class="numerator">
                                <span class="actioner" id="minus">-</span>
                                <input type="number" id="cantid" required name="cantid" value="1" readonly>
                                <span class="actioner" id="more">+</span>
                            </div>
                        `;
                    }
                    Swal.fire({
                        title: `Activar ${rta.message.producto}`,
                        html: `
                            <form id="moreminusing">
                                ${espizza}
                                <input type="hidden" name="id" value="${id}">
                                <input type="hidden" name="producto" value="${rta.message.producto}">
                                <input type="submit" value="Activar" class="send-button">
                            </form>
                        `,
                        showCancelButton: false,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                    if(document.querySelector("#cantid")){
                        let cantid = document.querySelector("#cantid");
                        let disponibles = document.querySelector(`#disponibles_${rta.message.id}`);
                        document.querySelector("#minus").addEventListener("click",()=>{
                            let vcantid = parseFloat(cantid.value);
                            let ncantid = parseFloat(vcantid - 1);
                            if(vcantid > 1){
                                cantid.value = ncantid;
                            }
                            else {
                                cantid.value = 1;
                            }
                        });
                        document.querySelector("#more").addEventListener("click",()=>{
                            let vcantid = parseFloat(cantid.value);
                            let ncantid = parseFloat(vcantid + 1);
                            if(ncantid <= disponibles.value){
                                cantid.value = ncantid;
                            }
                            else{
                                cantid.value = ncantid-1;
                            }
                        });
                    }
                    const cantidform = document.querySelector("#moreminusing");
                    cantidform.addEventListener('submit',async (x)=>{
                        x.preventDefault();
                        const uric = `../${uris.activethisproduct}`;
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
                        title: `Modificar ${rta.message.producto}`,
                        html: `
                            <div class="form-container">
                                <form id="modProduct" novalidate>
                                    <input type="hidden" name="id" value="${id}">
                                    <div class="oneInput">
                                        <div class="inputContainer" style="background-image:url(../res/icons/meat.svg)">
                                            <input type="text" value="${rta.message.producto}" name="producto" class="inputField" id="producto" required autocomplete="off">
                                            <label for="producto" class="active-label">Producto</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer" style="background-image:url(../res/icons/category.svg)">
                                            <select name="categoria" id="category" class="inputField" required autocomplete="off">
                                                <option class="cat_opt" value="pizza">Pizzas</option>
                                                <option class="cat_opt" value="hamburguesa">Hamburguesas</option>
                                                <option class="cat_opt" value="perrocaliente">Perros calientes</option>
                                                <option class="cat_opt" value="salchipapa">Salchipapas</option>
                                                <option class="cat_opt" value="jugo">Jugos</option>
                                            </select>
                                            <label for="category">Categoría</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer" style="background-image:url(../res/icons/dollar.svg)">
                                            <input type="text" value="${milesjs(rta.message.precio)}" name="precio" id="precio" class="inputField" required autocomplete="off" onkeyup="moneyFormat(this)">
                                            <label for="precio" class="active-label">Precio</label>
                                        </div>
                                    </div>
                                    <div class="oneInput" id="sizeOfPizza">
                                        <div class="inputContainer special-input-cont" style="background-image:url(../res/icons/size.svg)">
                                            <input class="sizeof" type="radio" name="size" value="S" id="pizza_S">
                                            <label for="pizza_S">S</label>
                                            <input class="sizeof" type="radio" name="size" value="M" id="pizza_M">
                                            <label for="pizza_M">M</label>
                                            <input class="sizeof" type="radio" name="size" value="L" id="pizza_L">
                                            <label for="pizza_L">L</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <div class="inputContainer textarea-container">
                                            <textarea class="prduct-desc" name="descripcion" id="txtarea">${rta.message.descripcion}</textarea>
                                            <label for="txtarea">Descripción</label>
                                        </div>
                                    </div>
                                    <div class="oneInput">
                                        <input type="submit" value="Actualizar" class="send-button">
                                    </div>
                                </form>
                            </div>
                        `,
                        showCancelButton: false,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                    if(document.querySelector(".cat_opt")){
                        const catops = document.querySelectorAll(".cat_opt");
                        catops.forEach(c => {
                            if(c.value == rta.message.categoria) {
                                c.setAttribute("selected","");
                            }
                        });
                    }
                    if(document.querySelector(".sizeof")){
                        const szof = document.querySelectorAll(".sizeof");
                        szof.forEach(t => {
                            if(t.value == rta.message.talla) {
                                t.setAttribute("checked","");
                            }
                        });
                    }
                    
                    if(document.querySelector("#modProduct")){
                        const fomrmod = document.querySelector("#modProduct");
                        const urm = `../${uris.modthisproduct}`;
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
                        title: `Eliminar ${rta.message.producto}`,
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
                            const urd = `../${uris.deleteproduct}`;
                            const dldata = new FormData();
                            (async ()=>{
                                dldata.append("id",id);
                                dldata.append("producto",rta.message.producto);
                                dldata.append("categoria",rta.message.categoria);
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

    window.openProductOptions = async (name,id,talla) => {
        Swal.fire({
            title: `Opciones ${name}`,
            html: `
                <div class="offerbtn">
                    <button id="offerbutton" class="stkbutton offer">Oferta</button>
                    <button id="deactivate" class="stkbutton desactivar">Desactivar</button>
                </div>
                <div class="oneInput imageInput">
                    <div class="inputContainer con-image">
                        <input type="file" name="portada" id="portada" class="form-image" required>
                        <label for="portada" id="forPortada" class="fore-photo"></label>
                        <div class="ot-image">
                            <input type="file" name="photo1" id="photo1" class="form-image">
                            <label for="photo1" id="forPhoto1" class="fore-photo"></label>
                            <input type="file" name="photo2" id="photo2" class="form-image">
                            <label for="photo2" id="forPhoto2" class="fore-photo"></label>
                            <input type="file" name="photo3" id="photo3" class="form-image">
                            <label for="photo3" id="forPhoto3" class="fore-photo"></label>
                        </div>
                    </div>
                </div>
                <div class="opscontainer">
                    <button class="stkbutton add" onclick="handleProduct('disponible',${id},'${talla}')">Disponible</button>
                    <button class="stkbutton modify" onclick="handleProduct('modify',${id},'${talla}')">Modificar</button>
                    <button class="stkbutton del" onclick="handleProduct('delete',${id},'${talla}')">Eliminar</button>
                </div>
            `,
            showCancelButton: false,
            showConfirmButton: false,
            showCloseButton: true
        });
        try {
            var disp = true;
            const urip = `../${uris.getthisproduct}`;
            const iddata = new FormData();
            iddata.append("id",id);
            const prdata = await fetch(urip,{
                method: "POST",
                body: iddata
            });
            const rpa = await prdata.json();
            let svaction = "desactivar";
            if(rpa.message.activos.some(can => can.cantidad <= 0)) {
                disp = false;
            }
            else {
                disp = true;
                if(rpa.message.estado == 0){
                    document.querySelector("#deactivate").innerHTML = `Activar`;
                    document.querySelector("#deactivate").classList.remove("desactivar");
                    document.querySelector("#deactivate").classList.add("activar");
                    svaction = "activar";
                }
                else {
                    document.querySelector("#deactivate").innerHTML = `Desactivar`;
                    document.querySelector("#deactivate").classList.remove("activar");
                    document.querySelector("#deactivate").classList.add("desactivar");
                    svaction = "desactivar";
                }
            }
            if(document.querySelector("#deactivate")){
                const deactbt = document.querySelector("#deactivate");
                //deactbt.style.display = "none";
                if(disp) {
                    deactbt.style.display = "block";
                    deactbt.addEventListener("click", async ()=> {
                        const urid = `../${uris.deactivateproduct}&act=${svaction}`;
                        const dsc = new FormData();
                        dsc.append("id",id);
                        dsc.append("val",0);
                        dsc.append("producto",rpa.message.producto);
                        try {
                            const desac = await fetch(urid,{
                                method: "POST",
                                body: dsc
                            });
                            const resdes = await desac.json();
                            Swal.fire({
                                title: resdes.title,
                                text: resdes.message,
                                icon: resdes.status
                            }).then(()=>{
                                if(resdes.status == "error"){
                                    location.reload();
                                }
                                get_ingredients();
                            });
                        }
                        catch (err) {
                            console.error(err);
                        }
                    });
                }
            }
            if(document.querySelector("#offerbutton")){
                const offerbtn = document.querySelector("#offerbutton");
                let ofr = 1;
                offerbtn.innerText = "En oferta";
                if(rpa.message.oferta > 0) {
                    ofr = 0;
                    offerbtn.innerText = "Quitar oferta";
                }
                offerbtn.addEventListener("click", async ()=>{
                    Swal.fire({
                        title: "Producto en oferta",
                        html: `
                            <form id="offer_form" class="small-form">
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/offer-grey.svg)">
                                        <input type="number" required name="porcoffer" id="porcoffer" class="inputField" autocomplete="off">
                                        <label for="porcoffer">Porcentaje</label>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="${id}">
                                <input type="hidden" name="producto" value="${rpa.message.producto}">
                                <input type="submit" value="Confirmar" class="send-button">
                            </form>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        showCloseButton: true
                    });
                    if(document.querySelector("#offer_form")){
                        const form_offer = document.querySelector("#offer_form");
                        form_offer.addEventListener('submit', async (f) => {
                            f.preventDefault();
                            const ouri = `../${uris.offerproduct}`;
                            const dta = new FormData(form_offer);
                            const onoffer = await fetch(ouri,{
                                method: "POST",
                                body: dta
                            });
                            const resoffer = await onoffer.json();
                            Swal.fire({
                                title: resoffer.title,
                                text: resoffer.message,
                                icon: resoffer.status
                            }).then(()=>{
                                if(resoffer.status == "error"){
                                    location.reload();
                                }
                                get_ingredients();
                            });
                        });
                    }
                });
            }
            
            if(document.querySelector("#forPortada")){
                const portada = document.querySelector("#forPortada");
                const ph1 = document.querySelector("#forPhoto1");
                const ph2 = document.querySelector("#forPhoto2");
                const ph3 = document.querySelector("#forPhoto3");
                portada.style.background = `url(${rpa.message.portada}) center / cover no-repeat`;
                ph1.style.background = `url(${rpa.message.foto1}) center / cover no-repeat`;
                ph2.style.background = `url(${rpa.message.foto2}) center / cover no-repeat`;
                ph3.style.background = `url(${rpa.message.foto3}) center / cover no-repeat`;
                const foreimage = document.querySelectorAll(".form-image");
                foreimage.forEach(im => {
                    im.addEventListener("change",async ()=>{
                        const imid = im.id;
                        const imname = im.getAttribute("name");
                        const upimage = `../${uris.prodimagemod}`;
                        const imdata = new FormData();
                        imdata.append("foto",imname);
                        imdata.append("id",id);
                        imdata.append("producto",rpa.message.producto);
                        imdata.append("categoria",rpa.message.categoria);
                        imdata.append(imname, im.files[0]);
                        try {
                            const changeimg = await fetch(upimage,{
                                method: "POST",
                                body: imdata
                            });
                            const respimg = await changeimg.json();
                            if(respimg.status == "success"){
                                openProductOptions(name, id, talla);
                            }
                            else {
                                iziToast.error({
                                    title: respimg.title,
                                    message: `${respimg.message}`,
                                    position: "topCenter"
                                });
                            }
                        }
                        catch (err) {
                            console.error(err);
                        }
                    });
                });
            }
        }
        catch (err) {
            console.error(err);
        }
    }

    if(document.querySelector("#get_barcodes")){
        const gbc = document.querySelector("#get_barcodes");
        gbc.addEventListener('click', async () => {
            const uricb = `../${uris.getbarcodes}`;
            const getcb = await fetch(uricb);
            if(!getcb.ok){
                throw new Error(`Error: ${getcb.status} / ${getcb.statusText}`);
            }
            const rescb = await getcb.json();
            const newwind = window.open("","_blank", "width=1000,height=700,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=no");
            newwind.focus();
            const doc = newwind.document;
            const ncb = doc.createElement('div');
            ncb.innerHTML = `
                <div style="width:100vw;display:flex;flex-wrap:wrap;gap:20px;padding:20px;margin:0;font-family:sans-serif;">
                    ${rescb.message}
                </div>
            `;
            doc.body.appendChild(ncb);
        });
    }

});

async function get_ingredients(){
    const url = `../${uris.getproducts}`;
    //try {
        const ings = await fetch(url);
        if(!ings.ok){
            throw new Error(`Error: ${ings.status} ${ings.statusText}`);
        }
        const res = await ings.json();
        document.querySelector("#ingredients").innerHTML = `${res.message}`;
        gettotal();
    /*}
    catch (err) {
        console.error(`Error: ${err}`);
    }*/
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