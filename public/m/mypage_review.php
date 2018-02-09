<?php include_once('outline/header_m.php'); ?>
<?php 

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.MDir."login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.MDir."login.php");
	}
}
pmysql_free_result($result);

$listnum = 3;

#####날짜 셋팅 부분
$s_year=(int)$_GET["s_year"];
$s_month=(int)$_GET["s_month"];
$s_day=(int)$_GET["s_day"];

$e_year=(int)$_GET["e_year"];
$e_month=(int)$_GET["e_month"];
$e_day=(int)$_GET["e_day"];

$day_division = $_GET['day_division'];

$limitpage = $_GET['limitpage'];

$review_type=$_GET['review_type']?$_GET['review_type']:"reviewwrite";

$r_s_year= 0;
$r_s_month= 0;
$r_s_day= 0;

$r_e_year= 0;
$r_e_month= 0;
$r_e_day= 0;

$r_day_division = "";

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$strDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Y-m-d",$etime);

$review_display[$review_type]="active";

?>

<?php include($Dir.TempletDir."review/myreview_TEM_001.php"); ?>
<?php include_once('outline/footer_m.php'); ?>
