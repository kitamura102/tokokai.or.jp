
$(function() {

	//gNav SmartPhone (jquery.mmenu)		
	$("#gNavSP").mmenu({
		 offCanvas: { position: "right" },
		 navbar:	{ title:"産科･婦人科のご案内"}
	});

});


$(window).on('load resize', function(){
									 
	//container padding 
	var sizeHead = $('#header').outerHeight();
	$('#container').css('padding-top', sizeHead + 'px');
	

	//AnchorLink
	var url = $(location).attr('href');
    if (url.indexOf("?id=") == -1) {
    }else{
        var url_sp = url.split("?id=");
        var hash   = '#' + url_sp[url_sp.length - 1];
        var tgt    = $(hash);
        var pos    = tgt.offset().top-sizeHead;
        $("html, body").animate({scrollTop:pos}, 550, "swing");

    }


});