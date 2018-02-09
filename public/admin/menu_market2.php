<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "market_notice.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "market_contentinfo.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "market_survey.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "market_partner.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "market_affiliatebanner.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "market_enginepage.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;

	case "market_eventpopup.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "market_quickmenu.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "market_newproductview.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "market_eventcode.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;
	case "market_eventbrand.php":
		$menuidx = "shop2"; $idx[1][4] = 'YES'; break;
	case "market_eventprdetail.php":
		$menuidx = "shop2"; $idx[1][5] = 'YES'; break;

	case "market_couponnew.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "market_couponsupply.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "market_couponlist.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;

	case "market_smsconfig.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "market_smssendlist.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "market_smssinglesend.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "market_smsgroupsend.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;
	case "market_smsaddressbook.php":
		$menuidx = "shop4"; $idx[3][4] = 'YES'; break;
	case "market_smsfill.php":
		$menuidx = "shop4"; $idx[3][5] = 'YES'; break;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3','shop4');
var thisshop="<?=$menuidx?>";
ino=4;

function Change(){
	if(document.all){
		for(i=0;i<ino;i++) {
			document.all(layerlist[i]).style.display="none";
		}
		stobj="document.all(shop).style";
	} else if(document.getElementById){
		for(i=0;i<ino;i++) {
			document.getElementById(layerlist[i]).style.display="none";
		}
		stobj="document.getElementById(shop).style";
	} else if(document.layers){
		for(i=0;i<ino;i++) {
			document.layers[layerlist[i]].display=none;
		}
		stobj="document.layers[shop]";
	}
}

function ChangeMenu(shop){
	if ( thisshop !== shop){
		Change();
		eval(stobj).display="block";
		thisshop=shop;
	} else{
		Change();
		//eval(stobj).display="block";
		thisshop=stobj;
	}
}

function InitMenu(shop) {
	try {
		tblashop = "tbla".concat(shop);
		tblbshop = "tblb".concat(shop);
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	} catch (e) {
		shop = "shop1";
		tblashop = "tblashop1";
		tblbshop = "tblbshop1";
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	}
}
//-->
</SCRIPT>

<div id="scrollingLeftParent" style="position:relative;">
<div id="scrollingLeft" style="VISIBILITY:visible;POSITION:absolute;">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" bgcolor="#FFFFFF">
<TR>
	<TD><img src="images/leftmenu_nero.gif" border="0"></TD>
</TR>
<TR>
	<TD height="50" align="right" valign="top" background="images/market_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD background="images/shop_leftmenu_leftbg.gif"></TD>
		<TD valign="top">
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop1">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">마케팅 지원</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" height="19" onClick="ChangeMenu('shop1');">마케팅 지원</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('공지사항 관리','market_notice.php',$idx[0][0],0);
			noselectmenu('정보(information)관리','market_contentinfo.php',$idx[0][1],1);
			noselectmenu('온라인투표 관리','market_survey.php',$idx[0][2],1);
			noselectmenu('제휴마케팅 관리','market_partner.php',$idx[0][3],1);
			noselectmenu('Affiliate 배너관리','market_affiliatebanner.php',$idx[0][4],1);
			noselectmenu('가격비교사이트 관리','market_enginepage.php',$idx[0][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">이벤트/사은품 기능 설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop2');">이벤트/사은품 기능 설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop2" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('팝업 이벤트 관리','market_eventpopup.php',$idx[1][0],0);
			noselectmenu('Quick메뉴 관리','market_quickmenu.php',$idx[1][1],1);
			noselectmenu('최근 본 상품 관리','market_newproductview.php',$idx[1][2],1);
			noselectmenu('카테고리별 이벤트 관리','market_eventcode.php',$idx[1][3],1);
			noselectmenu('브랜드별 이벤트 관리','market_eventbrand.php',$idx[1][4],1);
			noselectmenu('상품상세 공통이벤트 관리','market_eventprdetail.php',$idx[1][5],1);
			noselectmenu('고객 사은품 등록/관리','product_giftlist.php',$idx[1][6],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">쿠폰발생 서비스 설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop3');">쿠폰발생 서비스 설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop3" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop3") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('새로운 쿠폰 생성하기','market_couponnew.php',$idx[2][0],0);
			noselectmenu('생성된 쿠폰 즉시발급','market_couponsupply.php',$idx[2][1],1);
			noselectmenu('발급된 쿠폰 내역관리','market_couponlist.php',$idx[2][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">SMS 발송/관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop4');">SMS 발송/관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop4" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop4") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('SMS 기본환경 설정','market_smsconfig.php',$idx[3][0],0);
			noselectmenu('SMS 발송내역 관리','market_smssendlist.php',$idx[3][1],1);
			noselectmenu('SMS 개별 발송','market_smssinglesend.php',$idx[3][2],1);
			noselectmenu('SMS 등급/단체 발송','market_smsgroupsend.php',$idx[3][3],1);
			noselectmenu('SMS 주소록 관리','market_smsaddressbook.php',$idx[3][4],1);
			noselectmenu('SMS 충전하기','market_smsfill.php',$idx[3][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		</TD>
		<TD background="images/shop_leftmenu_rightbg.gif"></TD>
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD><IMG SRC="images/shop_leftmenu_down.gif" border="0"></TD>
</TR>
</TABLE>
</div>
</div>
<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>
