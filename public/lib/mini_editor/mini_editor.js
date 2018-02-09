window.onload = mini_init;

var mini_base = "./";
var mini_editor_load = false;
var mini_eMode = true;
var mini_bHeader = "\
<style>\
P {margin:2px 0}\
body,td,th {font:12px dotum}\
th {font-weight:bold}\
//td,th {border:1px #BFBFBF dotted}\
</style>\
";
var mini_bHeader2 = "\
<style>\
P {margin:2px 0}\
body,td,th {font:12px dotum}\
th {font-weight:bold}\
</style>\
";

function mini_init()
{
	if (document.all) mini_oHTML.designMode = "on";
	else document.getElementById('_mini_oHTML').contentDocument.designMode = "on";
	
	mini_oHTML.open();
	mini_oHTML.write(mini_bHeader);
	if (mini_oTEXT.value) mini_oHTML.write(mini_oTEXT.value);
	else mini_oHTML.write("<p>&nbsp;</p>");
	mini_oHTML.close();
	mini_oHTML.body.style.margin = 5;
	mini_oHTML.body.onclick = mini_reset;
	mini_oHTML.body.onkeypress = mini_oHTML_dn;

	mini_oPREVIEW.open();
	mini_oPREVIEW.write(mini_bHeader2);
	mini_oPREVIEW.close();
	mini_oPREVIEW.body.style.margin = 5;

	mini_height = document.getElementById('frmMiniEditor').offsetHeight;
	mini_editor_load = true;
}

function mini_to_HTML()
{
	mini_chg_mode("mini_oHTML");
	mini_oHTML.body.innerHTML = mini_oTEXT.value;
}

function mini_to_TEXT()
{
	mini_chg_mode("mini_oTEXT");
	mini_oTEXT.value = mini_oHTML.body.innerHTML;
}

function mini_to_PREVIEW()
{
	if (mini_eMode) mini_to_TEXT();
	mini_chg_mode("mini_oPREVIEW");
	mini_oPREVIEW.body.innerHTML = mini_oTEXT.value;
}

function mini_chg_mode(mode)
{
	document.getElementById('_mini_oHTML').style.display = "none";
	document.getElementById('_mini_oPREVIEW').style.display = "none";
	mini_oTEXT.style.display = "none";

	switch (mode){
		case "mini_oHTML": document.getElementById('_mini_oHTML').style.display = "block"; break;
		case "mini_oTEXT": mini_oTEXT.style.display = "block"; break;
		case "mini_oPREVIEW" : document.getElementById('_mini_oPREVIEW').style.display = "block"; break;
	}

	mini_eMode = (mode=="mini_oHTML") ? true : false;

	if (mode=="mini_oHTML"){
		
		mini_status_edit.src = mini_base + "images/status_edit_up.gif";
		mini_status_source.src = mini_base + "images/status_source.gif";
		mini_status_preview.src = mini_base + "images/status_preview.gif";
		
		mini_status_edit.onclick = "";
		mini_status_source.onclick = mini_to_TEXT;
		mini_status_preview.onclick = mini_to_PREVIEW;

		mini_status_edit.style.cursor = "default";
		mini_status_source.style.cursor = "pointer";
		mini_status_preview.style.cursor = "pointer";

		document.getElementById("_mini_oHTML").contentWindow.focus()
	
	} else if (mode=="mini_oTEXT"){
		
		mini_status_edit.src = mini_base + "images/status_edit.gif";
		mini_status_source.src = mini_base + "images/status_source_up.gif";
		mini_status_preview.src = mini_base + "images/status_preview.gif";
		
		mini_status_edit.onclick = mini_to_HTML;
		mini_status_source.onclick = "";
		mini_status_preview.onclick = mini_to_PREVIEW;

		mini_status_edit.style.cursor = "pointer";
		mini_status_source.style.cursor = "default";
		mini_status_preview.style.cursor = "pointer";
		
		mini_oTEXT.focus();
	
	} else {
		
		mini_status_edit.src = mini_base + "images/status_edit.gif";
		mini_status_source.src = mini_base + "images/status_source.gif";
		mini_status_preview.src = mini_base + "images/status_preview_up.gif";
		
		mini_status_edit.onclick = mini_to_HTML;
		mini_status_source.onclick = mini_to_TEXT;
		mini_status_preview.onclick = "";

		mini_status_edit.style.cursor = "pointer";
		mini_status_source.style.cursor = "pointer";
		mini_status_preview.style.cursor = "default";

	}
}

function mini_oTEXT_dn(El)
{
	if ((document.all)&&(event.keyCode==9)){
		El.selection = document.selection.createRange(); 
		document.all[El.name].selection.text = String.fromCharCode(9)
		document.all[El.name].focus();
		return false;
	}
}

function mini_oHTML_dn()
{
	var event = document.getElementById('_mini_oHTML').contentWindow.event;
	if (event.keyCode == 13){
		if (event.shiftKey == false){
			var sel = document.selection.createRange();
			sel.pasteHTML('<br>');
			event.cancelBubble = true;
			event.returnValue = false;
			sel.select();
			return false;
		} else {
			return event.keyCode = 13;
		}
	}
}

function mini_editor(name,value,base,option)
{
	if (base) mini_base = base;
	//value = decodeURIComponent(value);

	document.write("<table id=frmMiniEditor width=100% height=100% cellpadding=0 cellspacing=0 style='position:relative'>\
	<tr><td height=100% style='border-right:1px solid #000000;border-bottom:1px solid #000000'>\
	<table width=100% height=100% cellpadding=0 cellspacing=0 bgcolor=#C0C0C0>\
	<tr><td height=1></td></tr><tr><td height=1 bgcolor=#fffffff></td></tr><tr><td>");
	mini_setToolbar();
	document.write("</td></tr><tr><td height=100% style='padding:0 5 0 6' valign=top>\
	<iframe id=_mini_oHTML class=mini_oHTML></iframe>\
	<iframe id=_mini_oPREVIEW class=mini_oHTML style='display:none'></iframe>\
	<textarea id=_mini_oTEXT name=" + name + " class=mini_oTEXT onkeydown='return mini_oTEXT_dn(this)' " + option + ">" + value + "</textarea>\
	</td></tr>\
	<tr><td height=6></td></tr>\
	<tr><td background='" + mini_base + "images/status_border.gif'>\
	<table cellpadding=0 cellspacing=0 bgcolor=#C0C0C0><tr>\
	<td><img id=mini_status_edit_ src='" + mini_base + "images/status_edit_up.gif'></td>\
	<td><img id=mini_status_source_ src='" + mini_base + "images/status_source.gif' onClick='mini_to_TEXT()' class=hand></td>\
	<td><img id=mini_status_preview_ src='" + mini_base + "images/status_preview.gif' onClick='mini_to_PREVIEW()' class=hand></td>\
	<td background='" + mini_base + "images/status_border.gif' align=right width=100% valign=bottom>" + mini_setButton('reSizeM','button_reSizeM.gif','') + mini_setButton('reSizeP','button_reSizeP.gif','') + "</td>\
	</tr></table>\
	</td></tr></table></td></tr>\
	<tr id=lyr_mini_file style='display:none'><td>\
	<table width=100% cellpadding=0 cellspacing=0>\
	<tr>\
		<td width=100%><select id=mini_field multiple style='width:100%;height:90px;font:8pt tahoma'></select></td>\
		<td width=60 nowrap><input type=button style='border:1px solid #000000;background:#D6D3CE;font:bold 8pt tahoma;width:100%;height:90' onclick='mini_deleteItem()' value='delete'></td>\
	</tr>\
	<tr><td colspan=2>\
	<table id=mini_tableX width=100% cellpadding=0 cellspacing=0><tr><td><input type='file' name=mini_file[] onchange=mini_insert(this) style='width:100%'></td></tr></table>\
	</td></tr></table></td></tr></table>\
	<object id=mini_dlgHelper classid='clsid:3050f819-98b5-11cf-bb82-00aa00bdce0b' width=0 height=0></object>");

	mini_oHTML = document.getElementById('_mini_oHTML').contentWindow.document;
	mini_oTEXT = document.getElementById('_mini_oTEXT');
	mini_oPREVIEW = document.getElementById('_mini_oPREVIEW').contentWindow.document;
	mini_status_edit   = document.getElementById('mini_status_edit_');
	mini_status_source = document.getElementById('mini_status_source_');
	mini_status_preview = document.getElementById('mini_status_preview_');

	//mini_field	= document.getElementById('mini_field');
	//mini_table = document.getElementById('mini_tableX')
	mini_index	= 0;
}

function mini_setToolbar()
{
	document.write("\
	<table id=mini_toolbar cellpadding=0 cellspacing=2><tr>\
	<td>" + mini_setEl_fontFamily() + "</td>\
	<td>" + mini_setEl_fontSize() + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('Cut','button_cut.gif','') + "</td>\
	<td>" + mini_setButton('Copy','button_copy.gif','') + "</td>\
	<td>" + mini_setButton('Paste','button_paste.gif','') + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('Undo','button_back.gif','') + "</td>\
	<td>" + mini_setButton('Redo','button_forward.gif','') + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('Bold','button_bold.gif',2) + "</td>\
	<td>" + mini_setButton('Underline','button_underline.gif',2) + "</td>\
	<td>" + mini_setButton('Italic','button_italic.gif',2) + "</td>\
	<td>" + mini_setButton('Strikethrough','button_strikethrough.gif',2) + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('ForeColor','button_font_color.gif','') + "</td>\
	<td>" + mini_setButton('BackColor','button_background_color.gif','') + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('JustifyLeft','button_align_left.gif','') + "</td>\
	<td>" + mini_setButton('JustifyCenter','button_align_center.gif','') + "</td>\
	<td>" + mini_setButton('JustifyRight','button_align_right.gif','') + "</td>\
	<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
	<td>" + mini_setButton('CreateLink','button_link.gif','') + "</td>\
	<td>" + mini_setButton('InsertTable','button_table.gif','') + "</td>\
	<td>" + mini_setButton('InsertImage','button_image.gif','') + "</td>\
	</tr></table>\
	");
}

/*	Indent, Outdent
<td background='" + mini_base + "images/seperator.gif' width=2 nowrap></td>\
<td>" + mini_setButton('','button_help.gif','') + "</td>\
*/

function mini_addField()
{
	oTr		= mini_table.insertRow(); 
	oTd		= oTr.insertCell();
	oTd.innerHTML = mini_table.rows[0].cells[0].innerHTML;
}

function url_file(url)
{
	ret = "file:///" + url.replace(/\\/g,"/");
	return ret.replace(/ /g,"%20");
}

function mini_insert(obj)
{
	div = obj.value.split("\\");

	mini_field.options.length = mini_index + 1;
	mini_field.options[mini_index].text = div[div.length-1];
	mini_field.options[mini_index].value = obj.value;

	mini_table.rows[mini_index].style.display = "none";
	mini_addField();
	mini_index++;

	var value = url_file(obj.value);
	document.getElementById("_mini_oHTML").contentWindow.focus();
	document.getElementById("_mini_oHTML").contentWindow.document.execCommand("InsertImage",0,value);
}

function mini_deleteItem()
{
	var firstItem = mini_field.selectedIndex;
	if (firstItem<0) return;
	for (i=mini_field.options.length-1; i>=firstItem; i--) {
		if (mini_field.options[i].selected){
			mini_field.options[i] = null;
			mini_table.deleteRow(i);
			mini_index--;
		}
	}
}

function mini_setEl_fontFamily()
{
	var El	= new Array("굴림","dotum","바탕","궁서","굴림체","arial","courier","sans-serif","tahoma");
	mini_res = "<select id=mini_btnFontName class=mini_select onchange=\"mini_format('fontname',this[this.selectedIndex].value)\"><option>Font";
	for (i=0;i<El.length;i++){
		mini_res += "<option value='" + El[i] + "'>" + El[i];
	}
	mini_res += "</select>";
	return mini_res;
}

function mini_setEl_fontSize()
{
	var El	= new Array(1,2,3,4,5,6,7);
	mini_res = "<select id=mini_btnFontSize class=mini_select onchange=\"mini_format('fontsize',this[this.selectedIndex].value)\"><option>Size";
	for (i=0;i<El.length;i++){
		mini_res += "<option value='" + El[i] + "'>" + El[i];
	}
	mini_res += "</select>";
	return mini_res;
}

function mini_setButton(what,src,btn_out_mode)
{
	var id = "mini_btn" + what;
	mini_res = "<img id=" + id + " src='" + mini_base + "images/" + src + "' onmouseover=mini_btn_over(this) onmouseout=mini_btn_out" + btn_out_mode + "(this) onmousedown=mini_btn_down(this) onmouseup=mini_btn_up(this) class=mini_toolbutton onClick=\"mini_format('" + what + "',null)\" unselectable='on'>";
	return mini_res;
}

function mini_format(what,opt)
{
	if (what=="reSizeP" || what=="reSizeM"){
		mini_calcu = (what=="reSizeP") ? 1 : -1;
		mini_preWidth = document.getElementById('frmMiniEditor').offsetWidth;
		//document.getElementById('frmMiniEditor').style.width = mini_preWidth + 100 * mini_calcu;
		//if (mini_preWidth!=document.getElementById('frmMiniEditor').offsetWidth) document.getElementById('frmMiniEditor').style.height = document.getElementById('frmMiniEditor').offsetHeight + 100 * mini_calcu;
		var height = document.getElementById('frmMiniEditor').offsetHeight + 100 * mini_calcu;
		if (height>=mini_height) document.getElementById('frmMiniEditor').style.height = height;
		what = "";
	}
	
	if (mini_eMode){
		var mode = false;
		var rng = mini_oHTML.selection.createRange();
		switch (what){
		case "InsertImage":
			mini_insertImage();
			what = "";
			//document.getElementById("_mini_oHTML").contentWindow.focus()
			//mode = true;
			break;
		case "InsertTable":
			mini_insertTable();
			what = "";
			break;
		case "CreateLink":
			document.getElementById("_mini_oHTML").contentWindow.focus()
			mode = true;
			break;
		case "ForeColor": case "BackColor":
			var opt = mini_getColorFromColorDlg(mini_oHTML.queryCommandValue(what));
			break;
		}
		if (what) mini_oHTML.execCommand(what, mode, opt);
		//mini_oHTML.focus();
		mini_reset(what);
	}
}

function mini_insertHTML(str)
{
	document.getElementById("_mini_oHTML").contentWindow.focus();
	if (mini_oHTML.selection.type=="Control") mini_oHTML.selection.clear();
	rng = mini_oHTML.selection.createRange();
	rng.pasteHTML(str);
}

function mini_insertImage()
{
	//document.getElementById("_mini_oHTML").contentWindow.focus();
	showModalDialog(mini_base + "popupImg.php",window,"dialogWidth:400px; dialogHeight:510px; resizable: no; help: no; status: yes; scroll: no");
}

function mini_insertTable()
{
	showModalDialog(mini_base + "insertTable.php",window,"dialogWidth:400px; dialogHeight:200px; resizable: no; help: no; status: yes; scroll: no");
}

function mini_reset(El)
{
	if (!El || El=='Bold') mini_setState('mini_btnBold', mini_oHTML.queryCommandValue('Bold'));
	if (!El || El=='Underline') mini_setState('mini_btnUnderline', mini_oHTML.queryCommandValue('Underline'));
	if (!El || El=='Strikethrough') mini_setState('mini_btnStrikethrough', mini_oHTML.queryCommandValue('Strikethrough'));
	if (!El || El=='Italic') mini_setState('mini_btnItalic', mini_oHTML.queryCommandValue('Italic'));
	if (!El || El=='fontname') mini_setState2('mini_btnFontName', mini_oHTML.queryCommandValue('fontname'));
	if (!El || El=='fontsize') mini_setState2('mini_btnFontSize', mini_oHTML.queryCommandValue('fontsize'));
}

function mini_setState(El, on)
{
	El = document.getElementById(El);
	if (!El.disabled){
		if (on) mini_btn_down(El);
		else mini_btn_out(El);
	}
}

function mini_setState2(El, on)
{
	El = document.getElementById(El);
	for (i=0;i<El.length;i++){
		if (El.options[i].value==on){
			El.selectedIndex = i;
			break;
		}
	}
}

function mini_btn_down(El){
	El.style.borderBottom = "buttonhighlight 1px solid";
	El.style.borderLeft	  = "buttonshadow 1px solid";
	El.style.borderRight  = "buttonhighlight 1px solid";
	El.style.borderTop    = "buttonshadow 1px solid";
}

function mini_btn_up(El){
	El.style.borderBottom = "buttonshadow 1px solid";
	El.style.borderLeft   = "buttonhighlight 1px solid";
	El.style.borderRight  = "buttonshadow 1px solid";
	El.style.borderTop    = "buttonhighlight 1px solid";
	El = null; 
}

function mini_btn_over(El)
{
	if (El.style.borderBottom != "buttonhighlight 1px solid"){
		El.style.borderBottom = "buttonshadow 1px solid";
		El.style.borderLeft	  = "buttonhighlight 1px solid";
		El.style.borderRight  = "buttonshadow 1px solid";
		El.style.borderTop    = "buttonhighlight 1px solid";
	}
}

function mini_btn_out(El)
{
	El.style.borderColor = "buttonface";
}

function mini_btn_out2(El){
	if (El.style.borderBottom != "buttonhighlight 1px solid") El.style.borderColor = "buttonface";
}

function mini_editor_submit()
{
	if (mini_editor_load){
		if (mini_eMode) mini_oTEXT.value = mini_oHTML.body.innerHTML;
		if (mini_oTEXT.value=="<P>&nbsp;</P>") mini_oTEXT.value = "";
	}
}

function mini_getColorFromColorDlg(sInitColor)
{
	var dlgHelper = document.getElementById("mini_dlgHelper");
	var sColor = dlgHelper.ChooseColorDlg(sInitColor);
	sColor = sColor.toString(16);
	if (sColor.length<6){
		var sTempString = "000000".substring(0,6-sColor.length);
		sColor = sTempString.concat(sColor);
	}
	return sColor;
}