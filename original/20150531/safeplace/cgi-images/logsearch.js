dom.event.addEventListener(window, 'load', initDocument);
function initDocument() {
	var modes = document.getElementsByName('MODE')
	if(modes.item(0).checked == false && modes.item(1).checked == false) {
		modes.item(0).checked = true;
	}
	dateOptionChange();
	var inputs = document.getElementsByTagName('INPUT');
	for( var i=0; i<inputs.length; i++) {
		var elm = inputs.item(i);
		if(elm.name == 'DATE') {
			dom.event.addEventListener(elm, 'click', dateOptionChange);
		}
	}
}
function dateOptionChange() {
	var dates = document.getElementsByName('DATE')
	var syear = document.getElementsByName('SYEAR').item(0);
	var eyear = document.getElementsByName('EYEAR').item(0);
	var smon = document.getElementsByName('SMON').item(0);
	var emon = document.getElementsByName('EMON').item(0);
	var sday = document.getElementsByName('SDAY').item(0);
	var eday = document.getElementsByName('EDAY').item(0);
	if(dates.item(0).checked == true) {
		syear.disabled = true;
		syear.style.backgroundColor='#D4D0C8';
		eyear.disabled = true;
		eyear.style.backgroundColor='#D4D0C8';
		smon.disabled = true;
		smon.style.backgroundColor='#D4D0C8';
		emon.disabled = true;
		emon.style.backgroundColor='#D4D0C8';
		sday.disabled = true;
		sday.style.backgroundColor='#D4D0C8';
		eday.disabled = true;
		eday.style.backgroundColor='#D4D0C8';
	} else if(dates.item(1).checked == true) {
		syear.disabled = false;
		syear.style.backgroundColor='';
		eyear.disabled = false;
		eyear.style.backgroundColor='';
		smon.disabled = false;
		smon.style.backgroundColor='';
		emon.disabled = false;
		emon.style.backgroundColor='';
		sday.disabled = false;
		sday.style.backgroundColor='';
		eday.disabled = false;
		eday.style.backgroundColor='';
	} else {
		dates.item(0).checked = true;
		syear.disabled = true;
		syear.style.backgroundColor='#D4D0C8';
		eyear.disabled = true;
		eyear.style.backgroundColor='#D4D0C8';
		smon.disabled = true;
		smon.style.backgroundColor='#D4D0C8';
		emon.disabled = true;
		emon.style.backgroundColor='#D4D0C8';
		sday.disabled = true;
		sday.style.backgroundColor='#D4D0C8';
		eday.disabled = true;
		eday.style.backgroundColor='#D4D0C8';
	}
}
