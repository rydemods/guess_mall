<?php
switch(basename($_SERVER['SCRIPT_NAME'])) {
	case "vender_info.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "vender_info_add.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "delivery_info.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "product_deliinfo.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;

	case "minishop_info.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "minishop_design.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "cust_info.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "themecode_manager.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;
	case "themecode_prdtin.php":
		$menuidx = "shop2"; $idx[1][4] = 'YES'; break;
	case "main_design.php":
		$menuidx = "shop2"; $idx[1][5] = 'YES'; break;
	case "code_design.php":
		$menuidx = "shop2"; $idx[1][6] = 'YES'; break;
	case "main_topdesign.php":
		$menuidx = "shop2"; $idx[1][7] = 'YES'; break;
	case "code_topdesign.php":
		$menuidx = "shop2"; $idx[1][8] = 'YES'; break;
	case "minishop_notice.php":
		$menuidx = "shop2"; $idx[1][9] = 'YES'; break;

	case "product_register.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "product_myprd.php":
	case "product_prdmodify.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "product_csv_download.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "product_imgmultiset.php":
	case "product_allupdate.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;

	case "order_list.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "order_list_delivery.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "order_list_regoods.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "order_list_rechange.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;
	case "order_qna.php":
		$menuidx = "shop4"; $idx[3][4] = 'YES'; break;
	case "order_qnaview.php":
		$menuidx = "shop4"; $idx[3][4] = 'YES'; break;
	case "order_review.php":
		$menuidx = "shop4"; $idx[3][5] = 'YES'; break;

	case "sellstat_list.php":
		$menuidx = "shop5"; $idx[4][0] = 'YES'; break;
	case "sellstat_sale.php":
		$menuidx = "shop5"; $idx[4][1] = 'YES'; break;
	case "sellstat_calendar.php":
		$menuidx = "shop5"; $idx[4][2] = 'YES'; break;

	case "coupon_new.php":
		$menuidx = "shop6"; $idx[5][0] = 'YES'; break;
	case "coupon_supply.php":
		$menuidx = "shop6"; $idx[5][1] = 'YES'; break;
	case "coupon_list.php":
		$menuidx = "shop6"; $idx[5][2] = 'YES'; break;

	case "shop_notice.php":
		$menuidx = "shop7"; $idx[6][0] = 'YES'; break;
	case "shop_counsel.php":
		$menuidx = "shop7"; $idx[6][1] = 'YES'; break;

	case "promotion_reg.php":
		$menuidx = "shop8"; $idx[7][0] = 'YES'; break;
	case "promotion.php":
		$menuidx = "shop8"; $idx[7][1] = 'YES'; break;

}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5','shop6','shop7', 'shop8');
var thisshop="<?=$menuidx?>";
ino=7;

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
		eval(stobj).display="";
		thisshop=shop;
	} else{
		Change();
		//eval(stobj).display="block";
		thisshop=stobj;
	}
}

function InitMenu(shop) {
	try {
		if(document.all){
			document.all(shop).style.display="";
		} else if(document.getElementById){
			document.getElementById(shop).style.display="";
		} else if(document.layers){
			document.layers[shop].display="";
		}
	} catch (e) {
/*
		shop = "shop1";
		if(document.all){
			document.all(shop).style.display="block";
		} else if(document.getElementById){
			document.getElementById(shop).style.display="block";
		} else if(document.layers){
			document.layers[shop].display="block";
		}
*/
	}
}
//-->
</SCRIPT>

<table width=100%  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
<tr>
	<td height=100%>
	<table width=100%  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
	<tr>
		<td valign=top>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td><A HREF="main.php"><img src="images/menu_top.gif" width=167 height=50 border=0></A></td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop1');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[0])?"<font color=#FF6000>":"")?><B>업체정보 설정</B></font></a></b></td>
		</tr>
		<tr id=shop1 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('업체정보 관리','vender_info.php',$idx[0][0],0);
			noselectmenu('업체 추가정보 관리','vender_info_add.php',$idx[0][1],1);
			noselectmenu('배송관련 기능설정','delivery_info.php',$idx[0][2],1);
			noselectmenu('배송/교환/환불정보 노출','product_deliinfo.php',$idx[0][3],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF style='display:none;'>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop2');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[1])?"<font color=#FF6000>":"")?><B>미니샵 운영 관리</B></font></a></b></td>
		</tr>
		<tr id=shop2 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('미니샵 기본정보 설정','minishop_info.php',$idx[1][0],0);
			noselectmenu('미니샵 디자인관리','minishop_design.php',$idx[1][1],1);
			noselectmenu('고객센터 정보설정','cust_info.php',$idx[1][2],1);
			noselectmenu('테마 카테고리 설정','themecode_manager.php',$idx[1][3],1);
			noselectmenu('테마 카테고리 상품진열','themecode_prdtin.php',$idx[1][4],1);
			noselectmenu('메인 화면 관리','main_design.php',$idx[1][5],1);
			noselectmenu('대분류 화면 관리','code_design.php',$idx[1][6],1);
			noselectmenu('메인 상단/이벤트 관리','main_topdesign.php',$idx[1][7],1);
			noselectmenu('대분류 상단/이벤트 관리','code_topdesign.php',$idx[1][8],1);
			noselectmenu('미니샵 공지사항 관리','minishop_notice.php',$idx[1][9],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop3');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[2])?"<font color=#FF6000>":"")?><B>판매상품 관리</B></font></a></b></td>
		</tr>
		<tr id=shop3 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('상품 신규등록','product_register.php',$idx[2][0],0);
			noselectmenu('내 상품 관리','product_myprd.php',$idx[2][1],1);
			noselectmenu('상품 엑셀 다운로드','product_csv_download.php',$idx[2][2],2);
			//noselectmenu('다중이미지 등록/관리','product_imgmultiset.php',$idx[2][2],1);
			//noselectmenu('상품 일괄 간편수정','product_allupdate.php',$idx[2][3],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop4');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[3])?"<font color=#FF6000>":"")?><B>주문/배송 관리</B></font></a></b></td>
		</tr>
		<tr id=shop4 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('주문조회/배송','order_list.php',$idx[3][0],0);
			noselectmenu('배송준비중','order_list_delivery.php',$idx[3][1],1);
			noselectmenu('주문반품','order_list_regoods.php',$idx[3][2],1);
			noselectmenu('주문교환','order_list_rechange.php',$idx[3][3],1);
			noselectmenu('상품 Q&A 관리','order_qna.php',$idx[3][4],1);
			noselectmenu('상품 리뷰 관리','order_review.php',$idx[3][5],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop5');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[4])?"<font color=#FF6000>":"")?><B>정산/매출 관리</B></font></a></b></td>
		</tr>
		<tr id=shop5 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			//noselectmenu('판매상품 정산조회','sellstat_list.php',$idx[4][0],3);
            noselectmenu('판매상품 정산조회','sellstat_list_v2.php',$idx[4][0],3);
			//noselectmenu('입점상품 매출분석','sellstat_sale.php',$idx[4][1],1);
			//noselectmenu('정산 캘린더','sellstat_calendar.php',$idx[4][2],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF style='display:none;'>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop6');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[5])?"<font color=#FF6000>":"")?><B>할인쿠폰 관리</B></font></a></b></td>
		</tr>
		<tr id=shop6 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('쿠폰 생성하기','coupon_new.php',$idx[5][0],0);
			noselectmenu('생성된 쿠폰 개별발급','coupon_supply.php',$idx[5][1],1);
			noselectmenu('발급된 쿠폰내역 관리','coupon_list.php',$idx[5][2],2);
?>
			</table>
			</td>
		</tr>
		</table>

		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF style='display:none;'>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop7');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[6])?"<font color=#FF6000>":"")?><B>본사 커뮤니티</B></font></a></b></td>
		</tr>
		<tr id=shop7 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
			noselectmenu('본사 공지사항','shop_notice.php',$idx[6][0],0);
			noselectmenu('본사 상담게시판','shop_counsel.php',$idx[6][1],2);
?>
			</table>
			</td>
		</tr>
		</table>
		<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF style='display:none;'>
		<tr><td height=1 bgcolor=E6E6E6></td></tr>
		<tr height=30 style="padding-left:8px">
			<td style="padding-left:10px"><a href="javascript:ChangeMenu('shop8');"><img src=images/icon_cross01.gif border=0 align=absmiddle> <?=(isset($idx[7])?"<font color=#FF6000>":"")?><B>마케팅 지원</B></font></a></b></td>
		</tr>
		<tr id=shop8 style='display:none'>
			<td valign=top style=background-repeat:no-repeat;padding-top:10;padding-left:11 background=images/l_menu01.gif>
			<table border=0 cellspacing=0 cellpadding=0>
<?php
		
			noselectmenu('기획전 신규등록','promotion_reg.php?mode=ins',$idx[7][0],0);
			noselectmenu('기획전 관리','promotion.php',$idx[7][1],2);
		
?>		
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>InitMenu('<?=$menuidx?>');</script>
