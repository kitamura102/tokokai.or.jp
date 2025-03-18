dom.event.addEventListener(window, 'load', initDocument);
function initDocument() {
	dom.event.addEventListener(document.getElementById('AUTHFLAG'), 'change', AuthflagChange);
	dom.event.addEventListener(document.getElementById('URL2PATH_FLAG'), 'change', Url2pathChange);
	dom.event.addEventListener(document.getElementById('LOTATION'), 'change', LotationChange);
	dom.event.addEventListener(document.getElementById('USECOOKIE'), 'change', UsecookieChange);
	dom.event.addEventListener(document.getElementById('LOTATION_SAVE'), 'change', LotationSaveChange);
	var imgs = document.getElementsByTagName('IMG');
	for(var i=0; i<imgs.length; i++) {
		if(imgs.item(i).className == 'help') {
			dom.event.addEventListener(imgs.item(i), 'click', popupHelp);
		}
	}
	AuthflagChange();
	Url2pathChange();
	LotationChange();
	UsecookieChange();
	LotationSaveChange();
}
function popupHelp(e){
	var targetElm = dom.event.target(e);
	var url = 'admin.cgi?action=help&item=' + targetElm.id;
	var w = window.open(url,"help","width=500,height=300,toolbar=no,location=no,status=no,menubar=no,directories=no,scrollbars=yes");
	w.focus();
}
function AuthflagChange() {
	var selectedNum = document.getElementById('AUTHFLAG').selectedIndex;
	var PASSWORD = document.getElementById('PASSWORD');
	if(selectedNum == 1) {
		PASSWORD.disabled = false;
		PASSWORD.style.backgroundColor='';
	} else {
		PASSWORD.disabled = true;
		PASSWORD.style.backgroundColor='#D4D0C8';
	}
}
function Url2pathChange() {
	var selectedNum = document.getElementById('URL2PATH_FLAG').selectedIndex;
	var URL2PATH_URL = document.getElementById('URL2PATH_URL');
	var URL2PATH_PATH = document.getElementById('URL2PATH_PATH');
	if(selectedNum == 1) {
		URL2PATH_URL.disabled = false;
		URL2PATH_URL.style.backgroundColor='';
		URL2PATH_PATH.disabled = false;
		URL2PATH_PATH.style.backgroundColor='';
	} else {
		URL2PATH_URL.disabled = true;
		URL2PATH_URL.style.backgroundColor='#D4D0C8';
		URL2PATH_PATH.disabled = true;
		URL2PATH_PATH.style.backgroundColor='#D4D0C8';
	}
}
function LotationChange() {
	var selectedNum = document.getElementById('LOTATION').selectedIndex;
	var LOTATION_SIZE = document.getElementById('LOTATION_SIZE');
	var LOTATION_SAVE = document.getElementById('LOTATION_SAVE');
	var LOTATION_SAVE_NUM = document.getElementById('LOTATION_SAVE_NUM');
	if(selectedNum == 0) {
		LOTATION_SIZE.disabled = true;
		LOTATION_SIZE.style.backgroundColor='#D4D0C8';
		LOTATION_SAVE.disabled = true;
		LOTATION_SAVE.style.backgroundColor='#D4D0C8';
		LOTATION_SAVE_NUM.disabled = true;
		LOTATION_SAVE_NUM.style.backgroundColor='#D4D0C8';
	} else if(selectedNum == 1) {
		LOTATION_SIZE.disabled = false;
		LOTATION_SIZE.style.backgroundColor='';
		LOTATION_SAVE.disabled = false;
		LOTATION_SAVE.style.backgroundColor='';
		LotationSaveChange();
	} else {
		LOTATION_SIZE.disabled = true;
		LOTATION_SIZE.style.backgroundColor='#D4D0C8';
		LOTATION_SAVE.disabled = false;
		LOTATION_SAVE.style.backgroundColor='';
		LotationSaveChange();
	}
}
function LotationSaveChange() {
	var selectedNum = document.getElementById('LOTATION_SAVE').selectedIndex;
	var LOTATION_SAVE_NUM = document.getElementById('LOTATION_SAVE_NUM');
	if(selectedNum == 2) {
		LOTATION_SAVE_NUM.disabled = false;
		LOTATION_SAVE_NUM.style.backgroundColor='';
	} else {
		LOTATION_SAVE_NUM.disabled = true;
		LOTATION_SAVE_NUM.style.backgroundColor='#D4D0C8';
	}
}
function UsecookieChange() {
	var selectedNum = document.getElementById('USECOOKIE').selectedIndex;
	var EXPIREDAYS = document.getElementById('EXPIREDAYS');
	if(selectedNum == 0) {
		EXPIREDAYS.disabled = true;
		EXPIREDAYS.style.backgroundColor='#D4D0C8';
	} else {
		EXPIREDAYS.disabled = false;
		EXPIREDAYS.style.backgroundColor='';
	}
}
