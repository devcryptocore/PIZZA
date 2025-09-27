import * as uris from './uris.js';

document.addEventListener("DOMContentLoaded",()=>{

    document.querySelector("#new_employee").addEventListener("submit", async (e) => {
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
            location.reload();
        });
    });

});