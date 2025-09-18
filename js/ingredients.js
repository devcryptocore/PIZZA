import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    const fecha = new Date();
    const body = document.querySelector("body");

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
                title: "Nuevo insumo",
                html: `
                    <div class="form-container">
                        <form id="newIngredient">
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/meat.svg)">
                                    <input type="text" name="ingrediente" class="inputField" id="ingrediente" required autocomplete="off">
                                    <label for="ingrediente">Insumo</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer"  style="background-image:url(../res/icons/weight.svg)">
                                    <input type="number" name="stock" id="stock" class="inputField" required autocomplete="off">
                                    <label for="stock">Cantidad (gr)</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer"  style="background-image:url(../res/icons/weight.svg)">
                                    <input type="number" name="stock_minimo" id="stock_min" class="inputField" required autocomplete="off">
                                    <label for="stock_min">Mínimo (gr)</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer"  style="background-image:url(../res/icons/pesa.svg)">
                                    <select name="unidad" id="unidad" class="inputField" required autocomplete="off">
                                        <option value="gramo" selected>Gramos</option>
                                        <option value="ml">Mililitros</option>
                                        <option value="unidad">Unidades</option>
                                    </select>
                                    <label for="unidad">Medida</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer"  style="background-image:url(../res/icons/dollar.svg)">
                                    <input type="text" name="costo" id="costo" class="inputField" required autocomplete="off" onkeyup="moneyFormat(this)">
                                    <label for="costo">Costo</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer"  style="background-image:url(../res/icons/time.svg)">
                                    <input type="date" name="vencimiento" id="vencimiento" class="inputField" autocomplete="off">
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
            if(document.querySelector("#newIngredient")){
                const ingredientForm = document.querySelector("#newIngredient");
                ingredientForm.addEventListener('submit',async (e) => {
                    e.preventDefault();
                    const url = `../${uris.newingredient}`;
                    const form = e.target;
                    const data = new FormData(form);
                    try {
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
                    }
                    catch (error){
                        console.error(`Ha ocurrido un error: ${error}`);
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
                                <input type="submit" value="Registrar">
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
                    <button class="stkbutton add" onclick="handleIngredient('add',${id})">Agregar</button>
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
    const url = `../${uris.getingredients}`;
    try {
        const ings = await fetch(url);
        if(!ings.ok){
            throw new Error(`Error: ${ings.status} ${ings.statusText}`);
        }
        const res = await ings.json();
        document.querySelector("#ingredients").innerHTML = `${res.message}`;
    }
    catch (err) {
        console.error(`Error: ${err}`);
    }
}