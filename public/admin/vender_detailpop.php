<?php
/********************************************************************* 
// 파 일 명		: vender_detailpop.php 
// 설     명		: 입점업체 상세보기
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체 상세로 보기
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

#---------------------------------------------------------------
# 관리자 아이디를 체크한다.
#---------------------------------------------------------------
	if(ord($_ShopInfo->getId())==0){
		echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
		exit;
	}
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];

	if(ord($vender)==0) {
		alert_go('해당 입점업체가 존재하지 않습니다.','c');
	}

#---------------------------------------------------------------
# 업체 리스트를 불러온다.
#---------------------------------------------------------------
	$sql = "SELECT a.*, b.brand_name,b.brand_description,c.prdt_allcnt,c.prdt_cnt,c.cust_cnt,c.count_total ";
	$sql.= "FROM tblvenderinfo a, tblvenderstore b, tblvenderstorecount c ";
	$sql.= "WHERE a.vender='{$vender}' AND a.delflag='N' AND a.vender=b.vender AND b.vender=c.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$row=pmysql_fetch_object($result)) {
		alert_go('해당 입점업체가 존재하지 않습니다.','c');
	}
	pmysql_free_result($result);
	$_vdata=$row;

	$bank_account="";
	if(ord($_vdata->bank_account)) {
		$tmp=explode("=",$_vdata->bank_account);
		$bank_account=$tmp[0]." {$tmp[1]} ({$tmp[2]})";
	}

	$deli_price = $_vdata->deli_price;
	if($deli_price==-9) $delivery="Y";
	else if($deli_price==0) $delivery="F";
	else $delivery="M";
	if($deli_price<0) $deli_price=0;
	$deli_mini = $_vdata->deli_mini;

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>입점업체 상세정보</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	//var oHeight = document.all.table_body.clientHeight + 55;
	var oHeight = 500;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=700 style="table-layout:fixed;" id=table_body>
<tr>
	<td width=100% align=center>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/vender_detail_pop_t.gif" border="0" width="130" height="31"></td>
		<td width="100%" background="images/member_find_titlebg.gif"><FONT COLOR="#ffffff"><B>[<?=$_vdata->id?>]</B></FONT></td>
		<td align=right><img src="images/member_find_titleimg.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<tr><td height=10></td></tr>
		<tr>
			<td style="padding-bottom:3px"><IMG height=9 src="images/icon_9.gif" width=13 border=0><B>업체 회사정보</B>
			</td>
		</tr>
		</table>
		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<col width=120></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">아이디</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->id?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사명</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_name?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사업자등록번호</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_num?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">대표자 성명</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_owner?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">대표 전화/팩스</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_tel?> / <?=$_vdata->com_fax?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사 주소</td>
			<td class="td_con1"><img width="0"><B>[<?=$_vdata->com_zonecode?>] <?=$_vdata->com_addr?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사업자 업태</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_biz?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사업자 종목</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->com_item?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사 홈페이지</td>
			<td class="td_con1"><img width="0"><B><?=(ord($_vdata->com_homepage)?"<a href=\"http://{$_vdata->com_homepage}\" target=\"_blank\">http://{$_vdata->com_homepage}</a>":"")?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</table>

		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<tr><td height=20></td></tr>
		<tr>
			<td style="padding-bottom:3px"><IMG height=9 src="images/icon_9.gif" width=13 border=0><B>업체 담당자 정보</B>
			</td>
		</tr>
		</table>
		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<col width=120></col>
		<col width=></col>
		<TR>
			<TD colspan==2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 성명</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->p_name?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 핸드폰</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->p_mobile?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이메일</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->p_email?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 부서명</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->p_buseo?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 직위</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->p_level?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</table>

		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<tr><td height=20></td></tr>
		<tr>
			<td style="padding-bottom:3px"><IMG height=9 src="images/icon_9.gif" width=13 border=0><B>업체 관리정보</B>
			</td>
		</tr>
		</table>
		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<col width=120></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 처리 권한</td>
			<td class="td_con1">
			<input type=checkbox name=chk_prdt1 value="Y" <?php if($_vdata->grant_product[0]=="Y")echo"checked";?> disabled><B>등록</B>
			<img width=5 height=0>
			<input type=checkbox name=chk_prdt2 value="Y" <?php if($_vdata->grant_product[1]=="Y")echo"checked";?> disabled><B>수정</B>
			<img width=5 height=0>
			<input type=checkbox name=chk_prdt3 value="Y" <?php if($_vdata->grant_product[2]=="Y")echo"checked";?> disabled><B>삭제</B>
			<img width=10 height=0>
			<input type=checkbox name=chk_prdt4 value="Y" <?php if($_vdata->grant_product[3]=="Y")echo"checked";?> disabled><B>등록/수정시, 관리자 인증</B>
			</td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">입점 상품수</td>
			<td class="td_con1"><img width="0"><B><?=($_vdata->product_max==0?"무제한 등록 가능":$_vdata->product_max."개 까지 상품등록 가능")?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">판매 수수료</td>
			<td class="td_con1"><img width="0"><B><?=(int)$_vdata->rate?> %</B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">정산 계좌정보</td>
			<td class="td_con1"><img width="0"><B><?=$bank_account?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">정산일</td>
			<td class="td_con1"><img width="0"><B>매월 <?=(ord($_vdata->account_date)?$_vdata->account_date."일":"")?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</table>

		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<tr><td height=20></td></tr>
		<tr>
			<td style="padding-bottom:3px"><IMG height=9 src="images/icon_9.gif" width=13 border=0><B>미니샵 정보</B>
			</td>
		</tr>
		</table>
		<table border=0 cellpadding=0 cellspacing=0 width=95% style="table-layout:fixed">
		<col width=120></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">미니샵명</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->brand_name?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">미니샵 설명</td>
			<td class="td_con1"><img width="0"><?=$_vdata->brand_description?></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록 상품</td>
			<td class="td_con1"><img width="0"><B>진열중/진열안함 : <?=$_vdata->prdt_cnt?>개 / <?=$_vdata->prdt_allcnt?>개</B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">단골샵 등록</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->cust_cnt?>명</B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">총 방문자수</td>
			<td class="td_con1"><img width="0"><B><?=$_vdata->count_total?>명</B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">배송료</td>
			<td class="td_con1">
<?php
			if($delivery=="F") {
				echo "<B>무료</B>";
			} else if($delivery=="Y") {
				echo "<B>착불</B>";
			} else if($delivery=="M") {
				echo "주문금액이 <B>".number_format($deli_mini)."원</B> 보다 작으면 배송료 <B>".number_format($deli_price)."원</B> 추가";
			}
?>
			</td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</table>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center><input type="image" src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" hspace="2" onclick="window.close();">
		</td>
	</tr>
	<tr><td height=10></td></tr>
	</table>

	</td>
</tr>
</table>
</body>
</html>
