<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "counter_index.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "counter_index_t.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;

	case "counter_timevisit.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "counter_dayvisit.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "counter_timepageview.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "counter_daypageview.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;
	case "counter_timeorder.php":
		$menuidx = "shop2"; $idx[1][4] = 'YES'; break;
	case "counter_dayorder.php":
		$menuidx = "shop2"; $idx[1][5] = 'YES'; break;

	case "counter_prcodeprefer.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "counter_productprefer.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "counter_prsearchprefer.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "counter_sitepageprefer.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;

	case "counter_domainrank.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "counter_searchenginerank.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "counter_searchkeywordrank.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;

	case "counter_timetotal.php":
		$menuidx = "shop5"; $idx[4][0] = 'YES'; break;
	case "counter_daytotal.php":
		$menuidx = "shop5"; $idx[4][1] = 'YES'; break;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5');
var thisshop="<?=$menuidx?>";
ino=5;

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
		thisshop = "shop1";
		tblashop = "tblashop1";
		tblbshop = "tblbshop1";
		document.all(thisshop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=thisshop.substring(4,5)-1;
	}
}
//-->
</SCRIPT>

<div id=scrollingLeftParent style="position:relative;">
<div id=scrollingLeft style="VISIBILITY:visible;WIDTH:100%;POSITION:absolute;TOP:0px;z-index=2">
<TABLE WIDTH=190 BORDER=0 CELLPADDING=0 CELLSPACING=0 bgcolor=white>
<TR>
	<TD width="190" height="12"><p align="center"><img src="images/leftmenu_nero.gif" width="190" height="12" border="0"></TD>
</TR>
<TR>
	<TD width="190" height="50" valign="top" background="images/counter_leftmenu_title.gif" style="padding-top:10pt; padding-right:5pt;"><p align="right"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" width="27" height="15" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" width="28" height="15" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH=190 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD background="images/shop_leftmenu_leftbg.gif" valign="top"><IMG SRC="images/shop_leftmenu_leftbg.gif" WIDTH=16 HEIGHT="2" ALT=""></TD>
		<TD width="158" valign="top">
		<table cellpadding="0" cellspacing="0" width="158" id=tblashop1>
		<tr>
			<td width="158" height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>접속통계 HOME</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop1 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;" class="1depth_select" style="cursor:hand;" onClick="ChangeMenu('shop1');"><p>접속통계 HOME</p></td>
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
			noselectmenu('접속통계 HOME','counter_index.php',$idx[0][0],1);
			noselectmenu('좌측메뉴 테스트','counter_index_t.php',$idx[0][1],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblashop2>
		<tr>
			<td width="158" height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>트래픽 분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop2 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;" style="cursor:hand;" onClick="ChangeMenu('shop2');"><p>트래픽 분석</p></td>
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
			noselectmenu('시간별 순 방문자','counter_timevisit.php',$idx[1][0],0);
			noselectmenu('일자별 순 방문자','counter_dayvisit.php',$idx[1][1],1);
			noselectmenu('시간별 페이지뷰','counter_timepageview.php',$idx[1][2],1);
			noselectmenu('일자별 페이지뷰','counter_daypageview.php',$idx[1][3],1);
			noselectmenu('시간별 주문시도건수','counter_timeorder.php',$idx[1][4],1);
			noselectmenu('일자별 주문시도건수','counter_dayorder.php',$idx[1][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>

		<table cellpadding="0" cellspacing="0" width="158" id=tblashop3>
		<tr>
			<td width="158" height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>고객 선호도 분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop3 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;" style="cursor:hand;" onClick="ChangeMenu('shop3');"><p>고객 선호도 분석</p></td>
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
			noselectmenu('분류별 선호도','counter_prcodeprefer.php',$idx[2][0],0);
			noselectmenu('상품 선호도','counter_productprefer.php',$idx[2][1],1);
			noselectmenu('상품 검색 선호도','counter_prsearchprefer.php',$idx[2][2],1);
			noselectmenu('Site 구성요소 선호도','counter_sitepageprefer.php',$idx[2][3],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>

		<table cellpadding="0" cellspacing="0" width="158" id=tblashop4>
		<tr>
			<td width="158" height="28" style="cursor:hand;" onClick="ChangeMenu('shop4');"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>외부 접근 경로 분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop4 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;" style="cursor:hand;" onClick="ChangeMenu('shop4');"><p>외부 접근 경로 분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158">
		<tr>
			<td width="158">
			<div id=shop4 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table cellpadding="0" cellspacing="0" width="158" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop4") {
				echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
			}
			noselectmenu('도메인별 접근순위','counter_domainrank.php',$idx[3][0],0);
			noselectmenu('검색엔진별 접근순위','counter_searchenginerank.php',$idx[3][1],1);
			noselectmenu('검색엔진 검색어 순위','counter_searchkeywordrank.php',$idx[3][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>

		<table cellpadding="0" cellspacing="0" width="158" id=tblashop5>
		<tr>
			<td width="158" height="28" style="cursor:hand;" onClick="ChangeMenu('shop5');"><p><img src="images/icon_leftmenu.gif" width="17" height="17" border="0" align=absmiddle>그래프로 보는 통계분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158" id=tblbshop5 style="display:none">
		<tr>
			<td width="158" height="28" background="images/leftmenu_depth1_select.gif" class="1depth_select" style="padding-left:13pt;" style="cursor:hand;" onClick="ChangeMenu('shop5');"><p>그래프로 보는 통계분석</p></td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="158">
		<tr>
			<td width="158">
			<div id=shop5 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table cellpadding="0" cellspacing="0" width="158" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop5") {
				echo "<tr><td width=\"158\"><img src=\"images/leftmenu_line.gif\" width=\"158\" height=\"1\" border=\"0\"></td></tr>";
			}
			noselectmenu('시간별 전체 접속통계','counter_timetotal.php',$idx[4][0],0);
			noselectmenu('일자별 전체 접속통계','counter_daytotal.php',$idx[4][1],2);
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
</div>
</div>
<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>
