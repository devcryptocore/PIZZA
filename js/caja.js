import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded', () => {

    const set_entidad = document.querySelector("#set_entidad");

    set_entidad.addEventListener('click', async () => {
        Swal.fire({
            title: "Nueva entidad",
            html: `
                <div class="form-container">
                    <form id="entidadForm">
                        <div class="oneInput">
                            <div class="inputContainer" style="background:url(../res/icons/money-dollar.svg) 5px / 20px no-repeat;">
                                <input type="text" name="entidad" id="entidad" required class="inputField">
                                <label for="entidad">Entidad</label>
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

});