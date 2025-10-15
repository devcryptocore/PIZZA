import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded', ()=> {

    get_ingredients();

    if(document.querySelector("#add_ingredient")){
        const addingredient =  document.querySelector("#add_ingredient");
        addingredient.addEventListener("click",()=>{
            Swal.fire({
                title: "Nueva obligación",
                html: `
                    <div class="form-container">
                        <form id="newProduct" enctype="multipart/form-data" novalidate>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="text" name="concepto" class="inputField" id="idcode" required autocomplete="off">
                                    <label for="idcode">Concepto</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/dollar.svg)">
                                    <input type="text" name="valor" id="precio" class="inputField" required autocomplete="off" onkeyup="moneyFormat(this)">
                                    <label for="precio">Valor</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer con-image" style="justify-content:center;">
                                    <input type="file" name="foto_factura" id="portada" class="form-image" required>
                                    <label for="portada" id="forPortada" class="fore-photo"></label>
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
            activelabel();
            if(document.querySelector("#newProduct")){
                const ingredientForm = document.querySelector("#newProduct");
                ingredientForm.addEventListener('submit',async (e) => {
                    e.preventDefault();
                    const url = `../${uris.new_debt}`;
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

    window.debt_details = async (id) => {
        const urid = `../${uris.get_debt}`;
        const idata = new FormData();
        try {
            idata.append("id",id);
            const snd = await fetch(urid,{
                method: "POST",
                body: idata
            });
            const resp = await snd.json();
            let sld = '';
            let canc = '';
            if(resp.message.saldo <= 0){
                sld = 'style="display:none;"';
                canc = 'background: url(../res/images/cancelado.webp) top / 150px no-repeat;';
            }
            Swal.fire({
                title: "Histórico de abonos",
                html: `
                <div class="opscontainer" style="margin-bottom: 10px;${canc}">
                    <button ${sld} class="stkbutton add" onclick="handleDebt('add',${id})">Abonar</button>
                    <button class="stkbutton del" onclick="handleDebt('delete',${id})">Eliminar</button>
                </div>
                <div class="dtcon">
                    <div>
                        <b>Abonado:</b>
                        <span>$${resp.message.abonado}</span>
                    </div>
                    <div>
                        <b>Saldo:</b>
                        <span>$${resp.message.saldo}</span>
                    </div>
                </div>
                <div style="display:flex;width:100%;overflow:auto;">
                    <table class="table-container ingredients_table">
                        <thead>
                            <tr>
                                <th style="font-size:11px;">No.</th>
                                <th style="font-size:11px;">Abono</th>
                                <th style="font-size:11px;">Saldo</th>
                                <th style="font-size:11px;">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="debtbody">
                            ${resp.message.data}
                        </tbody>
                    </table>
                </div>
                `,
                showCancelButton: false,
                showConfirmButton: false,
                showCloseButton: true
            });
        }
        catch (err) {
            console.error(err);
        }
    }

    window.handleDebt = (act,id) => {
        if(act == 'add') {
            Swal.fire({
                title: "Nuevo abono",
                html: `
                    <div class="form-container">
                    <form id="abonoForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/dollar.svg) 5px / 20px no-repeat;">
                                <input type="text" id="valor" name="valor" class="inputField" autocomplete="off" required onkeyup="moneyFormat(this)">
                                <label for="valor">Valor</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <select name="entidad" id="entidad">
                                    <option value="efectivo" selected>Efectivo</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="bancolombia">Bancolombia</option>
                                    <option value="davivienda">Davivienda</option>
                                    <option value="consignacion">Consignación</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <label for="entidad">Fondos</label>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="${id}">
                        <div class="oneInput">
                            <input type="submit" value="Continuar" class="send-button">
                        </div>
                    </form>
                </div>
                `,
                showCancelButton: false,
                showConfirmButton: false,
                showCloseButton: true
            });
            activelabel();
            const abonoForm = document.querySelector("#abonoForm");
            const urim = `../${uris.abono}`;
            abonoForm.addEventListener('submit', async (m) => {
                m.preventDefault();
                try {
                    const datab = new FormData(abonoForm);
                    const ab = await fetch(urim, {
                        method: "POST",
                        body: datab
                    });
                    const resp = await ab.json();
                    Swal.fire({
                        title: resp.title,
                        text: resp.message,
                        icon: resp.status
                    }).then(()=>{
                        debt_details(id);
                        get_ingredients();
                    });
                }
                catch (err) {
                    console.error(err);
                }
            });
        }
        if(act == 'delete') {
            Swal.fire({
                title: "Eliminar obligación?",
                text: "Los abonos aplicados a esta deuda no se eliminarán",
                icon: "question",
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                showCloseButton: false
            }).then(async (res) => {
                if(res.isConfirmed){
                    const urid = `../${uris.delobl}`;
                    try {
                        const datdel = new FormData();
                        datdel.append("id",id);
                        const dels = await fetch(urid, {
                            method: "POST",
                            body: datdel
                        });
                        const respd = await dels.json();
                        Swal.fire({
                            title: respd.title,
                            text: respd.message,
                            icon: respd.status
                        }).then(()=>{
                            get_ingredients();
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                }
                get_ingredients();
            });
        }
    }

});

async function get_ingredients(){
    const url = `../${uris.get_debts}`;
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