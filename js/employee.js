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
                                <input type="file" name="foto_empleado" id="portada" class="form-image" required>
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