<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "member_list.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "member_outlist.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "member_excelupload.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;

	case "member_groupnew.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "member_groupmemreg.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "member_groupmemberview.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;

	case "member_mailsend.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "member_mailallsend.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "member_mailallsendinfo.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "member_smssend.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;
	case "member_smsallsend.php":
		$menuidx = "shop3"; $idx[2][4] = 'YES'; break;
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
	<TD height="50" align="right" valign="top" background="images/member_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">회원정보 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop1');">회원정보 관리</td>
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
			noselectmenu('회원정보 관리','member_list.php',$idx[0][0],0);
			noselectmenu('회원 탈퇴요청 관리','member_outlist.php',$idx[0][1],1);
			noselectmenu('회원정보 일괄 등록','member_excelupload.php',$idx[0][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">회원등급 설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop2');">회원등급 설정</td>
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
			noselectmenu('회원등급 등록/수정/삭제','member_groupnew.php',$idx[1][0],0);
			noselectmenu('회원등급 변경 관리','member_groupmemreg.php',$idx[1][1],1);
			noselectmenu('등급별 회원 관리','member_groupmemberview.php',$idx[1][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">회원관리 부가기능</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:13pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop3');">회원관리 부가기능</td>
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
			noselectmenu('개별메일 발송','member_mailsend.php',$idx[2][0],0);
			noselectmenu('단체메일 발송','member_mailallsend.php',$idx[2][1],1);
			noselectmenu('단체메일 발송내역 관리','member_mailallsendinfo.php',$idx[2][2],1);
			noselectmenu('개별 SMS 발송',"javascript:parent.topframe.GoMenu(7,'market_smssinglesend.php');",$idx[2][3],1);
			noselectmenu('단체 SMS 발송',"javascript:parent.topframe.GoMenu(7,'market_smsgroupsend.php');",$idx[2][4],2);
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
