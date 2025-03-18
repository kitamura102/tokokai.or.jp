dom.event.addEventListener(window, 'load', initDocument);
function initDocument() {
	var buttons = document.getElementsByTagName('BUTTON');
	for(var i=0; i<buttons.length; i++) {
		if(buttons.item(i).className == 'closeBtn') {
			dom.event.addEventListener(buttons.item(i), 'click', closeWindow);
		}
	}
}
function closeWindow() {
	window.close();
}
