<!--
var _expand_img = 'images/expand.gif';
var _contract_img = 'images/contract.gif';

function get_element(id) {
	return document.getElementById ? document.getElementById(id) : document.all ? eval('document.all.' + id)
 : null;
}

function toggle_display(id, img) {
	var obj = get_element(id);

	if (obj) {
		if (obj.className.toLowerCase() == "hidden_menu") {
			obj.className = "";
			img.src = _contract_img;
		} else {
			obj.className = "hidden_menu";
			img.src = _expand_img;
		}
	}
}


document.write('<style type="text/css">');
document.write('.hidden_menu { display: none; }');

if(document.all) { //for IE browsers only
   document.write('html div#container_div { width:expression(document.body.clientWidth < 784? "780px": "auto" ) }');
}

// Need to separate the close tag into two commands so it will be valid HTML4.01
document.write('<');
document.write('/style>');

-->


