import * as uris from '../js/uris.js'; 
document.addEventListener('DOMContentLoaded', ()=>{
    const modcompany = document.querySelector("#modCompany");
    const telcamp = document.querySelector("#telcamp");
    const telcamp2 = document.querySelector("#telcamp2");
    const mailcamp = document.querySelector("#mailcamp");
    const dircamp = document.querySelector("#dircamp");
    const nitcamp = document.querySelector("#nitcamp");
    const nomcamp = document.querySelector("#nomcamp");
    const doccamp = document.querySelector("#doccamp");
    const fechcamp = document.querySelector("#fechcamp");
    const comlogo = document.querySelector("#comlogo");
    const company_title = document.querySelector("#company_title");
    const suscs = document.querySelector("#sucursales");
    const fecha = new Date();
    let year = fecha.getFullYear();
    let marker;

    (async ()=>{
        const urc = `../${uris.get_company_data}`;
        try {
            const comp = await fetch(urc);
            if(!comp.ok){throw new Error(`${comp.status} / ${comp.statusText}`);}
            const resp = await comp.json();
            if(resp.status != 'empty') {
                telcamp.innerHTML = resp.message.ptelefono;
                telcamp2.innerHTML = resp.message.stelefono;
                mailcamp.innerHTML = resp.message.email;
                dircamp.innerHTML = resp.message.direccion;
                nitcamp.innerHTML = resp.message.nit;
                nomcamp.innerHTML = resp.message.encargado;
                doccamp.innerHTML = resp.message.documento;
                fechcamp.innerHTML = resp.message.fecha;
                comlogo.src = resp.message.logotipo;
                company_title.innerHTML = resp.message.organizacion;
                let scu = resp.message.sucursales;
                for(const key in scu) {
                    document.querySelector(".sucursales").innerHTML += `
                        <div class="sucursal-square" style="background-image:url('${scu[key].sucfoto}');"
                        onclick="sucursalopt('${key}','${scu[key].sucfoto}','${scu[key].sucdireccion}','${scu[key].suctelefono}','${scu[key].sucubicacion}','${scu[key].id}')">
                            <span>${key}</span>
                        </div>
                    `;
                }
            }
        }
        catch (err) {
            console.error(err);
        }
    })();

    window.sucursalopt = (suc,fot,dir,tel,ubic,id) => {
        let ubc = ubic.split(",");
        let latitud = ubc[0];
        let longitud = ubc[1];
        Swal.fire({
            title: `Sucursal ${suc}`,
            html: `
                <div class="consuc">
                    <img src="${fot}" alt=""/>
                    <ul>
                        <li><b>Dirección: </b>${dir}</li>
                        <li><b>Teléfono: </b>${tel}</li>
                    </ul>
                    <div id="mapa"></div>
                    <div class="btns">
                        <button class="stkbutton" onclick="manageSucursal('mod','${suc}','${fot}','${dir}','${tel}',${id})">Modificar</button>
                        <button class="stkbutton" onclick="manageSucursal('del','${suc}','${fot}','${dir}','${tel}',${id})">Eliminar</button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        var mapa = L.map('mapa').setView([1.608783, -77.132552], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://devcryptocore.github.io">Cryptocore</a>'
        }).addTo(mapa);
        marker = L.marker([latitud, longitud]).addTo(mapa);
    }

    window.manageSucursal = (acc, suc, fot, dir, tel, id) => {
        let urs = `../${uris.set_sucursal}`;
        if(acc == 'del'){
            urs = `../${uris.set_sucursal}&del`;
            const datadel = new FormData(); 
            Swal.fire({
                title: `Eliminar sucursal`,
                text: `Desea eliminar los registros de ${suc}?`,
                icon: "question",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then(async (pregunta) => {
                if(pregunta.isConfirmed){
                    try {
                        datadel.append("id",id);
                        const sucu = await fetch(urs, {
                            method: "POST",
                            body: datadel
                        });
                        const rep = await sucu.json();
                        Swal.fire({
                            title: rep.title,
                            text: rep.message,
                            icon: rep.status
                        }).then(()=>{
                            location.reload();
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                }
            });
        }
        if(acc == 'mod') {
            Swal.fire({
                title: "Modificar sucursal",
                html: `
                    <div class="form-container">
                        <form id="modSucursal" enctype="multipart/form-data" novalidate="">
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="text" name="sucursal" value="${suc}" class="inputField" id="sucursal" required="" autocomplete="off">
                                    <label for="sucursal" class="active-label">Sucursal</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="text" readonly name="ubicacion" class="inputField" id="oldubicacion" required="" autocomplete="off">
                                    <label for="ubicacion" class="active-label">Ubicación</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="text" name="direccion" value="${dir}" class="inputField" id="direccion" required="" autocomplete="off">
                                    <label for="direccion" class="active-label">Dirección</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                    <input type="text" name="telefono" value="${tel}" class="inputField" id="telefono" required="" autocomplete="off">
                                    <label for="telefono" class="active-label">Teléfono</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <input type="hidden" name="id" value="${id}">
                                <input type="hidden" name="old_photo" value="${fot}">
                                <div class="inputContainer con-image" style="justify-content:center;">
                                    <input type="file" name="foto_sucursal" id="foto_sucursal" class="form-image" required="">
                                    <label for="foto_sucursal" class="fore-photo" style="background:url('${fot}') center / cover no-repeat;"></label>
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
            (function(){
                let lat = "";
                let lon = "";
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(mostrarUbicacion, mostrarError);
                } else {
                    console.log("Tu navegador no soporta geolocalización.");
                }
                function mostrarUbicacion(position) {
                    const latitud = position.coords.latitude;
                    const longitud = position.coords.longitude;
                    lat = latitud;
                    lon = longitud;
                    document.querySelector("#oldubicacion").value = `${lat},${lon}`;
                }          
                function mostrarError(error) {
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                        break;
                    case error.POSITION_UNAVAILABLE:
                        console.log("No se pudo determinar la ubicación.");
                        break;
                    case error.TIMEOUT:
                        console.log("La petición de geolocalización se demoró demasiado.");
                        setTimeout(()=>{
                            location.reload();
                        },5000);
                        break;
                    case error.UNKNOWN_ERROR:
                        console.log("Se produjo un error desconocido.");
                        break;
                    }
                }
            })();
            const newsucursal = document.querySelector("#modSucursal");
            urs = `../${uris.set_sucursal}&mod`;
            newsucursal.addEventListener("submit", async (e) => {
                e.preventDefault();
                const data = new FormData(e.target);
                try {
                    const sucu = await fetch(urs, {
                        method: "POST",
                        body: data
                    });
                    const rep = await sucu.json();
                    Swal.fire({
                        title: rep.title,
                        text: rep.message,
                        icon: rep.status
                    }).then(()=>{
                        location.reload();
                    });
                }
                catch (err) {
                    console.error(err);
                }
            });
        }
    }

    window.nosotrostext = async () => {
        const urc = `../${uris.get_company_data}`;
        try {
            const comp = await fetch(urc);
            if(!comp.ok){throw new Error(`${comp.status} / ${comp.statusText}`);}
            const resp = await comp.json();
            if(resp.status != 'empty') {
                if(resp.message.nosotros.length > 0){
                    Swal.fire({
                        title: "Sobre nosotros",
                        html: `
                            <div class="text-container">
                                <p id="nostext">${resp.message.nosotros}</p>
                            </div>
                            <div class="btns">
                                <button class="stkbutton" onclick="oprText()">Modificar</button>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        showCloseButton: true
                    });
                }
                else {
                    Swal.fire({
                        title: "Sobre nosotros",
                        html: `
                        <div class="form-container">
                            <form id="textForm">
                                <div class="oneInput">
                                    <div class="inputContainer textarea-container">
                                        <textarea class="prduct-desc" name="texttopublish" id="txtarea" maxlength="800"></textarea>
                                        <label for="txtarea">Nosotros</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="submit" value="Publicar" class="send-button">
                                </div>
                            </form>
                        </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        showCloseButton: true
                    });
                    const textform = document.querySelector("#textForm");
                    const urn = `../${uris.set_us_info}&type=nosotros`;
                    textform.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const tex = new FormData(e.target);
                        try {
                            const settext = await fetch(urn,{
                                method: "POST",
                                body: tex
                            });
                            const rps = await settext.json();
                            Swal.fire({
                                title: rps.title,
                                text: rps.message,
                                icon: rps.status
                            }).then(()=>{
                                location.reload();
                            });
                        }
                        catch (err) {
                            console.error(err);
                        }
                    });
                }
            }
        }
        catch (err) {
            console.error(err);
        }
    }

    window.faqstext = async (f) => {
        if(f.length > 0){
            Swal.fire({
                title: "FAQs",
                html: `
                    <div class="text-container">
                        <p>${f}</p>
                    </div>
                    <div class="btns">
                        <button class="stkbutton" onclick="oprText('mod','${f}','faqs')">Modificar</button>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
        }
        else {
            Swal.fire({
                title: "FAQs",
                html: `
                <div class="form-container">
                    <form id="textForm">
                        <div class="oneInput">
                            <div class="inputContainer textarea-container">
                                <textarea class="prduct-desc" name="texttopublish" id="txtarea" maxlength="800"></textarea>
                                <label for="txtarea">FAQs</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" value="Publicar" class="send-button">
                        </div>
                    </form>
                </div>
                `,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
            const textform = document.querySelector("#textForm");
            const urn = `../${uris.set_us_info}&type=faqs`;
            textform.addEventListener('submit', async (e) => {
                e.preventDefault();
                const tex = new FormData(e.target);
                try {
                    const settext = await fetch(urn,{
                        method: "POST",
                        body: tex
                    });
                    const rps = await settext.json();
                    Swal.fire({
                        title: rps.title,
                        text: rps.message,
                        icon: rps.status
                    }).then(()=>{
                        location.reload();
                    });
                }
                catch (err) {
                    console.error(err);
                }
            });
        }
    }

    window.oprText = async () => {
        const urc = `../${uris.get_company_data}`;
        const comp = await fetch(urc);
        if(!comp.ok){throw new Error(`${comp.status} / ${comp.statusText}`);}
        const resp = await comp.json();
        Swal.fire({
            title: "Nosotros",
            html: `
            <div class="form-container">
                <form id="textForm">
                    <div class="oneInput">
                        <div class="inputContainer textarea-container">
                            <textarea class="prduct-desc" name="texttopublish" id="txtarea" maxlength="800">${resp.message.nosotros}</textarea>
                            <label for="txtarea" style="text-tramsform:capitalize;">Nosotros</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <input type="submit" value="Publicar" class="send-button">
                    </div>
                </form>
            </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        const textform = document.querySelector("#textForm");
        const urn = `../${uris.set_us_info}&type=nosotros&action=mod`;
        textform.addEventListener('submit', async (e) => {
            e.preventDefault();
            const tex = new FormData(e.target);
            try {
                const settext = await fetch(urn,{
                    method: "POST",
                    body: tex
                });
                const rps = await settext.json();
                Swal.fire({
                    title: rps.title,
                    text: rps.message,
                    icon: rps.status
                }).then(()=>{
                    location.reload();
                });
            }
            catch (err) {
                console.error(err);
            }
        });
    }

    window.modcomp = async () => {
        const uro = `../${uris.orgdata}`;
        try {
            const dat = await fetch(uro);
            if(!dat.ok){throw new Error(`${dat.status} / ${dat.statusText}`);}
            const res = await dat.json();
            if(res.status === 'success') {
                Swal.fire({
                    title: "Modificar datos de la empresa",
                    html: `
                        <style>
                            .form-container {
                                justify-content: flex-start;
                            }
                            .form-container h2 {
                                color: #202020ff;
                                font-size: 2em;
                                margin: 0;
                            }
                            .form-container span {
                                font-size: 12px;
                                color: #5c5c5cff;
                                padding: 0px 40px;
                            }
                            .inputContainer {
                                background-size: 15px;
                            }
                            .con-image {
                                    width: 290px;
                                    padding: 6px;
                                    flex-direction: row;
                                    justify-content: space-between;
                                }
                                .ot-image {
                                    display: flex;
                                    flex-direction: column;
                                }
                            .special-input-cont label,
                            .con-image label {
                                width: 250px;
                                height: 40px;
                                border: 0;
                                background: #00000000;
                                font-size: 16px;
                                color: var(--dark-grey);
                                outline: none;
                                position: relative;
                                align-items: center;
                                display: flex;
                                justify-content: flex-start;
                                transform: translate(0,0);
                                cursor: pointer;
                            }
                            .con-image label, .ot-image label {
                                width: 173px;
                                height: unset;
                                aspect-ratio: 1 / 1;
                                border: 1px solid #1f1f1f;
                                border-radius: 6px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 6px;
                                background: url(../res/icons/image.svg) center / 80px no-repeat;
                            }
                        </style>
                        <div class="form-container">
                            <form id="modOrganization" enctype="multipart/form-data">
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/store.svg)">
                                        <input type="text" value="${res.message.organizacion}" name="organizacion" id="organizacion" class="inputField" required autocomplete="off">
                                        <label for="organizacion" class="active-label">Organización</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/whatsapp-dark.svg)">
                                        <input type="tel" value="${res.message.ptelefono}" name="ptelefono" id="ptelefono" class="inputField" required autocomplete="off">
                                        <label for="ptelefono" class="active-label">Teléfono principal</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/phone.svg)">
                                        <input type="tel" value="${res.message.stelefono}" name="stelefono" id="stelefono" class="inputField" autocomplete="off">
                                        <label for="stelefono" class="active-label">Teléfono secundario (opcional)</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/email.svg)">
                                        <input type="email" value="${res.message.email}" name="email" id="email" class="inputField" required autocomplete="off">
                                        <label for="email" class="active-label">Correo electrónico</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/address.svg)">
                                        <input type="text" value="${res.message.direccion}" name="direccion" id="direccion" class="inputField" required autocomplete="off">
                                        <label for="direccion" class="active-label">Dirección</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/v-card.svg)">
                                        <input type="text" value="${res.message.nit}" name="nit" id="nit" class="inputField" required autocomplete="off">
                                        <label for="nit" class="active-label">NIT</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/user.svg)">
                                        <input type="text" value="${res.message.encargado}" name="encargado" id="encargado" class="inputField" required autocomplete="off">
                                        <label for="encargado" class="active-label">Encargado</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/v-card.svg)">
                                        <input type="text" value="${res.message.documento}" name="documento" id="documento" class="inputField" required autocomplete="off">
                                        <label for="documento" class="active-label">Documento</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="hidden" name="old_logo" value="${res.message.logotipo}" id="oldlogo">
                                    <div class="inputContainer con-image" style="justify-content:center;">
                                        <input style="display:none;" type="file" name="logotipo" id="logotipo" class="form-image" accept="image/*">
                                        <label for="logotipo" id="forLogotipo" class="fore-photo" style="background: url(${res.message.logotipo}) center / cover no-repeat;"></label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="submit" value="Actualizar información" class="send-button">
                                </div>
                            </form>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: false,
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
                if(document.querySelector("#modOrganization")){
                    const neworg = document.querySelector("#modOrganization");
                    const uorg = `../${uris.mod_orgdata}`;
                    neworg.addEventListener("submit", async (e) => {
                        e.preventDefault();
                        const dataorg = new FormData(e.target);
                        try {
                            const org = await fetch(uorg,{
                                method: "POST",
                                body: dataorg
                            });
                            const resp = await org.json();
                            Swal.fire({
                                title: resp.title,
                                text: resp.message,
                                icon: resp.status
                            }).then(()=>{
                                location.reload();
                            });
                        }
                        catch (err) {
                            console.error(err);
                        }
                    });
                }
            }
            else {
                Swal.fire({
                    title: res.title,
                    text: res.message,
                    icon: "error"
                });
            }
        }
        catch (err) {
            console.error(err);
        }
    }

    window.cursals = () => {
        Swal.fire({
            title: "Nueva sucursal",
            html: `
                <div class="form-container">
                    <form id="newSucursal" enctype="multipart/form-data" novalidate="">
                        <div class="oneInput">
                            <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                <input type="text" name="sucursal" class="inputField" id="sucursal" required="" autocomplete="off">
                                <label for="sucursal">Sucursal</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                <input type="text" readonly name="ubicacion" class="inputField" id="ubicacion" required="" autocomplete="off">
                                <label for="ubicacion" class="active-label">Ubicación</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                <input type="text" name="direccion" class="inputField" id="direccion" required="" autocomplete="off">
                                <label for="direccion">Dirección</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background-image:url(../res/icons/barcode.svg)">
                                <input type="text" name="telefono" class="inputField" id="telefono" required="" autocomplete="off">
                                <label for="telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer con-image" style="justify-content:center;">
                                <input type="file" name="foto_sucursal" id="foto_sucursal" class="form-image" required="">
                                <label for="foto_sucursal" class="fore-photo"></label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" value="Guardar" class="send-button">
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
        (function(){
            let lat = "";
            let lon = "";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(mostrarUbicacion, mostrarError);
            } else {
                console.log("Tu navegador no soporta geolocalización.");
            }
            function mostrarUbicacion(position) {
                const latitud = position.coords.latitude;
                const longitud = position.coords.longitude;
                lat = latitud;
                lon = longitud;
                document.querySelector("#ubicacion").value = `${lat},${lon}`;
            }          
            function mostrarError(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.log("No se pudo determinar la ubicación.");
                    break;
                case error.TIMEOUT:
                    console.log("La petición de geolocalización se demoró demasiado.");
                    setTimeout(()=>{
                        location.reload();
                    },5000);
                    break;
                case error.UNKNOWN_ERROR:
                    console.log("Se produjo un error desconocido.");
                    break;
                }
            }
        })();
        const newsucursal = document.querySelector("#newSucursal");
        const urs = `../${uris.set_sucursal}`;
        newsucursal.addEventListener("submit", async (e) => {
            e.preventDefault();
            const data = new FormData(e.target);
            try {
                const sucu = await fetch(urs, {
                    method: "POST",
                    body: data
                });
                const rep = await sucu.json();
                Swal.fire({
                    title: rep.title,
                    text: rep.message,
                    icon: rep.status
                }).then(()=>{
                    location.reload();
                });
            }
            catch (err) {
                console.error(err);
            }
        });
    }

});