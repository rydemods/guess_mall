<?php // hspark
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
var Class = function(properties){
	var klass = function(){
		for (p in this) this[p]._proto_ = this;
		if (arguments[0] != 'noinit' && this.initialize) return this.initialize.apply(this, arguments);
	};
	klass.extend = this.extend;
	klass.implement = this.implement;
	klass.prototype = properties;
	return klass;
};

Class.empty = function(){};

Class.create = function(properties){
	return new Class(properties);
};

Class.prototype = {
	extend: function(properties){
		var prototype = new this('noinit');
		for (property in properties){
			var previous = prototype[property];
			var current = properties[property];
			if (previous && previous != current) current = previous.parentize(current) || current;
			prototype[property] = current;
		}
		return new Class(prototype);
	},
	
	implement: function(properties){
		for (property in properties) this.prototype[property] = properties[property];
	}
}

function OpenWindow(url,width,height,scroll, windowname) {
	var left = (screen.width/2) - (width/2);
	var top = (screen.height/2) - (height/2); 
	window.open(url,windowname,"scrollbars="+scroll+",toolbar=no,location=no,directories=no,status=no,left="+left+",top="+top+",width="+width+",height="+height+",resizable=no,menubar=no");
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

function chkResNo(obj) {
	if (obj.length == 14) {
		var calStr1 = "2345670892345", biVal = 0, tmpCal, restCal;
		
		for (i=0; i <= 12; i++) {
			if (obj.substring(i,i+1) == "-")
				tmpCal = 1
			else
				biVal = biVal + (parseFloat(obj.substring(i,i+1)) * parseFloat(calStr1.substring(i,i+1)));
		}

		restCal = 11 - (biVal % 11);

		if (restCal == 11) {
			restCal = 1;
		}

		if (restCal == 10) {
			restCal = 0;
		}

		if (restCal == parseFloat(obj.substring(13,14))) {
			return true;
		} else {
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

function IsAlphaNumeric(data) {
	var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
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

function GoPage1(block,gotopage) {
        document.form1.block.value = block;
        document.form1.gotopage.value = gotopage;
        document.form1.submit();
}

function GoPage2(block,gotopage) {
        document.form2.block.value = block;
        document.form2.gotopage.value = gotopage;
        document.form2.submit();
}


//flash write
var embedcls = new Class({
	init : function(url,width,height) {
		this.url=url;
		this.width=width;
		this.height=height;
		this.param="<PARAM NAME=movie VALUE=\""+url+"\">";
		this.param+="<PARAM NAME=quality VALUE=high>";
		this.param+="<PARAM NAME=bgcolor VALUE=#FFFFFF>";
		this.param+="<PARAM NAME=wmode VALUE=Transparent>";
	},
	setparam : function(name,value) {
		this.param="<PARAM NAME=\""+name+"\" VALUE=\""+value+"\">"+this.param;
	},
	show : function() {
		embedstr="<OBJECT classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\" WIDTH=\""+this.width+"\" HEIGHT=\""+this.height+"\">";
		embedstr+=this.param;
		embedstr+="<EMBED src=\""+this.url+"\" quality=high bgcolor=#FFFFFF WIDTH="+this.width+" HEIGHT="+this.height+" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>";
		embedstr+="</OBJECT>";
		document.write(embedstr);
	}
});

function CrmView(id) {
    document.crmview.id.value = id;
    window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
    document.crmview.submit();
}

var flash_show = function(url,width,height) {
	embedcls=new embedcls();
	embedcls.init(url,width,height);
	embedcls.show();
};

