import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded',()=>{

    const roulette = document.querySelector("#set_roulette");
    const catprincipal = document.querySelector("#set_principal");
    const setclean = document.querySelector("#set_clean");

    catprincipal.addEventListener("click", ()=>{
        Swal.fire({
            title: "Establecer categoría principal",
            html: `
                <div class="form-container">
                    <form id="setPrincipal">
                        <div class="oneInput">
                            <div class="inputContainer" style="background-image:url(../res/icons/category.svg)">
                                <select name="categoria" id="category" class="inputField" required autocomplete="off"></select>
                                <label for="category">Categoría principal</label>
                            </div>
                        </div>
                        <div class="oneInput">
                            <input type="submit" value="Establecer" class="send-button">
                        </div>
                    </form>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true
        });
        (async () => {
            const ucat = `../${uris.get_categories}`;
            const cat = await fetch(ucat);
            if(!cat.ok){
                throw new Error(`${cat.status} / ${cat.statusText}`);
            }
            const categ = await cat.json();
            document.querySelector("#category").innerHTML = categ.message;
        })();
        document.querySelector("#setPrincipal").addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = new FormData(e.target);
            const urik = `../${uris.set_principal}`;
            try {
                const rb = await fetch(urik,{
                    method: "POST",
                    body: data
                });
                const rps = await rb.json();
                Swal.fire({
                    title: rps.title,
                    text: rps.message,
                    icon: rps.status
                });
            }
            catch (err) {
                console.error(err);
            }
        })
    });

    roulette.addEventListener('click', async () => {
        const uri = `../${uris.getroulette}`;
        try {
            const getdata = await fetch(uri);
            if(!getdata.ok){
                throw new Error(`Error: ${getdata.status} / ${getdata.statusText}`);
            }
            const resdata = await getdata.json();
            if(resdata.status == 'empty'){
                Swal.fire({
                    title: "Configurar la ruleta",
                    html: `
                        <div class="form-container">
                            <form id="setRoulette">
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio" class="inputField" id="premio" required autocomplete="off">
                                        <label for="premio" class="active-label">Premio</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="number" name="intentos" class="inputField" id="intentos" required autocomplete="off">
                                        <label for="intentos" class="active-label">Intentos</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada1" value="p1" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio1" class="inputField" id="premio1" required autocomplete="off">
                                        <label for="premio1" class="active-label">Premio 1</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada2" value="p2" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio2" class="inputField" id="premio2" required autocomplete="off">
                                        <label for="premio2" class="active-label">Premio 2</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada3" value="p3" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio3" class="inputField" id="premio3" required autocomplete="off">
                                        <label for="premio3" class="active-label">Premio 3</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada4" value="p4" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio4" class="inputField" id="premio4" required autocomplete="off">
                                        <label for="premio4" class="active-label">Premio 4</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada5" value="p5" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio5" class="inputField" id="premio5" required autocomplete="off">
                                        <label for="premio5" class="active-label">Premio 5</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="checkbox" name="premiada6" value="p6" class="chkpremiada">
                                    <div class="inputContainer" style="background-image:url(../res/icons/medal.svg)">
                                        <input type="text" name="premio6" class="inputField" id="premio6" required autocomplete="off">
                                        <label for="premio6" class="active-label">Premio 6</label>
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
                const setroulette = document.querySelector("#setRoulette");
                setroulette.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const urir = `../${uris.set_roulette}`;
                    const dat = new FormData(e.target);
                    try {
                        const rul = await fetch(urir,{
                            method: "POST",
                            body: dat
                        });
                        const ress = await rul.json();
                        Swal.fire({
                            title: ress.title,
                            text: ress.message,
                            icon: ress.status
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                });
            }
            else {
                Swal.fire({
                    title: "Desactivar ruleta?",
                    text: "Toda la información será reestablecida",
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Sí, desactivar",
                    cancelButtonText: "Cancelar"
                });
            }
        }
        catch(err){
            console.error(err);
        }
    });

    setclean.addEventListener('click', () => {
        Swal.fire({
            title: "Reestablecer sistema",
            text: "Está seguro de reestablecer el sistema? esto eliminará todo y no es posible deshacer",
            icon: "question",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "Sí, reestablecer",
            cancelButtonText: "Cancelar",
        }).then(async (conf) => {
            if(conf.isConfirmed) {
                Swal.fire({
                    title: "Ingrese su contraseña de administrador",
                    html: `
                        <div class="form-container">
                            <form id="setClean">
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/password.svg)">
                                        <input type="password" name="contrasena" class="inputField" id="contrasena" required autocomplete="off">
                                        <label for="contrasena">Contraseña</label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <div class="inputContainer" style="background-image:url(../res/icons/admin.svg)">
                                        <input type="number" name="verif" class="inputField" id="verif" required autocomplete="off">
                                        <label for="verif" class="active-label" id="veriff"></label>
                                    </div>
                                </div>
                                <div class="oneInput">
                                    <input type="submit" value="Reestablecer" class="send-button">
                                </div>
                            </form>
                        </div>
                    `,
                    showCancelButton: false,
                    showConfirmButton: false,
                    showCloseButton: true
                });
                let verifnum = Math.floor(Math.random()*(9678-1574+1)+1574);
                const veriflabel = document.querySelector("#veriff");
                veriflabel.innerHTML = `Escriba: <b>${verifnum}</b>`;
                const uridel = `../${uris.set_clean}`;
                const cleanform = document.querySelector("#setClean");
                cleanform.addEventListener('submit', async (c) => {
                    c.preventDefault();
                    const dataclean = new FormData(c.target);
                    try {
                        dataclean.append("gene",verifnum);
                        const clean = await fetch(uridel,{
                            method: "POST",
                            body: dataclean
                        });
                        const reps = await clean.json();
                        Swal.fire({
                            title: reps.title,
                            text: reps.message,
                            icon: reps.status
                        }).then(()=>{
                            location.href = '../php/logout.php';
                        });
                    }
                    catch (err) {
                        console.error(err);
                    }
                });
            }
        });
    });

});