import * as uris from './uris.js';
document.addEventListener("DOMContentLoaded",()=>{

    const tabla = document.querySelector("#tablaClientes");

    cargarClientes();

    document.querySelector("#add_ingredient").addEventListener("click",()=>{
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

    // Cargar lista de clientes
    async function cargarClientes() {
        const resp = await fetch(`../${uris.com_client}&action=list`);
        const clientes = await resp.json();
        tabla.innerHTML = clientes.message;
    }

    // Llenar formulario para editar
    window.editarCliente = async (id, nombre, documento, direccion, telefono) => {
        Swal.fire({
            title: "Nuevo cliente",
            html: `
                <div class="form-container">
                    <form id="clienteForm">
                        <input type="hidden" id="id" name="id">
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/user.svg) 5px / 20px no-repeat;">
                                <input type="text" id="nombre" name="nombre" required class="inputField">
                                <label for="nombre" class="active-label">Nombre</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/v-card.svg) 5px / 20px no-repeat;">
                                <input type="text" id="documento" name="documento" required class="inputField">
                                <label for="documento" class="active-label">Documento</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/address.svg) 5px / 20px no-repeat;">
                                <input type="text" id="direccion" name="direccion" class="inputField">
                                <label for="direccion" class="active-label">Dirección</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <div class="inputContainer" style="background: url(../res/icons/phone.svg) 5px / 20px no-repeat;">
                                <input type="text" id="telefono" name="telefono" class="inputField">
                                <label for="telefono" class="active-label">Teléfono</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <button type="submit" class="send-button">Actualizar</button>
                        </div>
                    </form>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        document.getElementById("id").value = id;
        document.getElementById("nombre").value = nombre;
        document.getElementById("documento").value = documento;
        document.getElementById("direccion").value = direccion;
        document.getElementById("telefono").value = telefono;
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
    }

    // Eliminar cliente
    window.eliminarCliente = async (id) => {
        Swal.fire({
            title: "Eliminar cliente",
            text: "Está seguro de eliminar los registros de este cliente?",
            icon: "question"
        }).then(async (conf)=> {
            if(conf.isConfirmed) {
                const dt = new FormData();
                dt.append("id",id);
                const resp = await fetch(`../${uris.com_client}&action=delete`, {
                    method: "POST",
                    body: dt
                });
                const result = await resp.json();
                Swal.fire({
                    title: result.title,
                    text: result.message,
                    icon: result.status
                }).then(()=>{
                    cargarClientes();
                });
            }
        });
    }


});