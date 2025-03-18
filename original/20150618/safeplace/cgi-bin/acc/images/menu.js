dom.event.addEventListener(window, 'load', initDocument);
var IMAGE_URL = '';
function initDocument() {
	IMAGE_URL = document.getElementById('IMAGE_URL').value;
	var divs = document.getElementsByTagName('IMG');
	for( var i=0; i<divs.length; i++) {
		var elm = divs.item(i);
		if(elm.className == 'Outline') {
			dom.event.addEventListener(elm, 'click', clickHandler);
		}
	}
}
function clickHandler(e) {
	var srcElement = dom.event.target(e);
	if(! srcElement) { return false; }
	if (srcElement.className == "Outline") {
		targetId = srcElement.id + "d";
		targetElement = document.getElementById(targetId);
		if (targetElement.style.display == "none") {
			targetElement.style.display = "";
			if(targetId != 'Out9-1d') {
				srcElement.src = IMAGE_URL + "/folder_o.gif";
			}
		} else {
			targetElement.style.display = "none";
			if(targetId != 'Out9-1d') {
				srcElement.src = IMAGE_URL + "/folder_c.gif";
			}
		}
	}
}