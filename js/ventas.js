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
        const ufac = `../${uris.get_my_invoices}`;
        try {
            const req = await fetch(ufac);
            if(!req.ok){
                throw new Error(`Error: ${req.status} / ${req.statusText}`);
            }
            const resp = await req.json();
            Swal.fire({
                title: resp.title,
                html: `
                    <table class="table-container ingredients_table">
                        <thead>
                            <tr>
                                <th>Factura No.</th>
                                <th>ID</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${resp.message}
                        </tbody>
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