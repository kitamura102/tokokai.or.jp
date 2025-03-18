<!--

var curFontSize = 13; 	
var fontModifier = 1; 

function fontSize(act) {
    
	tmpBodyContent = document.getElementById("contents");
	if (act == 1) {
		curFontSize += fontModifier;
		curFontSize = Math.min(curFontSize, 41);
		
	}
	else if (act == 0) {
		curFontSize -= fontModifier;
		curFontSize = Math.max(curFontSize, 9);
	}
	
	tmpBodyContent.style.fontSize = curFontSize + "px";
		    
	// set cookie with font size
	var expdate = new Date();
	FixCookieDate (expdate);
	expdate.setTime (expdate.getTime() + (672*60*60*1000)); // 4 weeks
	SetCookie("fontsizec",curFontSize,expdate);		
}

var userfont = GetCookie('fontsizec');

if (userfont<=1){userfont=13;}

function fontCookie() {
	
	ufd = ((userfont - curFontSize));
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
