<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$istop=true;

$topwidth=398;
$topwidth2=97;
$topmaxwidth=890;
$topalign=10;

include("header.php");
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;
	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

//메뉴얼
function shop_menual() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

function DescVender_popup() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

function DescPaygate_popup() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

function webftp_popup() {
	window.open("design_webftp.popup.php","webftppopup","height=10,width=10");
}

function SendSMS() {
	window.open("sendsms.php","sendsmspop","width=220,height=350,scrollbars=no");
}

function MemberMemo() {
	window.open("member_memoconfirm.php","memopop","width=250,height=120,scrollbars=no");
}

var menuno = 9;
var imagename = new Array('','01','02','03','04','05','06','07','08','09');

function GoMenu(no, url) {
	for(i=1;i<=menuno;i++){
		if(no==i) document.top.image[no-1].src='img/common/topmenu_'+imagename[no]+'_view.gif';
		else document.top.image[i-1].src='img/common/topmenu_'+imagename[i]+'_default.gif';
	}
	document.top.clickmenu.value=no;
	parent.bodyframe.location.href=url;
	if(no==0) location.reload();
}

function ChangeMenuImg(no) {
	for(i=1;i<=menuno;i++){
		if(no==i) document.top.image[no-1].src='img/common/topmenu_'+imagename[no]+'_view.gif';
		else document.top.image[i-1].src='img/common/topmenu_'+imagename[i]+'_default.gif';
	}
	document.top.clickmenu.value=no;
}

function MouseOver(obj,no) {
	var clickmenu = document.top.clickmenu.value;
	obj.filters.blendTrans.stop();
	obj.filters.blendTrans.Apply();
	obj.src = 'img/common/topmenu_'+imagename[no]+'_view.gif';
	obj.filters.blendTrans.Play();

}

function MouseOut(obj,no) {
	var clickmenu = document.top.clickmenu.value;
	obj.filters.blendTrans.stop();
	obj.filters.blendTrans.Apply();
	obj.src = 'img/common/topmenu_'+imagename[no]+'_default.gif';
	if(clickmenu!=0 && no==clickmenu) {
		obj.src = 'img/common/topmenu_'+imagename[no]+'_view.gif';
	} else if(clickmenu!=0 && no!=clickmenu) {
		document.top.image[clickmenu-1].src = 'img/common/topmenu_'+imagename[clickmenu]+'_view.gif';
	}
	obj.filters.blendTrans.Play();
}

//-->
</SCRIPT>

<script type="text/javascript" src="http://<?=_SellerUrl?>/incomushop/global.js"></script>

<link rel="styleSheet" href="/css/admin.css" type="text/css">

<table cellpadding="0" cellspacing="0" width="100%" >
<tr>
	<td width="100%" valign="top" >
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td bgcolor=#35353f >
		<table cellpadding="0" cellspacing="0" width="980">
		<colgroup>
			<col width="280" /><col width="700" />
		</colgroup>
		<tr> 
			<td class="pl_15"><A HREF="javascript:GoMenu(0,'main.php');"><IMG SRC="img/common/admin_logo.gif" border="0"></a></td>
			<td align=right>

				<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="top_menu_font">
					<?php if(setUseVender()) {?>
					<a href="javascript:GoMenu(1,'vender_index.php');" style="text-decoration:none;"><span style="letter-spacing:-0.5pt;"><font color="#2A6BC3" style="font-size:11px;"><B><font color="#999999">ㆍ</font>입점관리</B></font></span></a>
					<?php } else { ?>
					<a href="javascript:alert('입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.');" style="text-decoration:none;">입점관리</a>
					<?php } ?>

					<a href="javascript:webftp_popup();">FTP</a>
					<a href="javascript:SendSMS();">SMS</a>
					<a href="javascript:MemberMemo();">MEMO</a><?/*?>
					<a href="javascript:shop_menual();">메뉴얼</a><?*/?>
					<a href="javascript:GoMenu(0,'sitemap.php');">SITEMAP</a>
					<a href="javascript:GoMenu(0,'main.php');">처음으로</a>
					<a href="http://<?=$shopurl?>" name="shopurl" target="_blank"><img src="img/icon/icon_home01.gif" alt="" /> <span style="color:#fff;">my shop</span></a>
					<a href="logout.php"><img  src="img/icon/icon_logout01.gif" alt="" /> <span style="color:#fff;">접속종료</span></a>
					</td>
				</tr>
				</table>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>

		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">

			<div class="top_menu_wrap">
			<table cellpadding="0" cellspacing="0">
			<form name="top" method="post">
			<tr>
				<td><p><a href="javascript:GoMenu(1,'shop_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_01_default.gif" border="0" name="image" alt="상점관리" onMouseOut="MouseOut(this,1)" onMouseOver="MouseOver(this,1)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(2,'design_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_02_default.gif" border="0" name="image" onMouseOut="MouseOut(this,2)" onMouseOver="MouseOver(this,2)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(3,'member_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_03_default.gif" border="0" name="image" onMouseOut="MouseOut(this,3)" onMouseOver="MouseOver(this,3)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(4,'product_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_04_default.gif" border="0" name="image" onMouseOut="MouseOut(this,4)" onMouseOver="MouseOver(this,4)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(5,'order_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_05_default.gif" border="0" name="image" onMouseOut="MouseOut(this,5)" onMouseOver="MouseOver(this,5)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(6,'gong_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_06_default.gif" border="0" name="image" onMouseOut="MouseOut(this,6)" onMouseOver="MouseOver(this,6)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(7,'market_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_07_default.gif" border="0" name="image" onMouseOut="MouseOut(this,7)" onMouseOver="MouseOver(this,7)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(8,'community_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_08_default.gif" border="0" name="image" onMouseOut="MouseOut(this,8)" onMouseOver="MouseOver(this,8)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(9,'counter_index.php');" HIDEFOCUS="true"><img src="img/common/topmenu_09_default.gif" border="0" name="image" onMouseOut="MouseOut(this,9)" onMouseOver="MouseOver(this,9)" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
			</tr>
			<input type="hidden" name="clickmenu" value="0">
			</form>
			</table>
			</div>

			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
	</td>
</tr>
</table>



</body>
</html>
