<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원등급 혜택</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
</SCRIPT>

<?include ($Dir.MainDir.$_data->menu_type.".php");?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
########################## 인트로 #############################
##### 최근 주문 정보
/*
$toMonthStr = date("Y년 m월",strtotime ("-3 months"))." 01일";
$fromMonthStr = date("Y년 m월",strtotime ("-1 months"))." ".date("t",date("m",strtotime ("-1 months")))."일";

$toMonth = date("Y-m",strtotime ("-3 months"))."-01";
$fromMonth = date("Y-m",strtotime ("-1 months"))."-".date("t",date("m",strtotime ("-1 months")));
*/
$toMonthStr = date("Y년 m월",strtotime ("-2 months"))." 01일";
$fromMonthStr = date("Y년 m월 d일");

$toMonth = date("Y-m",strtotime ("-2 months"))."-01";
$fromMonth = date("Y-m-d");

$s_curtime=strtotime($toMonth);
$s_curdate=date("Ymd",$s_curtime);
$e_curtime=strtotime($fromMonth);
$e_curdate=date("Ymd",$e_curtime)."999999999999";

/*
$sql = "SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 1 as ordertype ";
$sql.= "FROM sales.tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
$sql.= "AND ( ";
$sql.= "			(
							deli_gbn='N' 
							AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag!='0000' AND pay_admin_proc='C'))
						) OR
						(
							deli_gbn='N'
							AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_admin_proc!='C' AND pay_flag='0000'))
						) OR
						(
							deli_gbn='S'
						) OR
						(
							deli_gbn='Y'
						)";
$sql.= "		) ";

$sql.= " union ";

$sql.= "SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 2 as ordertype ";
$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
$sql.= "AND ( ";
$sql.= "			(
							deli_gbn='N' 
							AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag!='0000' AND pay_admin_proc='C'))
						) OR
						(
							deli_gbn='N'
							AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_admin_proc!='C' AND pay_flag='0000'))
						) OR
						(
							deli_gbn='S'
						) OR
						(
							deli_gbn='Y'
						)";
$sql.= "		) ";
$sql.= "ORDER BY ordercode DESC";
*/
$sql = "SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 1 as ordertype ";
$sql.= "FROM sales.tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
$sql.= "AND deli_gbn = 'Y' AND receive_ok = '1'";
$sql.= " union ";

$sql.= "SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 2 as ordertype ";
$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
$sql.= "AND deli_gbn = 'Y' AND receive_ok = '1'";
$sql.= "ORDER BY ordercode DESC";
$result=pmysql_query($sql,get_db_conn());	
$t_pay = 0;
$t_count = 0;
while($row=pmysql_fetch_object($result)) {
	$t_pay = $t_pay+($row->price);	
	$t_count++;
}

?>


<!-- 메인 컨텐츠 -->
<div class="main_wrap">
	<div class="mypage1100">
		<!-- LNB -->
		<div class="left_lnb">
			<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
			<!---->
		</div><!-- //LNB -->

		<!-- 내용 -->
		<div class="right_section">
			
			<div class="sub_title"><h3 class="def"><span class="kr">회원등급 혜택</span></h3></div>

			<div class="benefit_table">
				<table cellpadding="0" cellspacing="0" border="0" width="895">
					<colgroup>
						<col style="width:auto" /><col style="width:240px" /><col style="width:240px" /><col style="width:240px" />
					</colgroup>
					<tr>
						<th style="border-left:0px">등급구분</th>
						<th style="color:#ea6000">VIP</th>
						<th style="color:#ea6000">일반</th>
						<th>비고</th>
					</tr>
					<tr>
						<td class="left">할인율</td>
						<td>5%</td>
						<td>3%</td>
						<td>기획상품 제외</td>
					</tr>
					<tr>
						<td class="left">적립금</td>
						<td colspan="2">2%</td>
						<td></td>
					</tr>
					<tr>
						<td class="left">선정기준</td>
						<td>누적금액 1백만원 이상</td>
						<td>회원가입 즉시</td>
						<td></td>
					</tr>
					<tr>
						<td class="left">기타</td>
						<td>등업시 10,000원 적립</td>
						<td>가입시 1,000원 적립</td>
						<td></td>
					</tr>
				</table>
			</div>

			<dl class="attention mt_50 mb_50">
				<dt class="eng">ATTENTION</dt>
				<dd>등급기준적용은 2년 기준입니다.</dd>
				<dd>적립금은 주문 구매확정시 적립되며, 적립금 1,000원 이상 있을시 100원 단위 사용가능합니다.</dd>
			</dl>

			


		</div>


	</div>
</div><!-- //메인 컨텐츠 -->


<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</div>
</BODY>
</HTML>
