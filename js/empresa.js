import * as uris from '../js/uris.js'; 
document.addEventListener('DOMContentLoaded', ()=>{
    const modcompany = document.querySelector("#modCompany");
    const fecha = new Date();
    let year = fecha.getFullYear();

    modcompany.addEventListener('click', async () => {
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
                <form id="modOrganization" enctype="multipart/form-data">
                    <h2>Configuración inicial</h2>
                    <span>Felicidades!, usted ha adqurido el nuevo sistema <b>NexFlow Pro v1.0.0</b><br>
                    Por favor, diligencie los datos solicitados a continuación:</span>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/store.svg)">
                            <input type="text" name="organizacion" id="organizacion" class="inputField" required autocomplete="off">
                            <label for="organizacion">Organización</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/whatsapp-dark.svg)">
                            <input type="tel" name="ptelefono" id="ptelefono" class="inputField" required autocomplete="off">
                            <label for="ptelefono">Teléfono principal</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/phone.svg)">
                            <input type="tel" name="stelefono" id="stelefono" class="inputField" autocomplete="off">
                            <label for="stelefono">Teléfono secundario (opcional)</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/email.svg)">
                            <input type="email" name="email" id="email" class="inputField" required autocomplete="off">
                            <label for="email">Correo electrónico</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/address.svg)">
                            <input type="text" name="direccion" id="direccion" class="inputField" required autocomplete="off">
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/v-card.svg)">
                            <input type="text" name="nit" id="nit" class="inputField" required autocomplete="off">
                            <label for="nit">NIT</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/user.svg)">
                            <input type="text" name="encargado" id="encargado" class="inputField" required autocomplete="off">
                            <label for="encargado">Encargado</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer" style="background-image:url(../res/icons/v-card.svg)">
                            <input type="text" name="documento" id="documento" class="inputField" required autocomplete="off">
                            <label for="documento">Documento</label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <div class="inputContainer con-image" style="justify-content:center;">
                            <input style="display:none;" type="file" name="logotipo" id="logotipo" class="form-image" accept="image/*">
                            <label for="logotipo" id="forLogotipo" class="fore-photo"></label>
                        </div>
                    </div>
                    <div class="oneInput">
                        <input type="submit" value="Actualizar información" class="send-button">
                    </div>
                </form>
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
            const uorg = `../${uris.set_orgdata}`;
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
    });

});