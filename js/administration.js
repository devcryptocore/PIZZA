import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded',()=>{

    const roulette = document.querySelector("#set_roulette");

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

});