<!--#include file="./_include/config.asp"-->

<html>
<head>
<title>색 만들기</title>
<style type="text/css">
  body   {margin-left:10;}
  p, table, td, input {font-size:9pt}
  p      {text-align:right}
  img {border:0}
</style>

<%
	formNm = getRequest("formNm", REQUEST_GET)
	itemNm = getRequest("itemNm", REQUEST_GET)
%>

<script type="text/javascript">
<!--
var array;
var drag;
var oldhc, oldsc, oldbc;
var oldre, oldgr, oldbl;

function init() {
	array = new Array();

	document.onmouseup = endDrag;
	oldX = oldY = newX = newY = 0;
	drag = null;

	colortbl.Hrgb.value = colortbl.Hrgb.value.toUpperCase();
	//Hrgbchange();
	//colortbl.hc.focus();
	window.focus();
}

function HSBtoRGB(H,S,B) {
	var array = new Array();
	var x,y,z;
	var h,s,b;
	var re,gr,bl;
	var htmp, i,j;

	if (isNaN(H) == true || isNaN(S) == true || isNaN(B) == true) return;
	if (H == 360) H = 0;

	h = H;
	s = S/100;
	b = B/100;

	if (s == 0) {
		re = b;
		gr = b;
		bl = b;
	}
	else {
		if (h == 0) htmp = 0;
		else htmp = h/60;

		i = Math.floor(htmp);
		j= htmp - i;

		x = b*(1-s);
		y = b*(1-(s*j));
		z = b*(1-(s*(1-j)));

		switch (i) {
			case 0:
				re = b;
				gr = z;
				bl = x;
				break;
			case 1:
				gr = b;
				re = y;
				bl = x;
				break;
			case 2:
				gr = b;
				re = x;
				bl = z;
				break;
			case 3:
				bl = b;
				gr = y;
				re = x;
				break;
			case 4:
				bl = b;
				gr = x;
				re = z;
				break;
			case 5:
				re = b;
				gr = x;
				bl = y;
				break;
		}
	}

	array["RE"] = Math.round(re * 255);
	array["GR"] = Math.round(gr * 255);
	array["BL"] = Math.round(bl * 255);

	return array;
}

function RGBtoHSB(R,G,B) {
	var array = new Array();
	var re,gr,bl;
	var h,s,b;
	var min, tmp;
	var angcase;

	re = R/255;
	gr = G/255;
	bl = B/255;

	min = Math.min(re,Math.min(gr,bl));
	b = Math.max(re,Math.max(gr,bl));

	tmp = b - min;

	if (tmp == 0) s = 0;
	else s = tmp/b;

	if (s == 0) {
		h = 0;
	}
	else {
		if (b == re) {
			if ((re != gr) && (re != bl)) h = 60*((gr-bl)/tmp);
		}

		if (b == gr) {
			if (gr != bl) h = 120 + ((60*(bl-re))/tmp);
		}

		if (b == bl) {
			h = 240 + ((60*(re-gr))/tmp);
		}
	}

	if (h < 0) {
		h = 360 + h;
	}

	array["H"] = Math.round(h);
	array["S"] = Math.round(s * 100);
	array["B"] = Math.round(b * 100);

	return array;
}

function HStoXY(H, S) {
	var array = new Array();

	if (S == 0) {
		array["X"] = 0;
		array["Y"] = 0;
	}
	else {
		array["X"] = Math.round(S * Math.cos((Math.PI * H)/180));
		array["Y"] = Math.round(S * Math.sin((Math.PI * H)/180));
		array["Y"] = -1 * array["Y"];
	}

	return array;
}

function XYtoHS(X,Y,dis) {
	var array = new Array();

	array["s"] = Math.round(dis);
	if (X == 100 && Y == 100) {
		array["h"] = 0;
	}
	else {
		var h_tmp = (X - 100)/dis;
		array["h"] = Math.acos(h_tmp);
		array["h"] = Math.round((array["h"]*180)/Math.PI);
		if (Y > 100) {
			array["h"] = 360 - array["h"];
		}
	}

	return array;
}

function MVcursor(left,top){
	left = left + 96;
	top = top + 96;
	cursor1.style.left = left;
	cursor1.style.top = top;

	return;
}

function MVcursor2(top) {
	cursor2.style.top = 194 - top;
	return;
}

function ChgHE(red, green, blue) {
	var array = new Array();

	red = red - 0;
	green = green - 0;
	blue = blue - 0;

	if (red <16) array["Hre"] = "0" + red.toString(16);
	else array["Hre"] = red.toString(16);

	if (green <16) array["Hgr"] = "0" + green.toString(16);
	else array["Hgr"] = green.toString(16);

	if (blue <16) array["Hbl"] = "0" + blue.toString(16);
	else array["Hbl"] = blue.toString(16);

	array["Hre"] = array["Hre"].toUpperCase();
	array["Hgr"] = array["Hgr"].toUpperCase();
	array["Hbl"] = array["Hbl"].toUpperCase();

	return array;
}

function Viewcolor(red, green, blue) {
	var array = new Array();

	array = ChgHE(red,green,blue);
	viewCol.style.backgroundColor = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];

	return;
}

function ChgBcol() {
	var array = new Array();
	var bvalue = 0;
	var i = 0;

	for(var i=0; i < 11; i++) {
		bvalue = i * 10;
		array = HSBtoRGB(colortbl.hc.value, colortbl.sc.value, bvalue);
		array = ChgHE(array["RE"],array["GR"],array["BL"]);

		eval("Blayer"+i).style.backgroundColor = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];
	}

	return;
}

function Hrgbchange() {
	var array = new Array();
	var abc = "0123456789abcdefABCDEF";

	if (colortbl.Hrgb.value.length != 7) return;
	if (colortbl.Hrgb.value.substring(0,1) != "#") return;

	for(var i=1; i < 7;i++) {
		if (abc.indexOf(colortbl.Hrgb.value.charAt(i)) < 0) return;
	}

	var retmp = parseInt(colortbl.Hrgb.value.substr(1,2),16);
	var grtmp = parseInt(colortbl.Hrgb.value.substr(3,2),16);
	var bltmp = parseInt(colortbl.Hrgb.value.substr(5,2),16);

	colortbl.red.value = retmp;
	colortbl.green.value = grtmp;
	colortbl.blue.value = bltmp;

	array = RGBtoHSB(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.hc.value = array["H"];
	colortbl.sc.value = array["S"];
	colortbl.bc.value = array["B"];

	array = HStoXY(colortbl.hc.value, colortbl.sc.value);
	MVcursor(array["X"], array["Y"]);

	var top = Math.round(colortbl.bc.value * (198/100));
	MVcursor2(top);

	Viewcolor(colortbl.red.value, colortbl.green.value, colortbl.blue.value);
	ChgBcol();
}

function HSBchange() {
	var array = new Array();

	array = HSBtoRGB(colortbl.hc.value, colortbl.sc.value, colortbl.bc.value);
	colortbl.red.value = array["RE"];
	colortbl.green.value = array["GR"];
	colortbl.blue.value = array["BL"];

	array = HStoXY(colortbl.hc.value,colortbl.sc.value);
	MVcursor(array["X"],array["Y"]);
	Viewcolor(colortbl.red.value, colortbl.green.value, colortbl.blue.value);
	ChgBcol();
	array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];

	return;
}

function Hkeydown() {
	if (event.keyCode == 38) {
		if (colortbl.hc.value < 360) {
			colortbl.hc.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.hc.value >0) {
			colortbl.hc.value--;
		}
	}

	oldhc = colortbl.hc.value;

	return;
}

function Hchange() {
	if (colortbl.hc.value == "") return;
	if (isNaN(colortbl.hc.value) == true) return;

	if (colortbl.hc.value > 360) {
		colortbl.hc.value = 360;
	}

	if (colortbl.hc.value < 0) {
		colortbl.hc.value = 0;
	}

	HSBchange();

	return;
}

function Skeydown() {
	if (event.keyCode == 38) {
		if (colortbl.sc.value < 100) {
			colortbl.sc.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.sc.value >0) {
			colortbl.sc.value--;
		}
	}

	return;
}

function Schange() {
	if (colortbl.sc.value == "") return;
	if (isNaN(colortbl.sc.value) == true) return;

	if (colortbl.sc.value > 100) {
		colortbl.hc.value = 100;
	}

	if (colortbl.hc.value < 0) {
		colortbl.hc.value = 0;
	}

	HSBchange();

	return;
}

function Bkeydown() {
	if (event.keyCode == 38) {
		if (colortbl.bc.value < 100) {
			colortbl.bc.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.bc.value >0) {
			colortbl.bc.value--;
		}
	}

	return;
}

function Bchange() {
	if (colortbl.bc.value == "") return;
	if (isNaN(colortbl.bc.value) == true) return;

	if (colortbl.bc.value > 100) {
		colortbl.bc.value = 100;
	}

	if (colortbl.bc.value < 0) {
		colortbl.bc.value = 0;
	}

	var top = Math.round(colortbl.bc.value * (198/100));
	MVcursor2(top);
	HSBchange();

	return;
}

function RGBchange() {
	var array = new Array();

	array = RGBtoHSB(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.hc.value = array["H"];
	colortbl.sc.value = array["S"];
	colortbl.bc.value = array["B"];

	array = HStoXY(colortbl.hc.value,colortbl.sc.value);

	Viewcolor(colortbl.red.value, colortbl.green.value, colortbl.blue.value);
	MVcursor(array["X"],array["Y"]);
	ChgBcol();
	var top = Math.round(colortbl.bc.value * (198/100));
	MVcursor2(top);

	array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];

	return;
}

function REkeydown() {
	if (event.keyCode == 38) {
		if (colortbl.red.value < 255) {
			colortbl.red.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.red.value >0) {
			colortbl.red.value--;
		}
	}

	return;
}

function REchange() {
	if (colortbl.red.value == "") return;
	if (isNaN(colortbl.red.value) == true) return;

	if (colortbl.red.value > 255) {
		colortbl.red.value = 255;
	}

	if (colortbl.red.value < 0) {
		colortbl.red.value = 0;
	}

	RGBchange();

	return;
}

function GRkeydown() {
	if (event.keyCode == 38) {
		if (colortbl.green.value < 255) {
			colortbl.green.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.green.value >0) {
			colortbl.green.value--;
		}
	}

	return;
}

function GRchange() {
	if (colortbl.green.value == "") return;
	if (isNaN(colortbl.green.value) == true) return;

	if (colortbl.green.value > 255) {
		colortbl.green.value = 255;
	}

	if (colortbl.green.value < 0) {
		colortbl.green.value = 0;
	}

	RGBchange();

	return;
}

function BLkeydown() {
	if (event.keyCode == 38) {
		if (colortbl.blue.value < 255) {
			colortbl.blue.value++;
		}
	}

	if (event.keyCode == 40) {
		if (colortbl.blue.value >0) {
			colortbl.blue.value--;
		}
	}

	return;
}

function BLchange() {
	if (colortbl.blue.value == "") return;
	if (isNaN(colortbl.blue.value) == true) return;

	if (colortbl.blue.value > 255) {
		colortbl.blue.value = 255;
	}

	if (colortbl.blue.value < 0) {
		colortbl.blue.value = 0;
	}

	RGBchange();
	return;
}

function startDrag() {
	var array = new Array();
	var disfm_tmp = (((event.clientX - 30) - 100)*((event.clientX - 30) - 100)) + (((event.clientY - 30) - 100)*((event.clientY - 30) - 100));
	var disfm = Math.sqrt(disfm_tmp);

	if (disfm > 100.5) return;

	drag = cursor1;
	drag.style.pixelLeft = event.clientX - (30 + 4);
	drag.style.pixelTop = event.clientY - (30 + 4);

	var Left = drag.style.pixelLeft + 4;
	var Top = drag.style.pixelTop + 4;

	array = XYtoHS(Left,Top,disfm);
	colortbl.hc.value = array["h"];
	colortbl.sc.value = array["s"];

	array = HSBtoRGB(colortbl.hc.value,colortbl.sc.value,colortbl.bc.value);
	colortbl.red.value = array["RE"];
	colortbl.green.value = array["GR"];
	colortbl.blue.value = array["BL"];

	Viewcolor(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	ChgBcol();

	array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];

	oldX = event.clientX;
	oldY = event.clientY;

	return;
}

function startDrag2() {
	var array = new Array();

	if (event.clientY < 30 || event.clientY > 228) {
		return;
	}

	drag = cursor2;
	drag.style.pixelTop = event.clientY - (30 + 4);

	var Top = drag.style.pixelTop + 4;

	colortbl.bc.value = Math.round(100 - (Top * (100/198)));

	array = HSBtoRGB(colortbl.hc.value,colortbl.sc.value,colortbl.bc.value);
	colortbl.red.value = array["RE"];
	colortbl.green.value = array["GR"];
	colortbl.blue.value = array["BL"];

	Viewcolor(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
	colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];
	oldY = event.clientY;

	return;
}

function dragIt(){
	var array = new Array();

	if (drag != cursor1) {
		return;
	}

	var disfm_tmp = (((event.clientX-30) - 100)*((event.clientX-30) - 100)) + (((event.clientY-30) - 100)*((event.clientY-30) - 100));
	var disfm = Math.sqrt(disfm_tmp);
	if (disfm > 100.5) {
	}
	else {
		newX = event.clientX;
		newY = event.clientY;

		var distanceX = (newX - oldX);
		var distanceY = (newY - oldY);

		oldX=newX;
		oldY=newY;

		drag.style.pixelLeft += distanceX;
		drag.style.pixelTop += distanceY;

		var Left = drag.style.pixelLeft + 4;
		var Top = drag.style.pixelTop + 4;

		array = XYtoHS(Left,Top,disfm);
		colortbl.hc.value = array["h"];
		colortbl.sc.value = array["s"];

		array = HSBtoRGB(colortbl.hc.value,colortbl.sc.value,colortbl.bc.value);
		colortbl.red.value = array["RE"];
		colortbl.green.value = array["GR"];
		colortbl.blue.value = array["BL"];

		Viewcolor(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
		ChgBcol();
		array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
		colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];
	}

	event.returnValue = false;
}

function dragIt2() {
	var array = new Array();

	if (drag != cursor2) {
		return;
	}

	if (event.clientY < 30 || event.clientY > 228) {
	}
	else {
		newY = event.clientY;

		var distanceY = (newY - oldY);

		oldY = newY;

		drag.style.pixelTop += distanceY;

		var Top = drag.style.pixelTop + 4;

		colortbl.bc.value = Math.round(100 - (Top * (100/198)));

		array = HSBtoRGB(colortbl.hc.value, colortbl.sc.value, colortbl.bc.value);
		colortbl.red.value = array["RE"];
		colortbl.green.value = array["GR"];
		colortbl.blue.value = array["BL"];

		Viewcolor(colortbl.red.value, colortbl.green.value, colortbl.blue.value);
		array = ChgHE(colortbl.red.value,colortbl.green.value,colortbl.blue.value);
		colortbl.Hrgb.value = "#" + array["Hre"] + array["Hgr"] + array["Hbl"];
	}

	event.returnValue = false;
}

function endDrag() {
	drag = null;
	return;
}

function selectColorOk() {
	var f = document.colortbl;
	opener.selectColorOk("<%=formNm%>", "<%=itemNm%>", f.Hrgb.value);
	window.close();
}

function myCancel() {
	parent.myCancel();
}

window.attachEvent("onload", init);
-->
</script>
</head>

<body bgcolor="#EFEFEF">

<script type="text/javascript">
<!--
var i=0;
do {
	var left = 270;
	var top = 210 - (i*18);
	var col = Math.round(i * (255/10));

	document.write("<div id=Blayer" + i + " border=0 style=\"background-color:rgb(" + col + "," + col + "," + col + "); position:absolute; width:20px; height:18px; top:" + top + "px; left:" + left + "px\"></div>\n");
}
while(++i < 11);
//-->
</script>

<div style="background-image:url('<%=adminImgURL%>/bbar.gif'); position:absolute; top:26; left:263; width:33; height:208" border=0></div>

<div id=Bbar style="position:absolute; top:30; left:267; width:33; height:208" border=0 onmousemove="dragIt2()" onmousedown="startDrag2()">
<img id=cursor2 src="<%=adminImgURL%>/p2_color.gif" style="position:absolute; top:-4; left:0; width:26; height:9" border=0>
</div>

<div id=HScircle style="background-image:url('<%=adminImgURL%>/disk.jpg'); position:absolute; width:217; height:217; top:15; left:15" onmousemove="dragIt()" onmousedown="startDrag()">
	<div style="position:absolute; top:8; left:8; width:201; height:201" border=0>
	<img id=cursor1 src="<%=adminImgURL%>/p_color.gif" border=0 style="position:absolute; top:97; left:97; width:9; height:9">
	</div>
</div>

<div style="background-image:url('<%=adminImgURL%>/viewborder_color.gif'); position:absolute; width:100; height:60; left:320; top:26">
	<div id=viewCol style="background-color:#FFFFFF; position:absolute; width:96; height:56; left:2; top:2"></div>
</div>

<table width=500 border=0 cellPadding=1 cellSpacing=1 bordercolorlight=black bordercolordark=white align=left>

<form id=colortbl name=colortbl>

<tr>
	<td>
		<table width=200 cellpadding=0 cellspacing=0 border=0>
		<tr><td>&nbsp;</tr></tr>
		</table>
	</td>
	<td>
		<br><br><br><br>
		<table width=100 cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td width=50><br>
				<table width=50 cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=10><font size=2>H</font></td>
					<td width=40><input id=hc type=text size=3 value=0 onkeydown="Hkeydown()" onkeyup="Hchange()">％</td>
				</tr>
				<tr>
					<td width=10><font size=2>S</font></td>
					<td width=40><input id=sc type=text size=3 value=0 onkeydown="Skeydown()" onkeyup="Schange()">％</td>
				</tr>
				<tr>
					<td width=10><font size=2>B</font></td>
					<td width=40><input id=bc type=text size=3 value=100 onkeydown="Bkeydown()" onkeyup="Bchange()">％</td>
				</tr>
				</table>
			</td>
			<td width=50><br>
				<table width=50 cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td width=10><font size=2>R</font></td>
					<td width=40><input id=red type=text size=3 value=255 onkeydown="REkeydown()" onkeyup="REchange()"></td>
				</tr>
				<tr>
					<td width=10><font size=2>G</font></td>
					<td width=40><input id=green type=text size=3 value=255 onkeydown="GRkeydown()" onkeyup="GRchange()"></td>
				</tr>
				<tr>
					<td width=10><font size=2>B</font></td>
					<td width=40><input id=blue type=text size=3 value=255  onkeydown="BLkeydown()" onkeyup="BLchange()"></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<table width=150 cellpadding=0 cellspacing=0 border=0>
		<tr>
			<td style='padding-left:3px;'>
				<hr size=1>
				<table cellpadding=0 cellspacing=0 border=0>
				<tr>
					<td><input id=Hrgb type=text style="width:60" value=#FFFFFF onkeyup="Hrgbchange();" maxlength=7></td>
					<td><img src="<%=adminImgURL%>/select_color.gif" width="57" height="20" onClick="selectColorOk()" style="cursor:pointer;"></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>

</form>

</table>

<!--#include virtual="/_include/closer.asp"-->
