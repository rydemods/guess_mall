<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
String.prototype.cut = function(len) {
    var str = this;
    var l = 0;
    for (var i=0; i<str.length; i++) {
        l += (str.charCodeAt(i) > 128) ? 2 : 1;
        if (l > len) return str.substring(0,i);
    }
    return str;
}

String.prototype.bytes = function() {
    var str = this;
    var l = 0;
    for (var i=0; i<str.length; i++) l += (str.charCodeAt(i) > 128) ? 2 : 1;
    return l;
}

/* onLoad Handler */
function LH_create() {
	this.LIST = new Array();
	this.add = LH_add;
	this.exec = LH_exec;
}

function LH_add(strExec) {
	this.LIST[this.LIST.length] = strExec;
}

function LH_exec() {
	var list_len = this.LIST.length;
	for (var i = 0; i < list_len; i++) {
		eval(this.LIST[i]);
	}
}

function resize_iframe(name) {
	try {
		var oBody   = document.frames(name).document.body;
		var oIFrame = document.all(name);
		var frmWidth  = oBody.scrollWidth;
		var frmHeight = oBody.scrollHeight;
		
		oIFrame.style.height = frmHeight;
		oIFrame.style.width = frmWidth;
	} catch (e) {
		//window.status = "IFrame Resize Error";
	}
}

function parent_resizeIframe(name) {
	try{
		if (parent && parent != this && parent.resize_iframe != null) {
			parent.resize_iframe(name);
		}
	}
	catch (e) {
		//window.status = "IFrame Resize Error";
	}
}

function chkBizNo(obj) {
	if (obj.length == 10) {
		var bizID = obj;
		var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
		var tmpBizID, i, c2, remander;
		var chkSum = 0;

		for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);

		c2 = "0" + (checkID[8] * bizID.charAt(8));
		c2 = c2.substring(c2.length - 2, c2.length);

		chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));

		remainder = (10 - (chkSum % 10)) % 10 ;

		if (Math.floor(bizID.charAt(9)) != remainder){
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function CheckLength(obj) {
	var data = obj.value;
	var numstr = "!@#$%^&*()_+|-=\,./?><0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
		else
			count = count + 2;
	}		
	return  count;
}

function IsNumeric(data) {
	var numstr = "0123456789";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )
				
	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
	}		
	if ( count == data.length )
		return(true);
	else
		return( false );
}	

function strnumkeyup(field) {
	if (!isNumber(field.value)) {
		alert("숫자만 입력하세요.");
		field.value=strLenCnt(field.value,field.value.length - 1);
		field.focus();
		return;
	}
}

function strLenCnt(str,lengths) {	//문자열의 특정 길이를 반환한다.
	var len = 0;
	var newStr = '';

	for (var i=0;i<str.length; i++) {
		var n = str.charCodeAt(i);
		var nv = str.charAt(i);
		if ((n>= 0)&&(n<256)) {
			len ++;
		} else {
			len += 2;
		}

		if (len>lengths)
			break;
		else
			newStr = newStr + nv;
	}
	return newStr;
}

function isNumber(arg) {
	for (i =0 ; i < arg.length; i++) {
	  	if (arg.charCodeAt(i) < 48 || arg.charCodeAt(i) > 57) {
	  		return false;
	  	}
	}
	return true;
}

function IsMailCheck(email) {
	isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
	if(isMailChk.test(email)) {
		return true;
	} else {
		return false;
	}
}

function replaceTag(str) {
	var temp = str;
	var oldArr = new Array("<", ">");
	var newArr = new Array("&lt;", "&gt;");

	for(var i=0; i<oldArr.length; i++){
		var oldStr = oldArr[i];
		var newStr = newArr[i];
		while (temp.indexOf(oldStr)>-1) {
			pos= temp.indexOf(oldStr);
			temp = "" + (temp.substring(0, pos) + newStr + temp.substring((pos + oldStr.length), temp.length));
		}
	}
	return temp;
}

function windowOpenScroll(url, title, w, h){
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = "width="+w+", height="+h+", resizable=no, menubar=no, status=yes, scrollbars=yes, left=" + winl + ", top=" + wint;

	win = window.open (url, title, winprops);
	win.window.focus();
	
	return win;
}

function toMoney(src){
	var srcObj = String(src);
	var rtnSrc = "";
	var len = srcObj.length;
	if (len <= 3 ) return srcObj;
	for(i=0;i<len;i++){
		if (i!=0 && ( i % 3 == len % 3) ) rtnSrc += ",";
		if(i < len ) rtnSrc += srcObj.charAt(i);
	}
	return rtnSrc;
}

function f_rtrim( instr ) {
	var last_space;
	var ret;

	last_space = instr.length;
	while( instr.charAt( last_space - 1 ) == " " ) {
		last_space --;
	}

	ret = instr.substring( 0, last_space );
	return( ret );
}

function ValidImageFile(obj){
	if(obj != null && f_rtrim(obj.value) != ''){
		var filename = obj.value;
		var idx = filename.lastIndexOf(".");
		ext = filename.substring( idx + 1, filename.length );
		if ( ext.toUpperCase() == "JPG" || ext.toUpperCase() == "GIF" ) {
			return true;
		}else{
			return false;
		}
	}
}

function chkFieldMaxLen(max) {
    var obj = event.srcElement;

    if (obj.value.bytes() > max) {
        alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글" + max/2 + "자 이내 혹은 영문/숫자/기호 " + max + "자 이내로 입력이 가능합니다.");
        obj.value = obj.value.cut(max);
        obj.focus();
    }
}
