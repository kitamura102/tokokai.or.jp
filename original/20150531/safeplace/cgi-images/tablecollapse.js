dom.event.addEventListener(window, 'load', initDocument);
var IMAGE_URL = '';
function initDocument() {
	var imgs = document.getElementsByTagName('IMG');
	for( var i=0; i<imgs.length; i++) {
		var elm = imgs.item(i);
		if(elm.className == 'arrow') {
			dom.event.addEventListener(elm, 'click', rowCollapse);
		}
	}
	if(document.getElementById('marrorwr')) {
		dom.event.addEventListener(document.getElementById('marrorwr'), 'click', allRowFold);
	}
	if(document.getElementById('marrorwb')) {
		dom.event.addEventListener(document.getElementById('marrorwb'), 'click', allRowCollapse);
	}
}
function allRowCollapse(e) {
	var imgs = document.getElementsByTagName('IMG');
	for( var i=0; i<imgs.length; i++) {
		var img = imgs.item(i);
		if(img.className != 'arrow') { continue; }
		var imgpath_parts = img.src.split('/');
		imgpath_parts.pop();
		var imgpath = imgpath_parts.join('/');
		var exp = /^arrow(\d+)$/;
		var expres = img.id.match(exp);
		var n = expres[1];
		var subElm = document.getElementById('sub'+n);
		subElm.style.display = '';
		img.src = imgpath + '/arrowb.gif';
	}
}
function allRowFold(e) {
	var imgs = document.getElementsByTagName('IMG');
	for( var i=0; i<imgs.length; i++) {
		var img = imgs.item(i);
		if(img.className != 'arrow') { continue; }
		var imgpath_parts = img.src.split('/');
		imgpath_parts.pop();
		var imgpath = imgpath_parts.join('/');
		var exp = /^arrow(\d+)$/;
		var expres = img.id.match(exp);
		var n = expres[1];
		var subElm = document.getElementById('sub'+n);
		subElm.style.display = 'none';
		img.src = imgpath + '/arrowr.gif';
	}
}
function rowCollapse(e) {
	var img = dom.event.target(e);
	if(! img) { return false; }
	var imgpath_parts = img.src.split('/');
	imgpath_parts.pop();
	var imgpath = imgpath_parts.join('/');
	var exp = /^arrow(\d+)$/;
	var expres = img.id.match(exp);
	var n = expres[1];
	var subElm = document.getElementById('sub'+n);
	if(subElm.style.display == 'none') {
		subElm.style.display = '';
		img.src = imgpath + '/arrowb.gif';
	} else {
		subElm.style.display = 'none';
		img.src = imgpath + '/arrowr.gif';
	}
}