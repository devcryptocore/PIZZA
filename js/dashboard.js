import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded',()=>{

    const soption = document.querySelectorAll(".source-option");
    const iframe = document.querySelector("#sites_container");
    const logo = document.querySelector('.logo-container');
    const installBtn = document.getElementById('installPWA');
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installBtn.hidden = false;
    });
    installBtn.addEventListener('click', async () => {
        installBtn.hidden = true;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('Usuario instaló la app');
        }
        deferredPrompt = null;
    });

    (async () => {
        const ur = `../${uris.orgdata}`;
        try {
            const cons = await fetch(ur);
            if(!cons.ok){throw new Error(`${cons.status} / ${cons.statusText}`);}
            const rpa = await cons.json();
            if(rpa.status == "success"){
                logo.style.background = `url(${rpa.message.logotipo}) center / 100% no-repeat`;
            }
        }
        catch (err) {
            console.error(err);
        }
    })();

    soption.forEach(b => {
        b.addEventListener('click',async ()=>{
            let source = b.getAttribute("data-source");
            if(source == "logout"){
                Swal.fire({
                    title: "Cerrar sesión",
                    text: "Desea cerrar sesión?",
                    icon: "question",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Sí, salir",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#ff0057",
                    cancelButtonColor: "#008bfd"
                }).then((salir) => {
                    if(salir.isConfirmed){
                        location.href = "../php/logout.php";
                    }
                });
            }
            else if(source == 'acercade'){
                source = 'https://devcryptocore.github.io';
                iframe.setAttribute('src',source);
            }
            else if(source == 'installap'){
                return null;
            }
            else {
                source = `../admin/${source}.php`;
                iframe.setAttribute('src',source);
            }
        });      
    });

    

});