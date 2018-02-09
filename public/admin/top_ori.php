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
var imagename = new Array('','shop','design','member','product','order','auction','market','community','counter');

function GoMenu(no, url) {
	for(i=1;i<=menuno;i++){
		if(no==i) document.top.image[no-1].src='images/topmenu_'+imagename[no]+'_view.gif';
		else document.top.image[i-1].src='images/topmenu_'+imagename[i]+'_default.gif';
	}
	document.top.clickmenu.value=no;
	parent.bodyframe.location.href=url;
	if(no==0) location.reload();
}

function ChangeMenuImg(no) {
	for(i=1;i<=menuno;i++){
		if(no==i) document.top.image[no-1].src='images/topmenu_'+imagename[no]+'_view.gif';
		else document.top.image[i-1].src='images/topmenu_'+imagename[i]+'_default.gif';
	}
	document.top.clickmenu.value=no;
}

function MouseOver(obj,no) {
	var clickmenu = document.top.clickmenu.value;
	obj.filters.blendTrans.stop();
	obj.filters.blendTrans.Apply();
	obj.src = 'images/topmenu_'+imagename[no]+'_view.gif';
	obj.filters.blendTrans.Play();
	
}

function MouseOut(obj,no) {
	var clickmenu = document.top.clickmenu.value;
	obj.filters.blendTrans.stop();
	obj.filters.blendTrans.Apply();
	obj.src = 'images/topmenu_'+imagename[no]+'_default.gif';
	if(clickmenu!=0 && no==clickmenu) {
		obj.src = 'images/topmenu_'+imagename[no]+'_view.gif';
	} else if(clickmenu!=0 && no!=clickmenu) {
		document.top.image[clickmenu-1].src = 'images/topmenu_'+imagename[clickmenu]+'_view.gif';
	}
	obj.filters.blendTrans.Play();
}

//-->
</SCRIPT>

<script type="text/javascript" src="http://<?=_SellerUrl?>/incomushop/global.js"></script>

<table cellpadding="0" cellspacing="0" width="100%" background="images/top_bg.gif">
<tr>
	<td style="padding-left:10px;"></td>
	<td width="100%" valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="padding-left:30px;"><A HREF="javascript:GoMenu(0,'main.php');"><IMG SRC="images/logo.gif" border="0"></a></td>
			<td width="100%" style="padding-right:46pt;">
			<div align="left">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding-left:150px;"></td>
				<td>
				<?php if(setUseVender()) {?>
				<a href="javascript:GoMenu(1,'vender_index.php');" style="text-decoration:none;"><span style="letter-spacing:-0.5pt;"><font color="#2A6BC3" style="font-size:11px;"><B><font color="#999999">ㆍ</font>입점관리</B></font></span></a>
				<?php } else { ?>
				<a href="javascript:alert('입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.');" style="text-decoration:none;"><span style="letter-spacing:-0.5pt;"><font color="#2A6BC3" style="font-size:11px;"><B><font color="#999999">ㆍ</font>입점관리</B></font></span></a>
				<?php } ?>
				
				<A HREF="javascript:webftp_popup();"><img src="images/top_menu_ftp.gif" border="0" align=absmiddle style="margin-bottom:2px;"></A><A HREF="javascript:SendSMS();"><img src="images/top_menu_sms.gif" border="0" align=absmiddle style="margin-bottom:2px;"></A><A HREF="javascript:MemberMemo();"><img src="images/top_menu_memo.gif" border="0" align=absmiddle style="margin-bottom:2px;"></A><?/*?><A HREF="javascript:shop_menual();"><img src="images/top_menu_menuer.gif" border="0" align=absmiddle style="margin-bottom:2px;"></A><?*/?><A HREF="javascript:GoMenu(0,'sitemap.php');"><img src="images/top_menu_sitemap.gif" border="0" align=absmiddle style="margin-bottom:2px;"></a><img width="30" height="0" border="0" align=absmiddle><A HREF="javascript:GoMenu(0,'main.php');"><img src="images/top_menu_home.gif" border="0" align=absmiddle style="margin-bottom:2px;"></a><a HREF="http://<?=$shopurl?>" name="shopurl" target=_blank><img src="images/top_menu_myshop.gif" border="0" align=absmiddle style="margin-bottom:2px;"></a><A HREF="logout.php"><img src="images/top_menu_logout.gif" border="0" align=absmiddle style="margin-bottom:2px;"></a></td>
			</tr>
			</table>
			</div>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="images/top_menuimg1.gif" border="0"></td>
			<td background="images/top_menu_bg.gif" width="100%">
			<table cellpadding="0" cellspacing="0">
			<form name="top" method="post">
			<tr>
				<td><a href="javascript:GoMenu(1,'shop_index.php');" HIDEFOCUS="true"><img src="images/topmenu_shop_default.gif" width="92" height="50" border="0" name="image" alt="상점관리" onMouseOut="MouseOut(this,1)" onMouseOver="MouseOver(this,1)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(2,'design_index.php');" HIDEFOCUS="true"><img src="images/topmenu_design_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,2)" onMouseOver="MouseOver(this,2)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(3,'member_index.php');" HIDEFOCUS="true"><img src="images/topmenu_member_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,3)" onMouseOver="MouseOver(this,3)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(4,'product_index.php');" HIDEFOCUS="true"><img src="images/topmenu_product_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,4)" onMouseOver="MouseOver(this,4)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(5,'order_index.php');" HIDEFOCUS="true"><img src="images/topmenu_order_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,5)" onMouseOver="MouseOver(this,5)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(6,'gong_index.php');" HIDEFOCUS="true"><img src="images/topmenu_auction_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,6)" onMouseOver="MouseOver(this,6)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(7,'market_index.php');" HIDEFOCUS="true"><img src="images/topmenu_market_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,7)" onMouseOver="MouseOver(this,7)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(8,'community_index.php');" HIDEFOCUS="true"><img src="images/topmenu_community_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,8)" onMouseOver="MouseOver(this,8)" style="filter:blendTrans(duration=0.3)"></a></td>
				<td><a href="javascript:GoMenu(9,'counter_index.php');" HIDEFOCUS="true"><img src="images/topmenu_counter_default.gif" width="92" height="50" border="0" name="image" onMouseOut="MouseOut(this,9)" onMouseOver="MouseOver(this,9)" style="filter:blendTrans(duration=0.3)"></a></td>
			</tr>
			<input type="hidden" name="clickmenu" value="0">
			</form>
			</table>
			</td>
			<td><IMG SRC="images/top_menuimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
	<td style="padding-left:40px;"></td>
</tr>
</table>
</body>
</html>
