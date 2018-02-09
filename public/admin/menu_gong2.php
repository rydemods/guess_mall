<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "gong_displayset.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "gong_auctionreg.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "gong_auctionlist.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "gong_gongchangereg.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "gong_gongchangelist.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "gong_gongfixset.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "gong_gongfixreg.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;
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

<div id="scrollingLeftParent" style="position:relative;">
<div id="scrollingLeft" style="VISIBILITY:visible;POSITION:absolute;">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" bgcolor="#FFFFFF">
<TR>
	<TD><img src="images/leftmenu_nero.gif" border="0"></TD>
</TR>
<TR>
	<TD height="50" align="right" valign="top" background="images/gong_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">경매/공동구매 화면설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop1');">경매/공동구매 화면설정</td>
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
			noselectmenu('경매/공동구매 화면설정','gong_displayset.php',$idx[0][0],3);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">쇼핑몰 경매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop2');">쇼핑몰 경매 관리</td>
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
			noselectmenu('경매상품 등록/수정','gong_auctionreg.php',$idx[1][0],0);
			noselectmenu('등록 경매 관리','gong_auctionlist.php',$idx[1][1],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">공동구매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop3');">공동구매 관리</td>
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
			noselectmenu('가격변동형 공구 등록/수정','gong_gongchangereg.php',$idx[2][0],0);
			noselectmenu('가격변동형 등록공구 관리','gong_gongchangelist.php',$idx[2][1],1);
			noselectmenu('가격고정형 공동구매 설정','gong_gongfixset.php',$idx[2][2],1);
			noselectmenu('가격고정형 공구구매 등록','gong_gongfixreg.php',$idx[2][3],2);
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
