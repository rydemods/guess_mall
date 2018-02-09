<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "order_list.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "order_delay.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "order_delisearch.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "order_namesearch.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "order_monthsearch.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "order_tempinfo.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "order_excelinfo.php":
		$menuidx = "shop1"; $idx[0][6] = 'YES'; break;
	case "order_csvdelivery.php":
		$menuidx = "shop1"; $idx[0][7] = 'YES'; break;

	case "order_basket.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "order_allsale.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "order_eachsale.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "order_profit.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;

	case "order_taxsaveabout.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "order_taxsaveconfig.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "order_taxsavelist.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "order_taxsaveissue.php":
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
<DIV id="scrollingLeft" style="VISIBILITY:visible;POSITION:absolute;">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" bgcolor="#FFFFFF">
<TR>
	<TD><img src="images/leftmenu_nero.gif" border="0"></TD>
</TR>
<TR>
	<TD height="50" align="right" valign="top" background="images/order_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">주문조회 및 배송관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop1');">주문조회 및 배송관리</td>
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
			noselectmenu('일자별 주문조회/배송','order_list.php',$idx[0][0],0);
			noselectmenu('미배송/미입금 주문관리','order_delay.php',$idx[0][1],1);
			noselectmenu('배송/입금일별 주문조회','order_delisearch.php',$idx[0][2],1);
			noselectmenu('이름/가격별 외 주문조회','order_namesearch.php',$idx[0][3],1);
			noselectmenu('개월별 상품명 주문조회','order_monthsearch.php',$idx[0][4],1);
			noselectmenu('결제시도 주문서 관리','order_tempinfo.php',$idx[0][5],1);
			noselectmenu('주문리스트 엑셀파일 관리','order_excelinfo.php',$idx[0][6],1);
			noselectmenu('주문리스트 일괄배송 관리','order_csvdelivery.php',$idx[0][7],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">장바구니 및 매출 분석</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop2');">장바구니 및 매출 분석</td>
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
			noselectmenu('장바구니 상품분석','order_basket.php',$idx[1][0],0);
			noselectmenu('전체상품 매출분석','order_allsale.php',$idx[1][1],1);
			noselectmenu('개별상품 매출분석','order_eachsale.php',$idx[1][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">현금영수증 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop3');">현금영수증 관리</td>
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
			noselectmenu('현금영수증 제도란?','order_taxsaveabout.php',$idx[2][0],0);
			noselectmenu('현금영수증 환경설정','order_taxsaveconfig.php',$idx[2][1],1);
			noselectmenu('현금영수증 발급/조회','order_taxsavelist.php',$idx[2][2],1);
			noselectmenu('현금영수증 개별발급','order_taxsaveissue.php',$idx[2][3],2);
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
