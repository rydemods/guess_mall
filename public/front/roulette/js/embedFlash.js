// 플래시 임베드
function embed_flash(_url, _id, _width, _height, _vars, _wmode, _bgcolor){

	if (_vars == undefined || _vars == "") _vars = "";
	if (_wmode == undefined || _wmode == "") _wmode = "transparent";
	if (_bgcolor == undefined || _bgcolor == "") _bgcolor = "#ffffff";

	var flashStr;
	
	if (navigator.userAgent.indexOf("Gecko") != -1 || navigator.userAgent.indexOf("Presto") != -1) {

		// 파이어폭스, 사파리, 크롬, 오페라 테스트 완료
		flashStr = "<embed src = '" + _url + "' id = '" + _id + "' width = '" + _width + "' height = '" + _height + "' flashVars = '" + _vars + "' wmode = '" + _wmode + "' bgcolor = '" + _bgcolor + "' quality = 'high' align = 'middle' allowScriptAccess = 'always' allowFullScreen = 'false' type = 'application/x-shockwave-flash' pluginspage = 'http://www.adobe.com/go/getflashplayer' />";

	} else {

		// 익스플로러 6, 7, 8 테스트 완료
		flashStr = "";
		flashStr += "<object classid = 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0' id = '" + _id + "' width = '" + _width + "' height = '" + _height + "' align = 'middle'>";
		flashStr += "<param name = 'movie' value = '" + _url + "' />";
		flashStr += "<param name = 'flashVars' value = '" + _vars + "' />";
		flashStr += "<param name = 'wmode' value = '" + _wmode + "' />";
		flashStr += "<param name = 'bgcolor' value = '" + _bgcolor + "' />";
		flashStr += "<param name = 'allowScriptAccess' value = 'always' />";
		flashStr += "<param name = 'allowFullScreen' value = 'false' />";
		flashStr += "<param name = 'quality' value = 'high' />";
		flashStr += "</object>";

	}

	document.write(flashStr);

}