document.addEventListener('DOMContentLoaded', ()=>{
	let v = Date.now();

    if(document.querySelector(".versionized")){
        document.querySelectorAll('.versionized').forEach(ver => {
            let sc = ver.getAttribute("href");
            ver.setAttribute("href",`${sc}?${v}`);
        });
    }

    if(document.querySelector(".versionizedjs")){
        document.querySelectorAll('.versionizedjs').forEach(verjs => {
            let scjs = verjs.getAttribute("src");
            verjs.setAttribute("src",`${scjs}?${v}`);
        });
    }

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

});

function activelabel() {
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
}

function moneyFormat(input){
	var num = input.value.replace(/\./g,'');
	if(!isNaN(num)){
		num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
		num = num.split('').reverse().join('').replace(/^[\.]/,'');
		input.value = num;
	}
	else{ alert('Solo se permiten numeros');
		input.value = input.value.replace(/[^\d\.]*/g,'');
	}
}

function milesjs(mil){
	var mlts = mil.toString();
	var miles = mlts.length;
	if (miles === 4) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 5) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 6) {
		mil = mlts.substr(0, miles -3)+"."+mlts.substr(-3);
	}
	else if (miles === 7) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(1, miles - 4)+"."+mlts.substr(-3);
	}
	else if (miles === 8) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(2, miles - 5)+"."+mlts.substr(-3);
	}
	else if (miles === 9) {
		mil = mlts.substr(0, miles - 6)+","+mlts.substr(3, miles - 6)+"."+mlts.substr(-3);
	}
	return mil;
}

function mesesjs(mes){
    let meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    return meses[mes];
}

async function sys_data() {
	const uro = `../php/mymenu.php?get_sys_data`;
	try {
		const udata = await fetch(uro);
		if(!udata.ok){
			throw new Error(`Error: ${udata.status} / ${udata.statusText}`);
		}
		const resp = await udata.json();
		return resp;
	}
	catch (err) {
		console.error(err);
	}
}

async function get_box_state() {
	const urb = `../php/box.php?constate`;
	try {
		const cos = await fetch(urb);
		if(!cos.ok){
			throw new Error(`ERR: ${cos.status} / ${cos.statusText}`);
		}
		const gets = await cos.json();
		return gets.message;
	}
	catch (err) {
		console.error(err);
	} 
}

function exportarExcel(tabla) {
    tabla = document.querySelector(tabla);
    const wb = XLSX.utils.table_to_book(tabla, { sheet: "Hoja 1" });
    XLSX.writeFile(wb, "datos_exportados.xlsx");
}