<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//exdebug($_ShopInfo);

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}


$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
	}
}
$staff_type = $row->staff_type;
pmysql_free_result($result);

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$cdate = date("YmdH");
if($_data->coupon_ok=="Y") {
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$coupon_cnt = $row->cnt;
	pmysql_free_result($result);
} else {
	$coupon_cnt=0;
}

$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$ordgbn=$_POST["ordgbn"];
if(!strstr("ASCR",$ordgbn)) {
	$ordgbn="A";
}
?>
<?//exdebug($_mdata);?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 마이페이지</TITLE>
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>


<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
					<?
					$subTop_flag = 3;
					//include ($Dir.MainDir."sub_top.php");
					?>
					<div class="containerBody sub_skin">

						<!-- LNB -->
						<div class="left_lnb">
							<? include ($Dir.FrontDir."mypage_TEM01_left.php");?>
							<!---->
						</div><!-- //LNB -->

						<div class="right_section">

							<h3 class="title">
								기부
								<p class="line_map"><a>홈</a> &gt; <a>기부</a>  &gt;  <a class="on">기부</a></p>
							</h3>

							<div class="donation_wrap">
								<p class="banner"><img src="../img/common/donation_banner.jpg" alt="" /></p>

								<div class="total_wrap">
									<ul class="total">
										<li><span>누적 기부 금액<strong>15,001,845</strong><em>원</em></span></li>
										<li><span>김대영 님이 기부한 적립금<strong>1,845</strong><em>원</em></span></li>
									</ul>
								</div>

								<p class="title">기부 가능 적립금</p>
								<table class="th_left" width="100%">
									<colgroup>
										<col style="width:15%" /><col style="width:35%" /><col style="width:15%" /><col style="width:35%" />
									</colgroup>
									<tr>
										<th>기부 가능한 적립금</th>
										<td>5,000 P</td>
										<th>기부하기</th>
										<td><input type="text" name="" id="" /> <a href="#" class="btn_D small">기부하기</a></td>
									</tr>
								</table>

								<p class="title">기부 참여자 리스트</p>
								<table class="th_top" width="100%">
									<colgroup>
										<col style="width:100px" /><col style="width:150px" /><col style="width:auto" /><col style="width:150px" />
									</colgroup>
									<tr>
										<th>번호</th>
										<th>날짜</th>
										<th>아이디</th>
										<th>적립금</th>
									</tr>
									<tr>
										<td>4</td>
										<td>2015-06-10</td>
										<td>adook****</td>
										<td>500P</td>
									</tr>
									<tr>
										<td>3</td>
										<td>2015-06-10</td>
										<td>hong****</td>
										<td>11,300P</td>
									</tr>
									<tr>
										<td>2</td>
										<td>2015-06-10</td>
										<td>rosem****</td>
										<td>3,100P</td>
									</tr>
									<tr>
										<td>1</td>
										<td>2015-06-10</td>
										<td>tiger****</td>
										<td>1,600P</td>
									</tr>
								</table>

							</div><!-- //div.donation_wrap -->

						</div>
					</div>
			</td>
		</tr>
	</table>



</body>
</html>