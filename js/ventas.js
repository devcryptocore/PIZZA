import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded', ()=> {
    const prres = document.querySelector("#prod_result");
    const sbar = document.querySelector("#product_search");
    const field_change = document.querySelector("#field_change");
    const devuelta = document.querySelector("#devuelta");
    const termid = document.querySelector("#terminalId");
    const makesell = document.querySelector("#make_sell");
    const company = document.querySelector("#organization");
    const sucursalname = document.querySelector("#sucursalName");
    const sellername = document.querySelector("#sellerName");
    const selledvalue = document.querySelector("#selledData");
    const client = document.querySelector("#client");
    const doc_client = document.querySelector("#doc_client");
    const clientdata = document.querySelector("#clientData");

    document.addEventListener('keydown', (k)=>{
        k = k || event;
        if(k.ctrlKey && k.keyCode == 32) {
            let wind = window.open(`?terminal=${termid.value}`, "_blank", "width=1000,height=600,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes");
            wind.focus();
        }
    });

    (async ()=>{
        let info = await sys_data();
        company.innerHTML = `<b>Organización: </b><span>Max Pizza</span>`;
        sucursalname.innerHTML = `<b>Sucursal: </b><span>${info.sucursal}</span>`;
        sellername.innerHTML = `<b>Vendedor: </b><span>${info.nombre}</span>`;
        selledvalue.innerHTML = `<b>Vendido: </b><span>$${info.vendido}</span>`;
    })();

    window.setClient = (nom,ced) => {
        client.value = nom;
        doc_client.value = ced;
        clientdata.innerHTML = `
            <b>Cliente:</b>
            <span>${nom}</span>
            <span>${ced}</span>
        `;
        Swal.close();
    }

    const uripr = `../${uris.search_product}`;
    const word = new FormData();
    sbar.addEventListener('keyup', async ()=>{
        let sval = sbar.value;
        if(sval.length > 3){
            word.append("producto",sval);
            try {
                const search = await fetch(uripr,{
                    method: "POST",
                    body: word
                });
                const resp = await search.json();
                prres.classList.add("active-results");
                prres.innerHTML = resp.message;
            }
            catch (err) {
                console.error(err);
            }
        }
        else {
            prres.classList.remove("active-results");
            prres.innerHTML = "";
        }
    });

    field_change.addEventListener('keyup',(c) => {
        moneyFormat(field_change);
        let tt = document.querySelector("#totalVal").value;
        let cval = field_change.value;
        cval = cval.replace(".","");
        if(cval.length >= tt.length && tt > 0 && parseInt(cval) > parseInt(tt)){
            devuelta.innerHTML = `Cambio: $${milesjs(parseInt(cval) - parseInt(tt))}`;
        }
        else {
            devuelta.innerHTML = "Cambio: $0";
        }
    });

    get_added_products();

    makesell.addEventListener("click", async ()=> {
        const uriv = `../${uris.makesell}&terminal=${termid.value}`;
        const datos = new FormData();
        if(field_change.value.length > 0){
            try {
                datos.append("cliente",client.value);
                datos.append("clidoc",doc_client.value);
                datos.append("recibido",field_change.value);
                const venta = await fetch(uriv,{
                    method: "POST",
                    body: datos
                });
                const response = await venta.json();
                if(response.status == "success"){
                    Swal.fire({
                        title: response.title,
                        text: response.message.text,
                        icon: response.status,
                        showCancelButton: false,
                        showConfirmButton: true,
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        denyButtonText: "No"
                    }).then((respuesta)=>{
                        if(respuesta.isConfirmed){//Manejar respuesta para la generación de factura de venta
                            open_bill(response.message.numero);
                            location.reload();
                        }
                        else if(respuesta.isDenied){
                            location.reload();
                        }
                    });
                }
                else {
                    Swal.fire({
                        title: response.title,
                        text: response.message,
                        icon: response.status
                    });
                }
            }
            catch (err) {
                console.error(err);
            }
        }
        else  {
            Swal.fire({
                title: "Datos incompletos!",
                text: "El campo recibido es obligatorio",
                icon: "warning"
            }).then(()=>{
                field_change.focus();
            });
        }
    });

    window.add_product = async (id) => {
        const urdd = `../${uris.add_sell_product}`;
        try {
            const dd = new FormData();
            dd.append("id",id);
            dd.append("terminal",termid.value);
            const sdata = await fetch(urdd,{
                method: "POST",
                body: dd
            });
            const rdata = await sdata.json();
            prres.classList.remove("active-results");
            prres.innerHTML = "";
            sbar.value = "";
            get_added_products();
        }
        catch (err) {
            console.error(err);
        }
    }

    window.changecant = (e, id) => {
        const uric = `../${uris.cant_added_products}`;
        const dat = new FormData();
        if(e.value.length > 0){
            try {
                setTimeout(async ()=>{
                    dat.append("id",id);
                    dat.append("valor",e.value);
                    dat.append("terminal",termid.value);
                    const snd = await fetch(uric,{
                        method: "POST",
                        body: dat
                    });
                    const rs = await snd.json();
                    if(rs.status !== "success"){
                        iziToast.error({
                            title: rs.title,
                            message: `${rs.message}`,
                            position: "topCenter"
                        });
                    }
                    get_added_products();
                },500);
            }
            catch (err) {
                console.error(err);
            }
        }
        else {
            e.value = 1;
        }
    }

    window.deladdedproduct = async (id) => {
        const urid = `../${uris.del_added_products}`;
        const datd = new FormData();
        try {
            datd.append("id",id);
            datd.append("terminal",termid.value);
            const sndd = await fetch(urid,{
                method: "POST",
                body: datd
            });
            const rs = await sndd.json();
            if(rs.status == "success"){
                iziToast.success({
                    title: rs.title,
                    message: `${rs.message}`,
                    position: "topCenter"
                });
            }
            else {
                iziToast.error({
                    title: rs.title,
                    message: `${rs.message}`,
                    position: "topCenter"
                });
            }
            get_added_products();
        }
        catch (err) {
            console.error(err);
        }
    }

    window.open_bill = (numfac,rev = '') => {
        let fact = window.open(`../${uris.getinvoice}${numfac}&${rev}`, "_blank", "width=1000,height=700,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=no");
        fact.focus();
    }

    window.get_invoices = async () => {
        Swal.fire({
            title: "Consultar factura",
            html: `
                <input type="text" id="invoice_search" placeholder="Número de factura" autocomplete="off" style="width: 290px;">
                <button class="send-button" id="factura_search">Buscar</button>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        document.querySelector("#factura_search").addEventListener('click', async ()=> {
            const numfac = document.querySelector("#invoice_search");
            if(!numfac.value.length > 4 || numfac.value == ""){
                iziToast.error({
                    title: "Error!",
                    message: `El número de factura no es correcto!`,
                    position: "topCenter"
                });
                return;
            }
            const ufac = `../${uris.get_my_invoices}`;
            const facnums = new FormData();
            try {
                facnums.append("facnum",numfac.value);
                const req = await fetch(ufac,{
                    method: "POST",
                    body: facnums
                });
                const resp = await req.json();
                if(resp.status == "success"){
                    open_bill(resp.message,'rev');
                    Swal.close();
                }
                else {
                    Swal.fire({
                        title: resp.title,
                        text: resp.message,
                        icon: resp.status
                    });
                }
            }
            catch (err) {
                console.error(err);
            }
        });
    }

    window.rollback = async () => {
        Swal.fire({
            title: "Devolución",
            html: `
                <input type="text" id="rollback_search" placeholder="Código de venta" autocomplete="off" style="width: 290px;">
                <button class="send-button" id="devolucion_dp">Consultar</button>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        document.querySelector("#devolucion_dp").addEventListener('click', async ()=> {
            const codigorb = document.querySelector("#rollback_search");
            if(!codigorb.value.length > 4 || codigorb.value == ""){
                iziToast.error({
                    title: "Error!",
                    message: `El código de devolución no es correcto!`,
                    position: "topCenter"
                });
                return;
            }
            const roll = `../${uris.get_to_rollback}`;
            const idv = new FormData();
            try {
                idv.append("idventa",codigorb.value);
                const reqs = await fetch(roll,{
                    method: "POST",
                    body: idv
                });
                const resp = await reqs.json();
                Swal.fire({
                    title: resp.title,
                    html: `
                    <div style="display:flex;width:100%;overflow:auto;">
                        <table class="table-container ingredients_table">
                            <thead>
                                <tr>
                                    <th style="font-size:11px;">Factura No.</th>
                                    <th style="font-size:11px;">Producto</th>
                                    <th style="font-size:11px;">Cant.</th>
                                    <th style="font-size:11px;">Precio</th>
                                    <th style="font-size:11px;">Subtotal</th>
                                    <th style="font-size:11px;">Dto.</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${resp.message}
                            </tbody>
                        </table>
                    </div>
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
    }

    window.set_rollback = async (id,can,prod) => {
        Swal.fire({
            title: `Devolución de ${prod}`,
            html: `
            <div class="form-container">
                <form id="rollbackForm">
                    <div class="oneInput">
                        <div class="inputContainer">
                            <input type="number" id="cantid" name="cantidad" class="inputField" autocomplete="off" required value="${can}">
                            <label for="cantid" class="active-label">Cantidad</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer textarea-container">
                            <textarea class="prduct-desc" name="motivo" id="txtarea"></textarea>
                            <label for="txtarea">Motivo</label>
                        </div>
                    </div>
                    <input type="hidden" name="venta_id" value="${id}">
                    <div class="oneInput">
                        <input type="submit" value="Devolver" class="send-button">
                    </div>
                </form>
            </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        const rollbackform = document.querySelector("#rollbackForm");
        rollbackform.addEventListener("submit", async (r) => {
            r.preventDefault();
            const urd = `../${uris.devolucion}`;
            const datadev = new FormData(r.target);
            try {
                const sdev = await fetch(urd, {
                    method: "POST",
                    body: datadev
                });
                const rpdev = await sdev.json();
                Swal.fire({
                    title: rpdev.title,
                    text: rpdev.message,
                    icon: rpdev.status
                });
            }
            catch (err) {
                console.error(err);
            }
        });
    }

    window.get_history = async () => {
        const history = `../${uris.history_bill}`;
        try {
            const reqs = await fetch(history);
            if(!reqs.ok){
                throw new Error(`Error: ${reqs.stauts}  / ${reqs.statusText}`);
            }
            const resp = await reqs.json();
            Swal.fire({
                title: resp.title,
                html: `
                <div style="display:flex;width:100%;overflow:auto;">
                    <table class="table-container ingredients_table">
                        <thead>
                            <tr>
                                <th style="font-size:11px;">Factura No.</th>
                                <th style="font-size:11px;">Producto</th>
                                <th style="font-size:11px;">Cant.</th>
                                <th style="font-size:11px;">Precio</th>
                                <th style="font-size:11px;">Subtotal</th>
                                <th style="font-size:11px;">Dto.</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${resp.message}
                        </tbody>
                    </table>
                </div>
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

    window.clients = () => {
        Swal.fire({
            title: "Clientes",
            html: `
                <button id="new_client"></button>
                <input type="text" id="client_search" placeholder="Documento" autocomplete="off" style="width: 290px;">
                <button class="send-button" id="conscli">Buscar</button>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        const conscli = document.querySelector("#conscli");
        const clidoc = document.querySelector("#client_search");
        conscli.addEventListener('click', async () => {
            const cliu = `../${uris.getclient}`;
            const clid = new FormData();
            try {
                clid.append("doc",clidoc.value);
                const cld = await fetch(cliu,{
                    method: "POST",
                    body: clid
                });
                const rescli = await cld.json();
                Swal.fire({
                    title: rescli.title,
                    html: rescli.message,
                    showConfirmButton: false,
                    showCancelButton: false,
                    showCloseButton: true
                });
            }
            catch (err) {
                console.error(err);
            }
        });
        const newclient = document.querySelector("#new_client");
        newclient.addEventListener('click', ()=> {
            Swal.fire({
                title: "Nuevo cliente",
                html: `
                    <div class="form-container">
                        <form id="clienteForm">
                            <input type="hidden" id="id" name="id">
                            <div class="oneInput">
                                <div class="inputContainer" style="background: url(../res/icons/user.svg) 5px / 20px no-repeat;">
                                    <input type="text" id="nombre" name="nombre" required class="inputField">
                                    <label for="nombre">Nombre</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background: url(../res/icons/v-card.svg) 5px / 20px no-repeat;">
                                    <input type="text" id="documento" name="documento" required class="inputField">
                                    <label for="documento">Documento</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background: url(../res/icons/address.svg) 5px / 20px no-repeat;">
                                    <input type="text" id="direccion" name="direccion" class="inputField">
                                    <label for="direccion">Dirección</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background: url(../res/icons/phone.svg) 5px / 20px no-repeat;">
                                    <input type="text" id="telefono" name="telefono" class="inputField">
                                    <label for="telefono">Teléfono</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <button type="submit" class="send-button">Guardar</button>
                            </div>
                        </form>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: false,
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
            const form = document.getElementById("clienteForm");
            form.addEventListener("submit", async (e) => {
                e.preventDefault();
                const clis = new FormData(e.target);
                const resp = await fetch(`../${uris.com_client}&action=save`, {
                    method: "POST",
                    body: clis
                });
                const result = await resp.json();
                Swal.fire({
                    title: result.title,
                    text: result.message,
                    icon: result.status
                }).then(()=>{
                    cargarClientes();
                });
            });
        });
    }

});

async function get_added_products() {
    const termid = document.querySelector("#terminalId");
    const urig = `../${uris.get_added_products}&terminal=${termid.value}`;
    try {
        const cons = await fetch(urig);
        if(!cons.ok){
            throw new Error(`Error: ${cons.status} / ${cons.statusText}`);
        }
        const rpa = await cons.json();
        document.querySelector("#addedProducts").innerHTML = rpa.message.prods;
        document.querySelector("#totalPrice").innerHTML = `$${milesjs(rpa.message.total)}`;
        document.querySelector(".descuento").innerHTML = `Descuento: $${rpa.message.descuento}`;
        document.querySelector("#totalVal").value = rpa.message.total;
        document.querySelectorAll(".prcant").forEach(s => {
            s.addEventListener("focus", ()=> {
                s.select();
            });
        });
    }
    catch (err) {
        console.error(err);
    }
}