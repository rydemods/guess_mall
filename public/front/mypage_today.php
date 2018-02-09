<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
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
pmysql_free_result($result);

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

$_prdt_list=trim($_COOKIE['ViewProduct'],',');	//(,상품코드1,상품코드2,상품코드3,) 형식으로
$prdt_list=explode(",",$_prdt_list);
$prdt_no=count($prdt_list);
if(ord($prdt_no)==0) {
	$prdt_no=0;
}

$tmp_product="";
for($i=0;$i<$prdt_no;$i++){
	$tmp_product.="'{$prdt_list[$i]}',";
}

$productall = array();
$tmp_product=rtrim($tmp_product,',');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 주문/배송 조회</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<tr>
<td align=center>

<style type="text/css">
.contents_side .indiv h3{width:840px;margin:40px 0 18px 0;}
.contents_side .indiv table .last_border{height:1px; background-color: #F2F2F2;}


.my_today_title {width:840px; height:30px; padding-top:30px;}
.my_today_titleimg {float:left;}
.my_today_warp {clear:both; border-top:2px solid #555555; }


.my_today_list {clear:both;}
.my_today_list ul {width:840px; height:80px; padding:5px 0; border-bottom:1px solid #ebebeb;}
.my_today_list ul li {float:left; text-align:center; line-height:80px;}
.my_today_list ul li img{margin:0 auto;}
.my_today_list ul li input{margin-top:12px}

.paging {width:100%;}
.my_today_paging {width:100px; height:20px; margin:0 auto; } 
.my_today_paging li {float:left; width:20px; height:20px;  padding-top:20px; text-align:center; line-height:13px;}

</style>

<!-- start container -->
<div id="container">
<!-- LNB -->
	<?	include ($Dir.FrontDir."mypage_TEM01_left.php");?>
	<!-- #LNB -->

	<div class="contents_side">
	<? include $Dir.FrontDir."mypage_menu.php";?>


<div class="my_today_detail">

             <div class="my_today_title">
			 <span class="my_today_titleimg"><img src="<?=$Dir?>image/mypage/today_today_title.gif" alt="오늘본상품"></span>
			 <span class="my_today_ea">&nbsp;&nbsp;&nbsp;총 <?=count(explode(",",$tmp_product))?>개의 상품이 있습니다.</span>
			 </div>

             <div class="my_today_warp">





			 <div class="my_today_list">
<?
		$sql = "SELECT COUNT(*) as t_count FROM tblproduct ";
		$sql.= "WHERE productcode IN ({$tmp_product}) ";
		$paging = new Tem001_Paging($sql,10,5,'GoPage',true);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;
		
		$sql = "SELECT productcode,productname,maximage,quantity,reserve,sellprice FROM tblproduct ";
		$sql.= "WHERE productcode IN ({$tmp_product}) ";
		$sql.= "ORDER BY FIELD(productcode,{$tmp_product}) ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
			
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

			$date=date('Y-m-d',$row->writetime);
			
?>
			 <ul>
			  <li class="cell20"><a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>"><img src="<?=$Dir.DataDir."shopimages/product/".urlencode($row->maximage)?>" width="80" height="80"></a></li>
			 <li class="cell40"><a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></a></li>
			 <li class="cell15"><?=number_format($row->reserve)?>원</li>
			 <li class="cell15"><?=number_format($row->sellprice)?>원</li>
			 </ul>
<?		$cnt++;
		}
		
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<ul><li style='width:850px'>해당내역이 없습니다.</li></ul>";
		}
?>
		<div class="paging"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></div><!-- paging 끝 -->
			 </div><!-- my_today_list 끝 -->

			 </div><!-- my_today_warp 끝 -->
	
		</div><!-- my_wish_detail 끝 -->


	</div><!-- //end contents -->
</div><!-- //end container -->



</td>
</tr>
</form>


<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
