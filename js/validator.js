import * as uris from '../js/uris.js';
window.onload = () =>{
    verify();
}
async function verify(){
    const uriv = `../${uris.ses}`;
    try {
        const cons = await fetch(uriv);
        if(!cons.ok){
            throw new Error(`Error: ${cons.status} / ${cons.statusText}`);
        }
        const resp = await cons.json();
        if(resp.status === 'no_logged'){
            location.href = resp.source;
        }
    }
    catch (err) {
        console.error(err);
    }
}