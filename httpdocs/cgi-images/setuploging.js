dom.event.addEventListener(window, 'load', initDocument);
function initDocument() {
	var img = document.createElement('IMG');
	img.src = 'acclog.cgi';
	img.alt = 'テスト用解析タグ';
	img.title = 'テスト用解析タグ';
	document.appendChild(img);
}
