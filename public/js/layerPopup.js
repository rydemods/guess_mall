function popupJquery(message, w, h) {
	$('#dialog-box').html("<div class='dialog-content'><div id='dialog-message'></div></div>");

	var maskHeight = $(document).height();  
	var maskWidth = $(window).width();
	
	// ¿øº» : var dialogTop =  (maskHeight/4) - ($('#dialog-box').height());
	var dialogTop =  $(window).scrollTop() + ($(window).height()/2) - (h/2);  

	//var dialogTop =  (maskHeight/1.5) - (h);  
	var dialogLeft = (maskWidth/2) - (w/2); 
	

	$('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
	$('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();
	
	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = w;
		height = h;
	}
	ifrm.id = 'createIfrm';
	ifrm.width = w;
	ifrm.height = h;
	ifrm.frameBorder = 0;

	$('#dialog-message').css({width:w, height:h, border:"1px solid #000000"});
	$('#dialog-message').append(ifrm);

	ifrm.src = message;
}
