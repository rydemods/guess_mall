<?php
/********************************************************************* 
// 파 일 명		: menu_vender.php 
// 설     명		: 입점관리 메뉴
// 상세설명	: 관리자 입점관리의 메뉴를 설정
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 메뉴에 대해 정보를 설정한다.
#---------------------------------------------------------------
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "vender_new.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "vender_management.php":
	case "vender_infomodify.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "vender_notice.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "vender_counsel.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "vender_mailsend.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "vender_smssend.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;

	case "vender_prdtlist.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "vender_prdtallupdate.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "vender_prdtallsoldout.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;

	case "vender_orderlist.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "vender_orderadjust.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "vender_calendar.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3');
var thisshop="<?=$menuidx?>";
ino=3;

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

<div id=scrollingLeftParent style="position:relative;">
<DIV id=scrollingLeft style='LEFT: 0px; VISIBILITY: visible; WIDTH: 190px; POSITION: absolute'>
<TABLE WIDTH=190 BORDER=0 CELLPADDING=0 CELLSPACING=0 bgcolor=white>
<TR>
	<TD width="190" height="12"><p align="center"><img src="images/leftmenu_nero.gif" width="190" height="12" border="0"></TD>
</TR>
<TR>
	<TD width="190" height="50" valign="top" background="images/vender_leftmenu_title.gif" style="padding-top:10pt; padding-right:5pt;"><p align="right"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" width="27" height="15" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" width="28" height="15" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH=190 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD background="images/shop_leftmenu_leftbg.gif" valign="top"><IMG SRC="images/shop_leftmenu_leftbg.gif" WIDTH=16 HEIGHT="2" ALT=""></TD>
		<TD width="158" valign="top" height="200">
		<table cellpadding="0" cellspacing="0" width="158" id=tblashop1>
		<tr>
			<td width="158" height="28" onClick="ChangeMenu('shop1');" style="cursor:hand;"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>입점업체 관리</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop1 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;cursor:hand;" onClick="ChangeMenu('shop1');"><p>입점업체 관리</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158">
		<tr>
			<td width="158">
			<div id=shop1 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table cellpadding="0" cellspacing="0" width="158" bgcolor="#DFF2FF">										
<?php
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
			}
			noselectmenu('입점업체 신규등록','vender_new.php',$idx[0][0],0);
			noselectmenu('입점업체 정보관리','vender_management.php',$idx[0][1],2);
			/**************************** 필요없는 메뉴 삭제 (김재수 - 2015.10.23) ****************************/
			/** 메뉴 임시 해제 2015 10 28 유동혁 **/
			noselectmenu('입점업체 공지사항','vender_notice.php',$idx[0][2],1);
			noselectmenu('입점업체 상담게시판','vender_counsel.php',$idx[0][3],1);
			noselectmenu('E-mail 발송','vender_mailsend.php',$idx[0][4],1);
			noselectmenu('SMS 문자전송','vender_smssend.php',$idx[0][5],2);
			/***********************************************************************************************/
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblashop2>
		<tr>
			<td width="158" height="28" onClick="ChangeMenu('shop2');" style="cursor:hand;"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>입점상품 관리</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop2 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;cursor:hand;" onClick="ChangeMenu('shop2');"><p>입점상품 관리</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158">
		<tr>
			<td width="158">
			<div id=shop2 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table cellpadding="0" cellspacing="0" width="158" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop2") {
				echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
			}
			noselectmenu('입점업체 상품목록','vender_prdtlist.php',$idx[1][0],3);
			/**************************** 필요없는 메뉴 삭제 (김재수 - 2015.10.23) ****************************/
			//noselectmenu('상품 일괄 간편수정','vender_prdtallupdate.php',$idx[1][1],1);
			//noselectmenu('품절상품 일괄 삭제/관리','vender_prdtallsoldout.php',$idx[1][2],2);
			/***********************************************************************************************/
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblashop3>
		<tr>
			<td width="158" height="28" onClick="ChangeMenu('shop3');" style="cursor:hand;"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle><span style="letter-spacing:-0.5pt;">주문/정산 관리</span></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop3 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;cursor:hand;" onClick="ChangeMenu('shop3');"><span style="letter-spacing:-1.5pt;">주문/정산 관리</span></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158">
		<tr>
			<td width="158">
			<div id=shop3 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table cellpadding="0" cellspacing="0" width="158" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop3") {
				echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
			}
			noselectmenu('입점업체 주문조회','vender_orderlist.php',$idx[2][0],3);
			/**************************** 필요없는 메뉴 삭제 (김재수 - 2015.10.23) ****************************/
			/** 메뉴 임시 해제 2015 10 28 유동혁 **/
			noselectmenu('입점업체 정산관리','vender_orderadjust.php',$idx[2][1],1);
			noselectmenu('입점업체 정산 캘린더','vender_calendar.php',$idx[2][2],2);
			/***********************************************************************************************/
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		</TD>
		<TD background="images/shop_leftmenu_rightbg.gif" valign="top"><IMG SRC="images/shop_leftmenu_rightbg.gif" WIDTH=16 HEIGHT="3" ALT=""></TD>
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD><IMG SRC="images/shop_leftmenu_down.gif" WIDTH=190 HEIGHT=23 ALT=""></TD>
</TR>
</TABLE>
</DIV>
</div>
<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>
