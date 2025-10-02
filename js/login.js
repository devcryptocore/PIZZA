import * as uris from '../js/uris.js'; 
document.addEventListener('DOMContentLoaded', ()=>{
    const fecha = new Date();
    let year = fecha.getFullYear();
    if(document.querySelector(".footerSignature")) {
        document.querySelector(".footerSignature").innerHTML = `
            &copy; Cryptocore ${year}
        `;
        document.querySelector(".footerSignature").addEventListener("click",()=>{
            window.open("https://devcryptocore.github.io","_blank");
        });
    }

    if(document.querySelector("#loginForm")){
        const loginform = document.querySelector("#loginForm");
        const uril = `../${uris.login}`;
        loginform.addEventListener('submit', (e)=> {
            e.preventDefault();
            document.querySelector("#sbt").innerHTML = `
                <img src="../res/images/loader2.gif" alt="Pizza" class="pizza_load">
            `;
            setTimeout(async ()=>{
                try {
                    const userdata = new FormData(loginform);
                    const send = await fetch(uril,{
                        method: "POST",
                        body: userdata
                    });
                    const resp = await send.json();
                    Swal.fire({
                        title: resp.title,
                        text: resp.message.text,
                        icon: resp.status,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(()=>{
                        if(resp.status == "success"){
                            location.href = resp.message.source;
                        }
                    });
                }
                catch (err) {
                    console.error(err);
                }
                document.querySelector("#sbt").innerHTML = `
                    <input type="submit" value="Ingresar" class="send-button">
                `;
            },2500);
        })
    }

})