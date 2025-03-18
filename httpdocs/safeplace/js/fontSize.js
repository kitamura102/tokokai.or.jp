<!--

var curMojiSize = 15; 	
var MojiModifier = 1; 

function fontSize(id) {
    
	HtmlContent = document.getElementById("contents");
	if (id == 1) {
		curMojiSize =15
		curMojiSize = Math.min(curMojiSize, 41);
		
	}
	else if (id == 0) {
		curMojiSize =12
		curMojiSize = Math.max(curMojiSize, 9);
	}
	else if (id == 2) {
		curMojiSize =16
		curMojiSize = Math.max(curMojiSize, 9);
	}
	
	HtmlContent.style.fontSize = curMojiSize + "px";
		    
	// set cookie with font size
	var expdate = new Date();
	FixCookieDate (expdate);
	expdate.setTime (expdate.getTime() + (672*60*60*1000)); 
	SetCookie("fontsizec",curMojiSize,expdate);		
}

var userfont = GetCookie('fontsizec');

if (userfont<=1){userfont=13;}

function fontCookie() {
	
	ufd = ((userfont - curMojiSize));
	if (userfont >= 13) {
		for (i=0;i<ufd;i++) {
			fontSize(1);
		}	
	} else {
		for (i=0;i>ufd;i--) {
			fontSize(0);
		}
	}	
}

function getCookieVal(offset) {
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1)
	endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}

function FixCookieDate(date) {
	var base = new Date(0);
	var skew = base.getTime();
	if (skew > 0) 
		date.setTime (date.getTime() - skew);
}

function GetCookie(name) {
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i=0;
	
	while (i < clen) {	
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg) {
			return getCookieVal (j);
		}
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) break;
		}
	return null;
}

function SetCookie(name,value,expires,path,domain,secure) {		
	document.cookie = name + "=" + escape (value) +
	((expires) ? "; expires=" + expires.toGMTString() : "") +
	"; path=/" + 
	((domain) ? "; domain=" + domain : "") +
	((secure) ? "; secure" : "");
}

-->
