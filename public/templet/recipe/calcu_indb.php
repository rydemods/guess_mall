<!-- start container -->
<div id="container">
<? include dirname(__FILE__)."/side.php" ; ?> 
	<!-- start contents -->
	<div class="contents_side">

<style>
	.tbl1{
		font-size:14px;
	}
</style>
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

<div id = 'ptrDiv' style="margin-left:15px">

<table width="764" border="0" cellspacing="0" cellpadding="0">
<tr><td><img src="/shop/data/img1/cal_title.gif"/></td></tr>
 <tr id = 'printShow' style = 'display:none;'>
    <td><table width="764" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="764" style = 'font:bold 14px 돋움;color:#303030;' align = 'center'>
			<A HREF="#"  onMouseOver="window.status=('print'); return true;" onClick="return window.print()">
				<img src="/shop/data/img1/res_02.gif" width="98" height="24" border="0" />
			</A>
		</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
	<table width="850" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="156"><img src="/shop/data/img1/res_01.gif" width="156" height="24" /></td>
        <td width="510" style = 'font:bold 14px 돋움;color:#303030;'><?=$titleReci?></td>
        <td width="98" id = 'hidePrint'><a href="javascript:printWindow()"><img src="/shop/data/img1/res_02.gif" width="98" height="24" border="0" /></a></td>
      </tr>
    </table></td>
  </tr>
    <tr>
    <td height="8"></td>
  </tr>
    <tr>
    <td height="100" bgcolor="#eff9f4">
	
	
	<table width="850" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="156"><img src="/shop/data/img1/res_11.gif" width="156" height="25" /></td>
        <td width="220" align="center" background="/shop/data/img1/res_bg1.gif"><?=$weightoil?>g</td>
        <td width="156"><img src="/shop/data/img1/res_12.gif" width="156" height="25" /></td>
        <td width="220" align="center" background="/shop/data/img1/res_bg1.gif"><?=$waterperoil?>%</td>
      </tr>
      <tr>
        <td><img src="/shop/data/img1/res_13.gif" width="156" height="25" /></td>
        <td align="center" background="/shop/data/img1/res_bg1.gif"><?=$superfat?>%</td>
        <td><img src="/shop/data/img1/res_14.gif" width="156" height="25" /></td>
        <td align="center" background="/shop/data/img1/res_bg1.gif"><?=($weightoil * $waterperoil / 100)?>g</td>
      </tr>
      <tr>
        <td><img src="/shop/data/img1/res_06.gif" width="156" height="25" /></td>
        <td colspan="3" background="/shop/data/img1/res_bg2.gif" style="padding-left:5px;"><?=$intoMemo?></td>
        </tr>
    </table>
	</td>
  </tr>
    <tr>
    <td>&nbsp;</td>
  </tr>
     <tr>
    <td>

   <div style="border:2px solid #777;padding:10px 0">
<p><img src="/shop/data/img1/res_07.gif"/></p>
		<table width="850" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="261" valign="top"><table width="261" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td><img src="/shop/data/img1/res_08.gif" width="261" height="25" /></td>
          </tr>
          <tr>
            <td height="26" align="right" background="/shop/data/img1/res_09.gif">
            <table width="126" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><?=($weightoil * $waterperoil / 100)?>g</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="26" background="/shop/data/img1/res_10.gif">
            <table width="261" border="0" cellspacing="0" cellpadding="0">

              <tr>
			   <td width="130" align="center">
				<?if($chkoh == 'chkNAOH'){?>
				NaOH</td><td width="130" align="center"><?=round($totNaoh - ($totNaoh - ($totNaoh*((100-$superfat) / 100))), 3)?>g
				<?}else{?>
				KOH</td><td  width="130" align="center"><?=round($totKoh - ($totKoh - ($totKoh*((100-$superfat) / 100))), 3)?>g
				<?}?>					
				</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
        <td width="31" background="/shop/data/img1/res_bg00.gif">&nbsp;</td>
        <td width="442" valign="top" ><table width="442" border="0" cellspacing="0" cellpadding="0">
		 <?
		 $i=0;
		 foreach($list as $data){
			 $i++;
			?>
          <tr>
            <td width="278" height="25" background="/shop/data/img1/res_bg6.gif" style="padding-left:5px;"><?=$i?>. <?=$data[name]?></td>
            <td width="84" align="center" background="/shop/data/img1/res_bg7.gif"><?=$data[lb]?>g</td>
            <td width="80" align="center" background="/shop/data/img1/res_bg7.gif"><?=$data[percent]?>%</td>
          </tr>
		  <?}?>
          <tr>
            <td colspan="3">&nbsp;</td>
            </tr>
          <tr>
            <td height="31" colspan="3" align="center" bgcolor="#666666" style = 'font:bold 14px 돋움;color:#FFFFFF;'>
            물+가성소다+원료 = 
							<?if($chkoh == 'chkNAOH'){?>
							<?= round($totNaoh - ($totNaoh - ($totNaoh*((100-$superfat) / 100))), 3)  + round($weightoil * $waterperoil / 100, 3) + $weightoil?>g
							<?}else{?>
							<?= round($totKoh - ($totKoh - ($totKoh*((100-$superfat) / 100))), 3) + round($weightoil * $waterperoil / 100, 3) + $weightoil ?>g
							<?}?>
            </td>
            </tr>
        </table></td>
      </tr>
    </table>
    </div>
	
	
	</td>
  </tr>
     <tr>
    <td>&nbsp;</td>
  </tr>
     <tr>
    <td>

<div style="background:#f7f7f7">
	<table width="850" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="129"><img src="/shop/data/img1/cal_13.gif" width="129" height="144" /></td>
    <td width="621" background="/shop/data/img1/cal_line3.gif" ><table width="621" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="10"></td>
      </tr>
      <tr>
        <td height="40"><table width="621" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>
<a href="#" onmouseover="doTooltip(event,1)" onmouseout="hideTip()">
<img src="/shop/data/img1/cal_14.gif" width="71" height="20" border="0" /></a></td>
            <td width="8" rowspan="2"><img src="/shop/data/img1/cal_line1.gif" width="8" height="40" /></td>
            <td>
<a href="#" onmouseover="doTooltip(event,2)" onmouseout="hideTip()">
<img src="/shop/data/img1/cal_15.gif" width="71" height="20" border="0" /></a></td>
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
                                                <td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif" height="22"><?=$totLauric ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totMyristic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totPalmitic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totStearic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totRicinoleic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totOleic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totLinoleic ?></td>
			<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totLinolenic ?></td>
          </tr>
        </table></td>
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
						<?foreach($list as $data){?>
						<?if(($data[Hardness] + $data[Cleansing] + $data[Conditions] + $data[Bubbly] + $data[Creamy]) == 0){?>
							[<?=$data[name]?>]
							<?$result = '1';?>
						<?}?>
						<?}?>
						<? if($result == '1') echo "의 품질 값이 없어 정확한 값이 아닐 수 있습니다."; ?>
			</td>
          </tr>
          <tr>
						<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif" height="22"><?=$totHardness ?></td>
						<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totCleansing ?></td>
						<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totConditions ?></td>
						<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totBubbly ?></td>
						<td style = 'font-size:14px;'  align="center" background="/shop/data/img1/res_bg8.gif"><?=$totCreamy ?></td>
          </tr>
        </table>
</td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
      <tr>
        <td><img src="/shop/data/img1/cal_27.gif" width="622" /></td>
      </tr>
    </table></td>
    <td width="14" bgcolor="#F7F7F7">&nbsp;</td>
  </tr>
</table>	
</div>

</td>
  </tr>
          <tr>
        <td height="15"></td>
      </tr>
      <tr>
        <td height="2" bgcolor="#E6E6E6"></td>
      </tr>
     <tr>
    <td align="right"><input type = 'image' src='/shop/data/img1/res_16.gif' style = 'cursor:pointer;' onClick = 'javascript:history.back();return false'></td>
  </tr>
     <tr>
    <td>&nbsp;</td>
  </tr>
</table>

</div>
<SCRIPT LANGUAGE='JavaScript'>
	function printWindow(){
		bV = parseInt(navigator.appVersion);
		document.getElementById('hidePrint').style.display = 'none';
		document.getElementById('printShow').style.display = '';
		var ContentIDX ="ptrDiv";
		var printContent = document.getElementById(ContentIDX ).innerHTML;
	  
		var url;
		var envwin;

		//width=650,height=800 이거를 수정하시면 팝업의 사이즈를 조정할수 있습니다.

		envwin = "width=790,height=800,menubar=0,resizable=1,scrollbars=1,status=1";
	 
		tm = window.open("","printform",envwin)
		tm.document.open();
	 
	   tm.document.write("<html>");
		tm.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-kr\">");
		tm.document.write("<link rel='styleSheet' href='../../style.css'>");

		tm.document.write("<head>");
		tm.document.write("<link rel='styleSheet' href='../style.css'>");
		tm.document.write("</head>");
	   tm.document.write("<body>");

	   tm.document.write("<table width=\"588\">");
	   //상단에 출력하기 버튼
	   //내용을 출력하는 부분
	   tm.document.write("<tr>");
	   tm.document.write("<td align=\"center\">");
	   tm.document.write( printContent );
	   tm.document.write("</td>");
	   tm.document.write("</tr>");


	   //하단에 출력하기 버튼
	   tm.document.write("<tr>");
	   tm.document.write("<td align=\"center\">");
	   tm.document.write("<A HREF=\"#\"  onMouseOver=\"window.status=('print'); return true;\" onClick=\"return window.print()\">");
	   tm.document.write("<img src = '/shop/data/img1/res_02.gif'>");
	   tm.document.write("</a>");
	   tm.document.write("</td>");
	   tm.document.write("</tr>");



	   tm.document.write("</table>");
	   tm.document.write("</body>");
	   tm.document.write("</html>");
	   tm.document.close();


		document.getElementById('printShow').style.display = 'none';
		document.getElementById('hidePrint').style.display = '';
	}
</script>
	</div>
</div>