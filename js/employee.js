import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    const new_employee = document.querySelector("#new_employee");
    get_employees();
    new_employee.addEventListener('click', () => {
        Swal.fire({
            title: "Registrar empleado",
            html: `
                <div class="form-container">
                    <form id="set_new_employee">
                        <div class="oneInput">    
                            <div class="inputContainer" style="background: url(../res/icons/new-user.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="nombre" id="nombre" required>
                                <label for="nombre">Nombre</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/new-user.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="apellido" id="apellido" required>
                                <label for="apellido">Apellido</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/v-card.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="documento" id="documento" required>
                                <label for="documento">Documento</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/phone.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="tel" name="telefono" id="telefono" required>
                                <label for="telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/address.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="direccion" id="direccion" required>
                                <label for="direccion">Dirección</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/email.svg) 5px / 20px no-repeat;">
                                <input class="inputField" type="text" name="email" id="email" required>
                                <label for="email">E-mail</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer con-image" style="justify-content:center;">
                                <input type="file" name="foto_empleado" id="portada" class="form-image">
                                <label for="portada" id="forPortada" class="fore-photo"></label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" value="Registrar" class="send-button">
                        </div>
                    </form>
                </div>
            `,
            showCloseButton: true,
            showCancelButton: false,
            showConfirmButton: false
        });
        activelabel();
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
        document.querySelector("#set_new_employee").addEventListener("submit", async (e) => {
            e.preventDefault();
            const uri = `../${uris.setnewemployee}`;
            const data = new FormData(e.target);
            const sendata = await fetch(uri,{
                method: "POST",
                body: data
            });
            const resp = await sendata.json();
            Swal.fire({
                title: resp.title,
                text: resp.message,
                icon: resp.status
            }).then(()=>{
                get_employees();
            });
        });
    });

    window.opr_action = async (act,id) => {
        const ism = new FormData();
        ism.append("id",id);
        if(act == 'def'){
            Swal.fire({
                title: "Crear acceso al sistema",
                html: `
                    <style>
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
                            padding: 0 40px 0 40px;
                            background-size: 15px;
                        }
                        .showhiddepass {
                            width: 35px;
                            aspect-ratio: 1/1;
                            padding: 0 !important;
                            background: transparent url(../res/icons/show-eye.svg) center / 25px no-repeat;
                            position: absolute;
                            right: 0;
                        }
                        .showhiddepass-active {
                            background: transparent url(../res/icons/hide-eye.svg) center / 25px no-repeat;
                        }
                    </style>
                    <div class="form-container">
                        <form id="newAdmin">
                            <input type="hidden" value="${id}" name="documento">
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/user.svg)">
                                    <select class="inputField" name="rol" id="rol" required>
                                        <option value="operador" selected>Operador</option>
                                        <option value="gestionador">Gestionador</option>
                                        <option value="administrador">Administrador</option>
                                    <select>
                                    <label for="rol" class="active-label">Rol</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/store.svg)">
                                    <select class="inputField" name="sucursal" id="sucursal" required><select>
                                    <label for="sucursal" class="active-label">Sucursal</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/user.svg)">
                                    <input type="text" name="usuario" id="usuario" class="inputField" autocomplete="off" required>
                                    <label for="usuario">Usuario</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/password.svg)">
                                    <span class="showhiddepass"></span>
                                    <input type="password" name="contrasena" id="contrasena" class="inputField" required autocomplete="off">
                                    <label for="contrasena">Contraseña</label>
                                </div>
                            </div>
                            <div class="oneInput">
                                <div class="inputContainer" style="background-image:url(../res/icons/password.svg)">
                                    <span class="showhiddepass"></span>
                                    <input type="password" name="conf-contrasena" id="conf-contrasena" class="inputField" required autocomplete="off">
                                    <label for="conf-contrasena">Confirmar contraseña</label>
                                </div>
                            </div>
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
            document.querySelectorAll(".showhiddepass").forEach(p => {
                let fl = 0;
                let inp = p.nextElementSibling;
                p.addEventListener('click', ()=>{
                    if(fl == 1){
                        p.classList.remove("showhiddepass-active");
                        inp.setAttribute("type","password");
                        fl = 0;
                    }
                    else {
                        p.classList.add("showhiddepass-active");
                        inp.setAttribute("type","text");
                        fl = 1;
                    }
                });
            });
            const urisucs = `../${uris.get_sucursales}`;
            try{
                const consu = await fetch(urisucs);
                if(!consu.ok){throw new Error(`${consu.status} / ${consu.statusText}`)}
                const respu = await consu.json();
                if(respu.status != 'empty'){
                    document.querySelector("#sucursal").innerHTML += respu.message.sucursaleshtml;
                }
            }
            catch (err) {
                console.error(err);
            }
            const newadmin = document.querySelector("#newAdmin");
            newadmin.addEventListener('submit', async (na) => {
                na.preventDefault();
                const urd = `../${uris.setadmindata}`;
                const adata = new FormData(na.target);
                try {
                    const sdt = await fetch(urd,{
                        method: "POST",
                        body: adata
                    });
                    const rpd = await sdt.json();
                    Swal.fire({
                        title: rpd.title,
                        text: rpd.message,
                        icon: rpd.status
                    }).then(()=>{
                        location.reload();
                    });
                }
                catch (err) {
                    console.error(err);
                }
            });
        }
        if(act == 'mod') {
            const urmod = `../${uris.mod_employee}`;
            const md = await fetch(urmod,{
                method: "POST",
                body: ism
            });
            const rpa = await md.json();
            Swal.fire({
                title: "Modificar empleado",
                html: rpa.message,
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false
            });
            if(document.querySelector("#set_mod_employee")){
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
                document.querySelector("#set_mod_employee").addEventListener("submit", async (e) => {
                    e.preventDefault();
                    const uri = `../${uris.set_mod_employee}`;
                    const data = new FormData(e.target);
                    const sendata = await fetch(uri,{
                        method: "POST",
                        body: data
                    });
                    const resp = await sendata.json();
                    Swal.fire({
                        title: resp.title,
                        text: resp.message,
                        icon: resp.status
                    }).then(()=>{
                        get_employees();
                    });
                });
            }
        }
        if(act == 'del'){
            Swal.fire({
                title: "Eliminar",
                text: "Desea eliminar la información del empleado?",
                icon: "question",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then(async (r) => {
                if(r.isConfirmed){
                    const uri = `../${uris.del_employee}`;
                    const data = new FormData();
                    data.append("id",id);
                    const sendata = await fetch(uri,{
                        method: "POST",
                        body: data
                    });
                    const resp = await sendata.json();
                    Swal.fire({
                        title: resp.title,
                        text: resp.message,
                        icon: resp.status
                    }).then(()=>{
                        get_employees();
                    });
                }
            });
        }
    }

});

async function get_employees(){
    const url = `../${uris.get_emps}`;
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