<!-- start container -->
<div id="container">
<? include dirname(__FILE__)."/side.php" ; ?> 
	<!-- start contents -->
	<div class="contents_side">

<script>
	function settingValue(values){
		arrVal = values.split("/");
		if(arrVal[1] != undefined){
			for(i = 2; i < 17; i++){
				mate = "material" + i;
				if(i == 2 || i == 3){
					arrVal[i] = arrVal[i].substring(0, 5)
				}
				document.getElementById(mate).value = arrVal[i];
			}
		}
	}
</script>

<script language="javascript" type="text/javascript">
<!--
var dom = (document.getElementById) ? true : false;
var ns5 = ((navigator.userAgent.indexOf("Gecko")>-1) && dom) ? true: false;
var ie5 = ((navigator.userAgent.indexOf("MSIE")>-1) && dom) ? true : false;
var ns4 = (document.layers && !dom) ? true : false;
var ie4 = (document.all && !dom) ? true : false;
var nodyn = (!ns5 && !ns4 && !ie4 && !ie5) ? true : false;

var origWidth, origHeight;
if (ns4) {
	origWidth = window.innerWidth; origHeight = window.innerHeight;
	window.onresize = function() { if (window.innerWidth != origWidth || window.innerHeight != origHeight) history.go(0); }
}

if (nodyn) { event = "nope" }

var tipFollowMouse= true;	
var tipWidth= 160;
var offX= 20;	
var offY= 12; 
var tipFontFamily= "tahoma";
var tipFontSize= "9pt";
var tipFontColor= "#000000";
var tipBgColor= "#505050"; 
var tipBorderColor= "#000000";
var tipBorderWidth= 0;
var tipBorderStyle= "ridge";
var tipPadding= 0;
var messages = new Array();

//이곳에 툴팁메세지(이미지포함)를 입력하세요..
messages[0] = new Array('','',"");  
messages[1] = new Array('/shop/data/img1/tooltip_01.gif','',""); 
messages[2] = new Array('/shop/data/img1/tooltip_02.gif','',""); 
messages[3] = new Array('/shop/data/img1/tooltip_03.gif','',""); 
messages[4] = new Array('/shop/data/img1/tooltip_04.gif','',""); 
messages[5] = new Array('/shop/data/img1/tooltip_05.gif','',""); 
messages[6] = new Array('/shop/data/img1/tooltip_06.gif','',""); 
messages[7] = new Array('/shop/data/img1/tooltip_07.gif','',""); 
messages[8] = new Array('/shop/data/img1/tooltip_08.gif','',""); 
messages[9] = new Array('/shop/data/img1/tooltip_09.gif','',""); 
messages[10] = new Array('/shop/data/img1/tooltip_10.gif','',""); 
messages[11] = new Array('/shop/data/img1/tooltip_11.gif','',""); 
messages[12] = new Array('/shop/data/img1/tooltip_12.gif','',""); 
messages[13] = new Array('/shop/data/img1/tooltip_13.gif','',""); 

if (document.images) {
	var theImgs = new Array();
	for (var i=0; i<messages.length; i++) {
  	theImgs[i] = new Image();
		theImgs[i].src = messages[i][0];
  }
}

var startStr = '<table width="' + tipWidth + '"><tr><td align="center" width="100%"><img src="';
var midStr = '" border="0"></td></tr><tr><td valign="top">';
var endStr = '</td></tr></table>';

var tooltip, tipcss;
function initTip() {
	if (nodyn) return;
	tooltip = (ns4)? document.tipDiv.document: (ie4)? document.all['tipDiv']: (ie5||ns5)? document.getElementById('tipDiv'): null;
	tipcss = (ns4)? document.tipDiv: tooltip.style;
	if (ie4||ie5||ns5) {	// ns4 would lose all this on rewrites
		tipcss.width = tipWidth+"px";
		tipcss.fontFamily = tipFontFamily;
		tipcss.fontSize = tipFontSize;
		tipcss.color = tipFontColor;
		tipcss.backgroundColor = tipBgColor;
		tipcss.borderColor = tipBorderColor;
		tipcss.borderWidth = tipBorderWidth+"px";
		tipcss.padding = tipPadding+"px";
		tipcss.borderStyle = tipBorderStyle;
	}
	if (tooltip&&tipFollowMouse) {
		if (ns4) document.captureEvents(Event.MOUSEMOVE);
		document.onmousemove = trackMouse;
	}
}

window.onload = initTip;

var t1,t2;	// for setTimeouts
var tipOn = false;	// check if over tooltip link
function doTooltip(evt,num) {
	if (!tooltip) return;
	if (t1) clearTimeout(t1);	if (t2) clearTimeout(t2);
	tipOn = true;
	// set colors if included in messages array
	if (messages[num][2])	var curBgColor = messages[num][2];
	else curBgColor = tipBgColor;
	if (messages[num][3])	var curFontColor = messages[num][3];
	else curFontColor = tipFontColor;
	if (ns4) {
		var tip = '<table bgcolor="' + tipBorderColor + '" width="' + tipWidth + '" cellspacing="0" cellpadding="' + tipBorderWidth + '" border="0"><tr><td><table bgcolor="' + curBgColor + '" width="100%" cellspacing="0" cellpadding="' + tipPadding + '" border="0"><tr><td>'+ startStr + messages[num][0] + midStr + '<span style="font-family:' + tipFontFamily + '; font-size:' + tipFontSize + '; color:' + curFontColor + ';">' + messages[num][1] + '</span>' + endStr + '</td></tr></table></td></tr></table>';
		tooltip.write(tip);
		tooltip.close();
	} else if (ie4||ie5||ns5) {
		var tip = startStr + messages[num][0] + midStr + '<span style="font-family:' + tipFontFamily + '; font-size:' + tipFontSize + '; color:' + curFontColor + ';">' + messages[num][1] + '</span>' + endStr;
		tipcss.backgroundColor = curBgColor;
	 	tooltip.innerHTML = tip;
	}
	if (!tipFollowMouse) positionTip(evt);
	else t1=setTimeout("tipcss.visibility='visible'",100);
}

var mouseX, mouseY;
function trackMouse(evt) {
	mouseX = (ns4||ns5)? evt.pageX: window.event.clientX + document.body.scrollLeft;
	mouseY = (ns4||ns5)? evt.pageY: window.event.clientY + document.body.scrollTop;
	if (tipOn) positionTip(evt);
}

function positionTip(evt) {
	if (!tipFollowMouse) {
		mouseX = (ns4||ns5)? evt.pageX: window.event.clientX + document.body.scrollLeft;
		mouseY = (ns4||ns5)? evt.pageY: window.event.clientY + document.body.scrollTop;
	}
	var tpWd = (ns4)? tooltip.width: (ie4||ie5)? tooltip.clientWidth: tooltip.offsetWidth;
	var tpHt = (ns4)? tooltip.height: (ie4||ie5)? tooltip.clientHeight: tooltip.offsetHeight;
	var winWd = (ns4||ns5)? window.innerWidth-20+window.pageXOffset: document.body.clientWidth+document.body.scrollLeft;
	var winHt = (ns4||ns5)? window.innerHeight-20+window.pageYOffset: document.body.clientHeight+document.body.scrollTop;
	if ((mouseX+offX+tpWd)>winWd) 
		tipcss.left = (ns4)? mouseX-(tpWd+offX): mouseX-(tpWd+offX)+"px";
	else tipcss.left = (ns4)? mouseX+offX: mouseX+offX+"px";
	if ((mouseY+offY+tpHt)>winHt) 
		tipcss.top = (ns4)? winHt-(tpHt+offY): winHt-(tpHt+offY)+"px";
	else tipcss.top = (ns4)? mouseY+offY: mouseY+offY+"px";
	if (!tipFollowMouse) t1=setTimeout("tipcss.visibility='visible'",100);
}

function hideTip() {
	if (!tooltip) return;
	t2=setTimeout("tipcss.visibility='hidden'",100);
	tipOn = false;
}

//-->
</script>

<form name = 'calcuFrm' action = 'recipe_calcu_indb.php' method = 'post'>
<div style = 'width:100%;float:left;margin-left:15px'>

<div style = 'width:100%;float:left;font-size:15px;'>
<table width="850" border="0" cellspacing="0" cellpadding="0">
  <tr><td><img src="/shop/data/img1/cal_title.gif"/></td></tr>
  <tr>
    <td height="20" background="/shop/data/img1/cal_01.gif" style="padding:0 0 0 202px;">
    <table width="240" height="15" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="26"><input type = 'radio' name = 'oh' value = 'chkNAOH' checked></td>
        <td width="187">&nbsp;</td>
        <td width="27"><input type = 'radio' name = 'oh' value = 'chkKOH'></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="850" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" style="padding-top:35px;"><img src="/shop/data/img1/cal_02.gif"/></td>
  </tr>
  <tr>
    <td height="26" colspan="2" background="/shop/data/img1/cal_03.gif" style="padding:0 0 0 160px;">
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="366"><input type = 'text' id = 'waterperoil' size = '11' name = 'waterperoil' style = 'border:0px; height:18px; font-size:15px;' value = '33'></td>
        <td width="234"><input type = 'text' id = 'weightOil' size = '11' name = 'weightoil' style = 'border:0px; height:18px; font-size:15px;' readonly></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td height="26" colspan="2" background="/shop/data/img1/cal_04.gif" style="padding:0 0 0 160px;">
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="366"><input type = 'text' id = 'superfat' size = '11' name = 'superfat' style = 'border:0px; height:18px; font-size:15px;' value = '5'></td>
        <td width="234"><input type = 'text' id = 'titleReci' size = '25' name = 'titleReci' style = 'border:0px; height:16px; font-size:15px;'></td>
      </tr>
    </table>
    </td>
  </tr>
    <tr>
    <td height="26" colspan="2" background="/shop/data/img1/cal_05.gif" style="padding:0 0 0 160px;">
    <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="600"><input type = 'text' id = 'intoMemo' size = '70' name = 'intoMemo' style = 'border:0px ;height:18px; font-size:15px;'></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><img src="/shop/data/img1/cal_06.gif" /></td>
  </tr>
  <tr>
    <td width="208" align="left"><img src="http://www.soapschool.co.kr/shop/data/img1/cal_memo.gif" width="200" height="80" /></td>
    <td width="556"></td>
  </tr>
</table>
</div>
</div>
<div style = 'width:100%;float:left;'>

	<div style = 'width:200px;float:left;'>		
		<table align = 'center' style = 'font-size:15px;'>
			<col align = 'center'><col align = 'center'>
			<tr>
				<td>
					<select size = '18' style = 'width:200px;border:0px solid #ccc;font-size:17px;' onClick = "settingValue(this.value)" ondblclick = 'addVal(this.value)'>
						<?foreach($list as $data){?>
							<option value = 'tmp/<?=$data[name]?>/<?=$data[naoh]?>/<?=$data[koh]?>/<?=$data[lauric]?>/<?=$data[myristic]?>/<?=$data[palmitic]?>/<?=$data[stearic]?>/<?=$data[ricinoleic]?>/<?=$data[oleic]?>/<?=$data[linoleic]?>/<?=$data[linolenic]?>/<?=$data[hardness]?>/<?=$data[cleansing]?>/<?=$data[conditions]?>/<?=$data[bubbly]?>/<?=$data[creamy]?>'>
								<?=$data[name]?>
							</option>
						<?}?>
					</select>
				</td>
			</tr>
            <tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td>
				<table width="198" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td>
<table width="198" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td><img src="/shop/data/img1/cal_10.gif" width="198" height="33"></td>
  </tr>
  <tr> 
    <td height="24" background="/shop/data/img1/cal_11.gif"><table width="188" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="95"><input type = 'text' id = 'material2' size = '10' maxlength = '5' name = 'NaOH2' style = 'border:1px ;height:18px; font-size:14px;'></td>
          <td width="93"><input type = 'text' id = 'material3' size = '10' maxlength = '5' name = 'KOH2' style = 'border:0px ;height:18px; font-size:14px;'></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="8" background="/shop/data/img1/cal_12.gif"></td>
  </tr>
</table>
					</td>
				  </tr>

				</table>
				</td>
			</tr>
		</table>
	</div>	
	<div style="width:30px">
		
	</div>
	<div style = 'width:544px;float:left;background:url(/shop/data/img1/cal_08.gif); margin-left:5px;border:2px solid #a0a0a0'>		
		<table width='544' style="font-size:12px;" border="0" cellspacing="2" cellpadding="0">
			<col width = '300' align = 'center'><col width = '80' align = 'center'><col width = '80' align = 'center'><col width = '90' align = 'center'>
			<tr><td height="40">&nbsp;</td><td> </td><td></td><td> </td></tr>
			<tr><td id = 'addRep01'><div align="center">
              <input type = 'text' size = '43' id = 'addRep001' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub01'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %</td><td id = 'addRepSub01'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub201'><span style = 'cursor:pointer;' onClick = "delVal('addRep01','addRepSub01','addRepSub201','addRepPerSub01')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'></span></td></tr>
			<tr><td id = 'addRep02'><div align="center">
              <input type = 'text' size = '43' id = 'addRep002' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub02'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub02'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub202'><span style = 'cursor:pointer;' onClick = "delVal('addRep02','addRepSub02','addRepSub202','addRepPerSub02')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep03'><div align="center">
              <input type = 'text' size = '43' id = 'addRep003' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub03'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub03'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub203'><span style = 'cursor:pointer;' onClick = "delVal('addRep03','addRepSub03','addRepSub203','addRepPerSub03')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep04'><div align="center">
              <input type = 'text' size = '43' id = 'addRep004' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub04'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub04'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub204'><span style = 'cursor:pointer;' onClick = "delVal('addRep04','addRepSub04','addRepSub204','addRepPerSub04')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep05'><div align="center">
              <input type = 'text' size = '43' id = 'addRep005' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub05'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub05'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub205'><span style = 'cursor:pointer;' onClick = "delVal('addRep05','addRepSub05','addRepSub205','addRepPerSub05')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep06'><div align="center">
              <input type = 'text' size = '43' id = 'addRep006' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub06'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub06'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub206'><span style = 'cursor:pointer;' onClick = "delVal('addRep06','addRepSub06','addRepSub206','addRepPerSub06')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep07'><div align="center">
              <input type = 'text' size = '43' id = 'addRep007' value = '' width = '100%'>
            </div></td><td id = 'addRepPerSub07'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %<td id = 'addRepSub07'><input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g</td><td id = 'addRepSub207'><span style = 'cursor:pointer;' onClick = "delVal('addRep07','addRepSub07','addRepSub207','addRepPerSub07')"> <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span></td></tr>
			<tr><td id = 'addRep08'></td><td id = 'addRepPerSub08'><td id = 'addRepSub08'></td><td id = 'addRepSub208'></td></tr>
			<tr><td id = 'addRep09'></td><td id = 'addRepPerSub09'><td id = 'addRepSub09'></td><td id = 'addRepSub209'></td></tr>
			<tr><td id = 'addRep010'></td><td id = 'addRepPerSub010'><td id = 'addRepSub010'></td><td id = 'addRepSub2010'></td></tr>
			<tr><td id = 'addRep011'></td><td id = 'addRepPerSub011'><td id = 'addRepSub011'></td><td id = 'addRepSub2011'></td></tr>
			<tr><td id = 'addRep012'></td><td id = 'addRepPerSub012'><td id = 'addRepSub012'></td><td id = 'addRepSub2012'></td></tr>
           <tr><td colspan="4"></td></tr>
		</table>
	</div>
</div>

<div style="text-align:right"><input type = 'image' src = '/shop/data/img1/cal_bt1.gif' value = '계산하기'><img src = '/shop/data/img1/cal_bt2.gif' value = '다시하기' style = 'cursor:pointer;display:inline' onClick = 'reLoadForm();'> </div>
<div style = 'width:740px;float:left;padding-top:40px;'>
<table width="850" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="140" bgcolor="#F7F7F7"><img src="/shop/data/img1/cal_13.gif" width="129" height="144" /></td>
			<td width="684" background="/shop/data/img1/cal_line3.gif" >
				<table width="621" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td height="40">
						<table width="621" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>
										<a href="#" onmouseover="doTooltip(event,1)" onmouseout="hideTip()">
											<img src="/shop/data/img1/cal_14.gif" width="71" height="20" border="0" />
										</a>
								</td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,2)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_15.gif" width="71" height="20" border="0" /></a>
								</td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,3)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_16.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,4)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_17.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,5)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_18.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,6)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_19.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,7)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_20.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,8)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_21.gif" width="71" height="20" border="0" /></a></td>
							</tr>
							<tr>										
								<td><input type = 'text' id = 'material4' name = 'Lauric' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material5' name = 'Myristic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material6' name = 'Palmitic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material7' name = 'Stearic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material8' name = 'Ricinoleic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material9' name = 'Oleic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material10' name = 'Linoleic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material11' name = 'Linolenic' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td height="41">
						<table width="621" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<a href="#" onmouseover="doTooltip(event,9)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_22.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="41" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,10)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_23.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="41" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,11)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_24.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="41" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,12)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_25.gif" width="71" height="20" border="0" /></a></td>
								<td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="41" /></td>
								<td>
									<a href="#" onmouseover="doTooltip(event,13)" onmouseout="hideTip()">
									<img src="/shop/data/img1/cal_26.gif" width="71" height="20" border="0" /></a></td>
								<td width="230" rowspan="2" style = 'font-size:11px;padding-left:10px;' >
								</td>
							</tr>
							<tr>
								<td><input type = 'text' id = 'material12' name = 'Hardness' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material13' name = 'Cleansing' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material14' name = 'Conditions' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material15' name = 'Bubbly' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
								<td><input type = 'text' id = 'material16' name = 'Creamy' size  = '8' style = 'border:1px solid #ccc;font-size:14px;text-align:center;'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td><img src="/shop/data/img1/cal_27.gif" /></td>
				</tr>
			</table>
		</td>
		<td width="26" bgcolor="#F7F7F7">&nbsp;</td>
	</tr>
	</table>
</div>
</form>

<script>
	function reLoadForm(){
		for(i = 1; i < 13; i++){
			addStr = "addRep0" + i;
			addStrSub = "addRepSub0" + i;
			addStrSub2 = "addRepSub20" + i;
			addStrPerSub2 = "addRepPerSub0" + i;
			document.getElementById(addStr).innerHTML = '';
			document.getElementById(addStrSub).innerHTML = "";
			document.getElementById(addStrPerSub2).innerHTML = "";
			document.getElementById(addStrSub2).innerHTML = "";
		}
		for(i = 2; i < 17; i++){
			addStr = "material" + i;
			document.getElementById(addStr).value = '';
		}
	}
	function addVal(val){
		for(i = 1; i < 13; i++){
			addStrS = "addRep00" + i;
			addStr = "addRep0" + i;
			addStrSub = "addRepSub0" + i;
			addStrSub2 = "addRepSub20" + i;
			addStrPerSub2 = "addRepPerSub0" + i;
			
			arrValue = val.split("/");
			if(document.getElementById(addStr).innerHTML == ''){
				if(arrValue[1] != 'undefined'){
					document.getElementById(addStr).innerHTML = "<div align='center'><input type = 'text' size = '43' id = '" + addStrS + "' value = '" + arrValue[1] + "' width = '100%'></div>";
					document.getElementById(addStrSub).innerHTML = "<input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g";
					document.getElementById(addStrPerSub2).innerHTML = "<input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %";
					document.getElementById(addStrSub2).innerHTML = "<span style = 'cursor:pointer;' onClick = 'delVal(\""+addStr+"\",\""+addStrSub+"\",\""+addStrSub2+"\",\""+addStrPerSub2+"\")'>  <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span><input type = 'hidden' name = 'hdnAll[]' value = '" +val+"'>";
					break;
				}
			}else if(document.getElementById(addStrS).value == ''){
				if(arrValue[1] != 'undefined'){
					document.getElementById(addStr).innerHTML = "<div align='center'><input type = 'text' size = '43' id = '" + addStrS + "' value = '" + arrValue[1] + "' width = '100%'></div>";
					document.getElementById(addStrSub).innerHTML = "<input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' value = '0' name = 'lb[]' onKeyup = 'changeTotOil()'> g";
					document.getElementById(addStrPerSub2).innerHTML = "<input type = 'text' style = 'border:1px solid #ccc;text-align:right;' size = '7' name = 'percent[]' readonly> %";
					document.getElementById(addStrSub2).innerHTML = "<span style = 'cursor:pointer;' onClick = 'delVal(\""+addStr+"\",\""+addStrSub+"\",\""+addStrSub2+"\",\""+addStrPerSub2+"\")'>  <img src = '/shop/data/img1/cal_del.gif' style = 'cursor:pointer;'> </span><input type = 'hidden' name = 'hdnAll[]' value = '" +val+"'>";
					break;
				}
			}else{		
				arrD = document.getElementById(addStr).innerHTML;
				arrD2 = "<INPUT value=\""+arrValue[1] + "\" width=\"100%\" size=34>";
				//alert(arrD);
				//alert(arrD2);
				if(arrD == arrD2){
					alert('동일한 레시피가 존재 하거나 선택을 하지 않으셨습니다.');
					return false;
				}
			}
		}
	}
	function changeTotOil(){
		var el = document.getElementsByName('lb[]');
		var per = document.getElementsByName('percent[]');
		totOils = 0;
		for(i = 0; i < el.length; i++){
			totOils = parseInt(totOils) + parseInt(el[i].value);
		}
		document.getElementById('weightOil').value = totOils;

		for(i = 0; i < per.length; i++){
			per[i].value = Math.round(((parseInt(el[i].value) / parseInt(totOils)) * 100) * 100) / 100;
		}


	}
	function delVal(val1,val2,val3,val4){
		document.getElementById(val1).innerHTML = '';
		document.getElementById(val2).innerHTML = '';
		document.getElementById(val3).innerHTML = '';
		document.getElementById(val4).innerHTML = '';			
	}
</script>
	</div>
</div>