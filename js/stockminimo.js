import * as uris from './uris.js';
document.addEventListener('DOMContentLoaded',()=>{

    (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.elem-busqueda').hide();
            $('.elem-busqueda').filter(function () {
    	        return ValorBusqueda.test($(this).text());
            }).show();
        })
    }(jQuery));

    (async ()=>{
        const urc = `../${uris.getstockminimo}`;
        try {
            const cad = await fetch(urc);
            if(!cad.ok){throw new Error(`${cad.status} / ${cad.statusText}`)}
            const resp = await cad.json();
            document.querySelector("#ingredients").innerHTML = resp.message.insumos;
        }
        catch (err) {
            console.error(err);
        }
    })();

});