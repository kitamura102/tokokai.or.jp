dom.event.addEventListener(window, 'load', initDocument);
function initDocument() {
	var inputs = document.getElementsByTagName('INPUT');
	for(var i=0; i<inputs.length; i++) {
		if(inputs.item(i).type == 'submit') {
			inputs.item(i).disabled = false;
			dom.event.addEventListener(inputs.item(i), 'click', disabledBtn);
		}
	}
}
function disabledBtn(e) {
	var inputs = document.getElementsByTagName('INPUT');
	for(var i=0; i<inputs.length; i++) {
		if(inputs.item(i).type == 'submit') {
			inputs.item(i).disabled = true;
		}
	}
	var target = dom.event.target(e);
	target.form.submit();
	dom.event.preventDefault(e);
	dom.event.stopPropagation(e);
}
