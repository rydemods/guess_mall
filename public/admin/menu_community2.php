<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "community_list.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "community_register.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "community_article.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "community_notice.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "community_personal.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "community_schedule_year.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "community_schedule_month.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "community_schedule_week.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "community_schedule_day.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1');
var thisshop="<?=$menuidx?>";
ino=1;

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
	<TD height="50" align="right" valign="top" background="images/community_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop1">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">커뮤니티 관리</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:16px;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop1');">커뮤니티 관리</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('게시판 리스트 관리','community_list.php',$idx[0][0],0);
			noselectmenu('게시판 신규 생성','community_register.php',$idx[0][1],1);
			noselectmenu('게시판 게시물 관리','community_article.php',$idx[0][2],1);
			noselectmenu('게시판 공지사항 관리','community_notice.php',$idx[0][3],1);
			noselectmenu('1:1 고객 게시판 관리','community_personal.php',$idx[0][4],1);
			noselectmenu('쇼핑몰 일정관리','community_schedule_year.php',$idx[0][5],2);
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
