<?php // hspark
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "shop_basicinfo.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "shop_keyword.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "shop_mainintro.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "shop_companyintro.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "shop_agreement.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "shop_privercyinfo.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;


	case "shop_openmethod.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "shop_displaytype.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "shop_layout.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "shop_mainproduct.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;
	case "shop_productshow.php":
		$menuidx = "shop2"; $idx[1][4] = 'YES'; break;
	case "shop_mainleftinform.php":
		$menuidx = "shop2"; $idx[1][5] = 'YES'; break;
	case "shop_logobanner.php":
		$menuidx = "shop2"; $idx[1][6] = 'YES'; break;
	case "shop_orderform.php":
		$menuidx = "shop2"; $idx[1][7] = 'YES'; break;
	case "shop_ssl.php":
		$menuidx = "shop2"; $idx[1][8] = 'YES'; break;
	case "shop_bizsiren.php":
		$menuidx = "shop2"; $idx[1][9] = 'YES'; break;
	
	case "shop_tag.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "shop_search.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "shop_reserve.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "shop_recommand.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;
	case "shop_member.php":
		$menuidx = "shop3"; $idx[2][4] = 'YES'; break;
	case "shop_deli.php":
		$menuidx = "shop3"; $idx[2][5] = 'YES'; break;
	case "shop_payment.php":
		$menuidx = "shop3"; $idx[2][6] = 'YES'; break;
	case "shop_escrow.php":
		$menuidx = "shop3"; $idx[2][7] = 'YES'; break;
	case "shop_return.php":
		$menuidx = "shop3"; $idx[2][8] = 'YES'; break;
	case "shop_basket.php":
		$menuidx = "shop3"; $idx[2][9] = 'YES'; break;
	case "shop_review.php":
		$menuidx = "shop3"; $idx[2][10] = 'YES'; break;
	case "shop_estimate.php":
		$menuidx = "shop3"; $idx[2][11] = 'YES'; break;
	
	case "shop_rolelist.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "shop_adminlist.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "shop_iplist.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "shop_changeadminpasswd.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;
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

<!-- LNB 리뉴얼 -->
<div class="admin_lnb_wrap">
ㅇ래농린어러
</div>
<!-- LNB 리뉴얼 -->

<div id="scrollingLeftParent" style="position:relative;">
<DIV id="scrollingLeft" style="VISIBILITY:visible;POSITION:absolute;">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" bgcolor="#FFFFFF">

<TR>
	<TD height="50" align="right" valign="top" background="images/shop_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">상점 기본정보 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:16px;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop1');">상점 기본정보 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#DFF2FF">
		
<?php
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('상점 기본정보 관리','shop_basicinfo.php',$idx[0][0],0);
			noselectmenu('브라우저 타이틀/키워드','shop_keyword.php',$idx[0][1],1);
			noselectmenu('메인 타이틀이미지 디자인','shop_mainintro.php',$idx[0][2],1);
			noselectmenu('회사 소개/약도','shop_companyintro.php',$idx[0][3],1);
			noselectmenu('쇼핑몰 이용약관','shop_agreement.php',$idx[0][4],1);
			noselectmenu('쇼핑몰 개인정보취급방침','shop_privercyinfo.php',$idx[0][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">쇼핑몰 환경 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:16pt;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop2');">쇼핑몰 환경 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop2" style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('업종별 운영방식 설정','shop_openmethod.php',$idx[1][0],0);
			noselectmenu('프레임/정렬 설정','shop_displaytype.php',$idx[1][1],1);
			noselectmenu('쇼핑몰 레이아웃 설정','shop_layout.php',$idx[1][2],1);
			noselectmenu('상품 진열수/화면 설정','shop_mainproduct.php',$idx[1][3],1);
			noselectmenu('상품 진열 기타 설정','shop_productshow.php',$idx[1][4],1);
			noselectmenu('왼쪽 고객 알림 디자인','shop_mainleftinform.php',$idx[1][5],1);
			noselectmenu('로고/배너 관리','shop_logobanner.php',$idx[1][6],1);
			noselectmenu('회원가입/주문 안내문구','shop_orderform.php',$idx[1][7],1);
			noselectmenu('SSL(보안서버) 기능 설정','shop_ssl.php',$idx[1][8],1);
			noselectmenu('실명인증 정보 설정','shop_bizsiren.php',$idx[1][9],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">쇼핑몰 운영 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:16px;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop3');">쇼핑몰 운영 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop3" style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop3") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('상품태그 관련 기능설정','shop_tag.php',$idx[2][0],0);
			noselectmenu('상품검색 관련 기능설정','shop_search.php',$idx[2][1],1);
			noselectmenu('적립금/쿠폰 기능설정','shop_reserve.php',$idx[2][2],1);
			noselectmenu('추천인 제도 설정','shop_recommand.php',$idx[2][3],1);
			noselectmenu('회원가입 관련 설정','shop_member.php',$idx[2][4],1);
			noselectmenu('상품 배송관련 기능설정','shop_deli.php',$idx[2][5],1);
			noselectmenu('상품 결제관련 기능설정','shop_payment.php',$idx[2][6],1);
			noselectmenu('에스크로 결제관련 설정','shop_escrow.php',$idx[2][7],1);
			noselectmenu('상품 반품/환불 기능설정','shop_return.php',$idx[2][8],1);
			noselectmenu('장바구니 관련 기능설정','shop_basket.php',$idx[2][9],1);
			noselectmenu('상품리뷰(후기) 설정','shop_review.php',$idx[2][10],1);
			noselectmenu('상품 견적서 기능설정','shop_estimate.php',$idx[2][11],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr>
			<td height="28" style="cursor:hand;" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">보안 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
		<tr>
			<td height="28" background="images/leftmenu_depth1_select.gif" style="padding-left:16px;cursor:hand;" class="1depth_select" onClick="ChangeMenu('shop4');">보안 설정</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop4" style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#DFF2FF">
<?php
			if($menuidx != "shop4") {
				echo "<tr><td height=\"1\" bgcolor=\"#3375C9\"></td></tr>";
			}
			noselectmenu('그룹 및 권한 설정','shop_rolelist.php',$idx[3][0],0);
			noselectmenu('운영자/부운영자 설정','shop_adminlist.php',$idx[3][1],1);
			noselectmenu('접근IP 설정','shop_iplist.php',$idx[3][2],1);
			noselectmenu('패스워드 변경','shop_changeadminpasswd.php',$idx[3][3],2);
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
</DIV>
</DIV>
<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>
