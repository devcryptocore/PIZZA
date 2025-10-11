import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded', () => {
    const set_box_state = document.querySelector("#set_box_state");
    const set_entidad = document.querySelector("#set_entidad");
    const transfer = document.querySelector("#transfer");
    const movement = document.querySelector("#movement");
    const filtrocaja = document.querySelector("#FiltrarBoxes");
    const buscaja = document.querySelector("#boxsearch");

    getboxes();

    set_box_state.addEventListener('click', async (e) => {
        let situacion = "";
        let boxstate = await get_box_state();
        const urbox = `../${uris.setboxstate}`;
        if(boxstate == 'close') {
            situacion = 'Abrir caja';
            Swal.fire({
                title: situacion,
                html: `
                    <div class="form-container">
                        <form id="boxForm">
                            <div class="oneInput">
                                <div class="inputContainer" style="background:url(../res/icons/dollar.svg) 5px / 20px no-repeat;">
                                    <input type="text" onkeyup="moneyFormat(this)" name="saldobase" id="saldobase" required class="inputField" autocomplete="off">
                                    <label for="saldobase">Saldo base</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                    <select name="entidad" id="metodoPago">
                                        <option value="efectivo" selected>Efectivo</option>
                                        <option value="nequi">Nequi</option>
                                        <option value="daviplata">Daviplata</option>
                                        <option value="bancolombia">Bancolombia</option>
                                        <option value="davivienda">Davivienda</option>
                                        <option value="consignacion">Consignación</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="oneInput">
                                <input type="submit" class="send-button" value="${situacion}">
                            </div>
                        </form>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
            activelabel();
            const boxform = document.querySelector("#boxForm");
            boxform.addEventListener('submit', async (b) => {
                b.preventDefault();
                const monto = new FormData(boxform);
                try {
                    const opbox = await fetch(urbox,{
                        method: "POST",
                        body: monto
                    });
                    const rpbox = await opbox.json();
                    Swal.fire({
                        title: rpbox.title,
                        text: rpbox.message,
                        icon: rpbox.status
                    }).then(()=>{
                        getboxes();
                    });
                }
                catch (err) {
                    console.error(err);
                }
            });
        }
        else {
            situacion = 'Cerrar caja';
            Swal.fire({
                title: situacion,
                text: 'Desea realizar cierre de caja?',
                icon: 'question',
                showConfirmButton: true,
                confirmButtonText: "Sí, continuar",
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#00bd82",
                cancelButtonColor: "#f30053"
            }).then(async (elec) => {
                if(elec.isConfirmed){
                    let action = "cerrar"
                    try {
                        const clbox = new FormData();
                        clbox.append("accion",action);
                        const clos = await fetch(urbox,{
                            method: "POST",
                            body: clbox
                        });
                        const rclose = await clos.json();
                        Swal.fire({
                            title: rclose.title,
                            text: rclose.message,
                            icon: rclose.status
                        }).then(()=>{
                            getboxes();
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                }
            });
        }
        
    });

    set_entidad.addEventListener('click', async () => {
        Swal.fire({
            title: "Inicializar entidades",
            html: `
                <div class="form-container">
                    <form id="entidadForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <select name="entidad" id="metodoPago">
                                    <option value="efectivo" selected>Efectivo</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="bancolombia">Bancolombia</option>
                                    <option value="davivienda">Davivienda</option>
                                    <option value="consignacion">Consignación</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/dollar.svg) 5px / 20px no-repeat;">
                                <input type="text" name="inicial" id="monto" required class="inputField" onkeyup="moneyFormat(this)">
                                <label for="monto">Monto inicial</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" class="send-button" value="Registrar">
                        </div>
                    </form>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        activelabel();
        const formentidad = document.querySelector("#entidadForm");
        const ure = `../${uris.set_entidad}`;
        formentidad.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = new FormData(e.target);
            try {
                const ent = await fetch(ure,{
                    method: "POST",
                    body: data
                });
                const resp = await ent.json();
                Swal.fire({
                    title: resp.title,
                    text: resp.message,
                    icon: resp.status
                }).then(()=>{
                    location.reload();
                });
            }
            catch (er) {
                console.error(er);
            }
        });
        
    });

    transfer.addEventListener('click', ()=>{
        Swal.fire({
            title: "Transferencia de fondos",
            html: `
                <div class="form-container">
                    <form id="transferForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <select name="desde_entidad" id="desde">
                                    <option value="efectivo" selected>Efectivo</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="bancolombia">Bancolombia</option>
                                    <option value="davivienda">Davivienda</option>
                                    <option value="consignacion">Consignación</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <label for="desde">Desde</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <select name="hacia_entidad" id="hacia">
                                    <option value="efectivo" selected>Efectivo</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="bancolombia">Bancolombia</option>
                                    <option value="davivienda">Davivienda</option>
                                    <option value="consignacion">Consignación</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <label for="hacia">Hacia</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/dollar.svg) 5px / 20px no-repeat;">
                                <input type="text" name="monto" id="monto" required class="inputField" onkeyup="moneyFormat(this)">
                                <label for="monto">Monto</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" class="send-button" value="Transferir">
                        </div>
                    </form>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        const transferForm = document.querySelector("#transferForm");
        transferForm.addEventListener('submit', async (t) => {
            t.preventDefault();
            const urit = `../${uris.transfer}`;
            try {
                const dataTransfer = new FormData(transferForm);
                const transf = await fetch(urit,{
                    method: "POST",
                    body: dataTransfer
                });
                const resp = await transf.json();
                Swal.fire({
                    title: resp.title,
                    text: resp.message,
                    icon: resp.status
                }).then(()=>{
                    getboxes();
                });
            }
            catch (err) {
                console.error(err);
            }
        });
    });

    movement.addEventListener('click', ()=>{
        Swal.fire({
            title: "Nuevo movimiento",
            html: `
                <div class="form-container">
                    <form id="movementForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <select name="tipo" id="desde">
                                    <option value="egreso" selected>Gasto</option>
                                    <option value="ingreso">Entrada</option>
                                </select>
                                <label for="desde">Tipo</label>
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
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/dollar.svg) 5px / 20px no-repeat;">
                                <input type="text" name="monto" id="monto" required class="inputField" onkeyup="moneyFormat(this)">
                                <label for="monto">Monto</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/about.svg) 5px / 20px no-repeat;">
                                <input type="text" name="concepto" id="concepto" required class="inputField">
                                <label for="concepto">Concepto</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" class="send-button" value="Registrar">
                        </div>
                    </form>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        activelabel();
        const movementForm = document.querySelector("#movementForm");
        const urim = `../${uris.movement}`;
        movementForm.addEventListener('submit', async (m) => {
            m.preventDefault();
            try {
                const datmov = new FormData(movementForm);
                const mov = await fetch(urim, {
                    method: "POST",
                    body: datmov
                });
                const resp = await mov.json();
                Swal.fire({
                    title: resp.title,
                    text: resp.message,
                    icon: resp.status
                }).then(()=>{
                    getboxes();
                });
            }
            catch (err) {
                console.error(err);
            }
        });
    });

    buscaja.addEventListener('click', async () => {
        const uric = `../${uris.findcaja}`;
        const dts = new FormData();
        try {
            dts.append("fecha",filtrocaja.value);
            const find = await fetch(uric,{
                method: "POST",
                body: dts
            });
            const resp = await find.json();
            Swal.fire({
                title: resp.title,
                html: `
                    <table class="table-container ingredients_table">
                        <thead>
                            <tr>
                                <th>Ventas</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>${resp.message}</tbody>
                    </table>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
        }
        catch (err) {
            console.error(err);
        }
    });

    window.get_details = async (id) => {
        const uric = `../${uris.boxdetails}`;
        const dts = new FormData();
        try {
            dts.append("codcaja",id);
            const find = await fetch(uric,{
                method: "POST",
                body: dts
            });
            const resp = await find.json();
            Swal.fire({
                title: resp.title,
                html: `
                    <div class="dtcon">
                        <div>
                            <b>Ventas:</b>
                            <span>$${resp.message.ventas}</span>
                        </div>
                        <div>
                            <b>Entradas:</b>
                            <span>$${resp.message.ingresos}</span>
                        </div>
                        <div>
                            <b>Gastos:</b>
                            <span>$${resp.message.egresos}</span>
                        </div>
                    </div>
                    <div class="fondosbar">${resp.message.entities}</div>
                    <table class="table-container ingredients_table" id="detable">
                        <thead>
                            <tr>
                                <th style="font-size:12px;">Tipo</th>
                                <th style="font-size:12px;">Concepto</th>
                                <th style="font-size:12px;">Entidad</th>
                                <th style="font-size:12px;">Valor</th>
                                <th style="font-size:12px;">Sucursal</th>
                                <th style="font-size:12px;">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>${resp.message.datatable}</tbody>
                    </table>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
        }
        catch (err) {
            console.error(err);
        }
    }

    window.xls = (tabla) => {
        tabla = document.querySelector(tabla).cloneNode(true);
        tabla.querySelectorAll('td').forEach(td => {
            let texto = td.textContent.trim();
            texto = texto.replace(/[.$]/g,'');
            td.textContent = `${texto}`;
        });
        const wb = XLSX.utils.table_to_book(tabla, { sheet: "Hoja 1" });
        XLSX.writeFile(wb, "datos_exportados.xlsx");
    }

});

async function boxbutton() {
    let boxstate = await get_box_state();
    if(boxstate == 'close') {
        set_box_state.textContent = "Abrir caja";
        set_box_state.classList.add("open_box");
        set_box_state.classList.remove("close_box");
    }
    else {
        set_box_state.textContent = "Cerrar caja";
        set_box_state.classList.remove("open_box");
        set_box_state.classList.add("close_box");
    }
}

async function getfondos() {
    const urs = `../${uris.getfondos}`;
    try {
        const getf = await fetch(urs);
        if(!getf.ok){
            throw new Error(`Error: ${getf.status} / ${getf.statusText}`);
        }
        const reps = await getf.json();
        document.querySelector(".fondosbar").innerHTML = reps.message;
    }
    catch (err) {
        console.error(err);
    }
}

async function getboxes() {
    boxbutton();
    getfondos();
    const urb = `../${uris.getboxstates}`;
    try {
        const cons = await fetch(urb);
        if(!cons.ok){
            throw new Error(`Error: ${cons.status} / ${cons.statusText}`);
        }
        const rpa = await cons.json();
        document.querySelector("#ingredients").innerHTML = rpa.message;
    }
    catch (err){
        console.error(`ERROR: ${err}`);
    }
}