import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    const body = document.querySelector("body");

    if(document.querySelector("#add_ingredient")){
        const addingredient =  document.querySelector("#add_ingredient");
        addingredient.addEventListener("click",()=>{
            Swal.fire({
                title: "Nuevo insumo",
                html: `
                    <div class="from_container">
                        <form id="newIngredient">
                            <input type="text" name="ingrediente" placeholder="Ingrediente">
                            <input type="number" name="stock" id="stock" placeholder="Stock">
                            <input type="number" name="stock_minimo" id="stock_min" placeholder="Stock mínimo">
                            <select name="unidad" id="unidad">
                                <option value="gramo" selected>Gramos</option>
                                <option value="ml">Mililitros</option>
                                <option value="unidad">Unidades</option>
                            </select>
                            <input type="text" name="costo" id="costo" placeholder="Costo">
                            <input type="date" name="vencimiento" placeholder="Vencimiento">
                            <input type="submit" value="Registrar">
                        </form>
                    </div>
                `,
                showCancelButton: false,
                showConfirmButton: false,
                showCloseButton: true
            });
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
                            <div class="from_container">
                                <form id="modIngredient">
                                    <input type="text" name="ingrediente" placeholder="Ingrediente" value="${rta.nombre}">
                                    <input type="number" name="stock" id="stock" placeholder="Stock" value="${rta.stock}">
                                    <input type="number" name="stock_minimo" id="stock_min" placeholder="Stock mínimo" value="${rta.minimo}">
                                    <select name="unidad" id="unidad">
                                        ${rta.unidad_select}
                                    </select>
                                    <input type="text" name="costo" id="costo" placeholder="Costo" value="${rta.costo_total}">
                                    <input type="date" name="vencimiento" placeholder="Vencimiento" value="${rta.vencimiento}">
                                    <input type="hidden" name="id" value="${id}">
                                    <input type="submit" value="Modificar">
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

    window.openIngredientOptions = (id) => {
        Swal.fire({
            title: "Opciones",
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