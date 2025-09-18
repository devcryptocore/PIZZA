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