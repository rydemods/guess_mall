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
	window.open("member_memoconfirm.php","memopop","width=300,height=220,scrollbars=no");
}
function ClearCache(flag) {
    $.ajax({
        type: "GET",
        url: "http://www.shinwonmall.com/admin/clear_cache.php",
    }).done(function(msg) {
    });
/*
    $.ajax({
        type: "GET",
        url: "http://52.231.36.21/admin/clear_cache.php",
    }).done(function(msg) {
    });
    $.ajax({
        type: "GET",
        url: "http://52.231.37.17/admin/clear_cache.php",
    }).done(function(msg) {
    });
*/
	if (flag != 'N')
	{
		alert('초기화했습니다.');
	}
}

var menuno = 11;
var imagename = new Array('','01','02','03','04','05','06','07','08','09','10','11','12');

function GoMenu(no, url) {
	for(i=0;i<menuno;i++){
		if(no-1==i) $("#menu_tr").find("td").eq(i).find("img").attr("src",'img/common/topmenu_'+imagename[no]+'_view.gif');
		else $("#menu_tr").find("td").eq(i).find("img").attr("src",'img/common/topmenu_'+imagename[i+1]+'_default.gif');
	}
	document.top.clickmenu.value=no;
	parent.bodyframe.location.href=url;
	if(no==0) location.reload();
}

function ChangeMenuImg(no) {
	for(i=1;i<=menuno;i++){
		//if(no==i) document.top.image[no-1].src='img/common/topmenu_'+imagename[no]+'_view.gif';
		//else document.top.image[i-1].src='img/common/topmenu_'+imagename[i]+'_default.gif';
	}
	//document.top.clickmenu.value=no;
}

$(document).ready(function(){
	$(".top_menu_over").mouseover(function(){
		rowIdx = parseInt($(this).closest("td").prevAll().length)+1;
		$(this).attr("src",'img/common/topmenu_'+imagename[rowIdx]+'_view.gif');
	}).mouseout(function(){
		rowIdx = parseInt($(this).closest("td").prevAll().length)+1;
		nowIdx = $("#clickmenu").val();
		if( nowIdx != (rowIdx) )
		$(this).attr("src",'img/common/topmenu_'+imagename[rowIdx]+'_default.gif');
	});
});

//-->
</SCRIPT>

<script type="text/javascript" src="http://<?=_SellerUrl?>/incomushop/global.js"></script>

<link rel="styleSheet" href="/css/admin.css" type="text/css">

<table cellpadding="0" cellspacing="0" width="100%" >
<tr>
	<td width="100%" valign="top" >
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td bgcolor="#000000">
		<table cellpadding="0" cellspacing="0" width="980">
		<colgroup>
			<col width="280" /><col width="700" />
		</colgroup>
		<tr height="35">
			<td class="pl_15"><A HREF="javascript:GoMenu(0,'main.php');"><IMG SRC="img/common/admin_logo.png" border="0"></a></td>
			<td align=right>

				<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="top_menu_font">
					<!-- <a href="javascript:webftp_popup();">FTP</a>
					<a href="javascript:SendSMS();">SMS</a>
					<a href="javascript:MemberMemo();">MEMO</a>
					<a href="javascript:GoMenu(0,'sitemap.php');">SITEMAP</a> -->
					<a href="javascript:GoMenu(0,'main.php');">HOME</a>
					<a href="javascript:ClearCache();">캐시초기화</a>
					<a href="http://<?=$shopurl?>" name="shopurl" target="_blank" class="bg c1"><!--<img src="img/icon/icon_home01.gif" alt="" />--> <span style="color:#fff;">My Shop</span></a>
					<a href="logout.php" class="bg"><!--<img  src="img/icon/icon_logout01.gif" alt="" />--> <span style="color:#fff;">로그아웃</span></a>
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
			<tr id="menu_tr">
				<td><p><a href="javascript:GoMenu(1,'shop_basicinfo.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_01_default.gif" border="0" name="image" alt="상점관리" style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(2,'main_banner_mng.php?no=113');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_02_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
                <td><p><a href="javascript:GoMenu(3,'member_list.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_03_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(4,'product_code_product.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_04_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(5,'order_list_all.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_05_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td style="display: none;"><p><a href="javascript:GoMenu(6,'gong_displayset.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_06_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(7,'market_eventpopup.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_07_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				<td><p><a href="javascript:GoMenu(8,'community_lookbook_list.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_08_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				
				<td><p><a href="javascript:GoMenu(9,'counter_timevisit.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_09_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></a></p></td>
				
				<td><p><a href="javascript:GoMenu(10,'cscenter_order_list_all_order.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_10_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></p></td>

				<td><p><a href="javascript:GoMenu(11,'vender_management2.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_11_default.gif" border="0" name="image" alt="브랜드관리" style="filter:blendTrans(duration=0.3)"></a></p></td>

				<!--<td><p><a href="javascript:GoMenu(11,'signage_store_list.php');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_11_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></p></td>-->

				<!--<td><p><a href="javascript:GoMenu(12,'#');" HIDEFOCUS="true"><img class="top_menu_over" src="img/common/topmenu_12_default.gif" border="0" name="image"  style="filter:blendTrans(duration=0.3)"></p></td>-->
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
				<td><p></p></td>
			</tr>
			<input type="hidden" name="clickmenu" id="clickmenu" value="0">
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
