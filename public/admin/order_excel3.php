<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ordercodes=rtrim($_POST["ordercodes"],',');
//$order_codes=rtrim($_POST["order_codes"][],',');
$order_codes=$_POST["order_codes"];
$paymethod=$_POST["paymethod"];
$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];	//배송/입금일별 주문조회(처리기준)
$search=$_POST["search"];



$sql = "SELECT * FROM tblorderoption ";
$result = pmysql_query($sql,get_db_conn());

$CurrentTime = time();

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('주문서 EXCEL 다운로드 기간은 1년을 초과할 수 없습니다.');
}

if(($s_check=="bank_date" || $s_check=="deli_date") && ord($ordercodes)==0) {
	if ($termday>31) {
		alert_go('배송/입금별 주문서 EXCEL 다운로드 기간은 1달을 초과할 수 없습니다.');
	}
}

$excel_info = trim($_shopdata->excel_info,',');
$excel_ok  = $_shopdata->excel_ok;


$isproductall="N";
if(preg_match("/24|33/",$excel_info)){	//상품명1-갯수-옵션 ^ 상품명2-갯수-옵션 일 경우
	$isproductall="Y";
}
$isproduct ="N";
if(preg_match("/2([12356]{1})|34|38|39|40|43|44|45|46|47/",$excel_info)){
	$isproduct="Y";
}
$arr_excel = explode(",",$excel_info);
$cnt = count($arr_excel);

if(ord($ordercodes)) $ordercodes="'".str_replace(",","','",$ordercodes)."'";

if($isproduct=="Y" || $isproductall=="Y") {
	if(($s_check=="bank_date" || $s_check=="deli_date") && ord($ordercodes)==0) {
		$tablecode=$s_check;
		$sql = "SELECT ordercode FROM tblorderinfo WHERE {$tablecode} >= '{$search_s}' AND {$tablecode} <= '{$search_e}' ";
		$result = pmysql_query($sql,get_db_conn());
		$_ = array();
		while($row=pmysql_fetch_object($result)) {
			$_[] = "'{$row->ordercode}'";
		}
		pmysql_free_result($result);
		$tempordercode=implode(',',$_);
	} else {
		$tablecode="ordercode";
	}

	if($termday<=92) {
		$sql = "SELECT * FROM tblorderoption ";
		if(ord($tempordercode)) 
			$sql.= "WHERE ordercode IN ({$tempordercode}) ";
		elseif(ord($ordercodes)) 
			$sql.= "WHERE ordercode IN ({$ordercodes}) ";
		else
			$sql.= "WHERE ordercode >= '{$search_s}' AND ordercode <= '{$search_e}' ";
		$result = pmysql_query($sql,get_db_conn());
		while($row = pmysql_fetch_object($result)) {
			$optionkey=$row->ordercode.$row->productcode.$row->opt_idx;
			$addoption[$optionkey]=$row->opt_name;
		}
		pmysql_free_result($result);
	}

	$sql = "SELECT a.ordercode, a.sender_name, a.paymethod, a.bank_date, a.pay_flag, a.pay_admin_proc, a.deli_gbn, a.sabangnet_mall_id ";
	$sql.= " FROM tblorderinfo a ";

	if(ord($ordercodes))
		$sql.= " WHERE a.{$tablecode} IN ({$ordercodes}) ";
	else
		$sql.= " WHERE a.{$tablecode} >= '{$search_s}' AND a.{$tablecode} <= '{$search_e}' ";
	if (ord($deli_gbn)) $sql.= "AND a.deli_gbn = '{$deli_gbn}' ";

	if($paystate=="Y") {		//입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(a.bank_date)=14 ";	//무통장/가상계좌/실시간
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc!='C' AND a.pay_flag='0000' ";	//신용카드/핸드폰
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
	} elseif($paystate=="B") {	//미입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND (a.bank_date IS NULL OR a.bank_date='') ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc='C' AND a.pay_flag!='0000' ";
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
	} elseif($paystate=="C") {	//환불
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(a.bank_date)=9 ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND a.pay_admin_proc='C' AND a.pay_flag='0000' ";
		else $sql.= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
	}

	if (ord($paymethod)) $sql.= "AND a.paymethod LIKE '{$paymethod}%' ";

	if(ord($search)) {
		if($s_check=="cd") $sql.= "AND a.ordercode like '{$search}%' ";
		else if($s_check=="mn") $sql.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $sql.= "AND a.id='{$search}' ";
		else if($s_check=="cn") $sql.= "AND a.id LIKE 'X{$search}%' ";
	}

	$sql.= "ORDER BY a.ordercode DESC ";
} else {
	if(ord($ordercodes)) $tablecode="ordercode";
	if(ord($tablecode)==0) $tablecode="ordercode";
	$sql = "SELECT *,price as sumprice,reserve as usereserve FROM tblorderinfo ";

	if(ord($ordercodes))
		$sql.= "WHERE {$tablecode} IN ({$ordercodes}) ";
	else 
		$sql.= "WHERE {$tablecode} >= '{$search_s}' AND {$tablecode} <= '{$search_e}' ";

	if (ord($deli_gbn)) $sql.= "AND deli_gbn = '{$deli_gbn}' ";

	if($paystate=="Y") {		//입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(bank_date)=14 ";	//무통장/가상계좌/실시간
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc!='C' AND pay_flag='0000' ";	//신용카드/핸드폰
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=14) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_admin_proc!='C' AND pay_flag='0000')) ";
	} elseif($paystate=="B") {	//미입금
		if(strstr("BOQ",$paymethod)) $sql.= "AND (bank_date IS NULL OR bank_date='') ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc='C' AND pay_flag!='0000' ";
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND (bank_date IS NULL OR bank_date='')) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag!='0000' AND pay_admin_proc='C')) ";
	} elseif($paystate=="C") {	//환불
		if(strstr("BOQ",$paymethod)) $sql.= "AND LENGTH(bank_date)=9 ";
		elseif(strstr("CPMV",$paymethod)) $sql.= "AND pay_admin_proc='C' AND pay_flag='0000' ";
		else $sql.= "AND ((SUBSTR(paymethod,1,1) IN ('B','O','Q') AND LENGTH(bank_date)=9) OR (SUBSTR(paymethod,1,1) IN ('C','P','M','V') AND pay_flag='0000' AND pay_admin_proc='C')) ";
	}

	if (ord($paymethod)) $sql.= "AND paymethod LIKE '{$paymethod}%' ";

	if(ord($search)) {
		if($s_check=="cd") $sql.= "AND a.ordercode like '{$search}%' ";
		else if($s_check=="mn") $sql.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $sql.= "AND a.id='{$search}' ";
		else if($s_check=="cn") $sql.= "AND a.id LIKE 'X{$search}%' ";
	}

	$sql.= "ORDER BY ordercode DESC";
}



$result = pmysql_query($sql,get_db_conn());
$tot_cnt = pmysql_num_rows($result);

Header("Content-Type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=order_excel_".date("YmdHis",$CurrentTime).".csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");

/*각 셀 타이틀
$menu = array('제휴몰','상품명','상품개수');

for($i = 0 ; $i < count($menu) ; $i++){
	if($i!=0) echo ",";
	echo '"' . doubleQuote($menu[$i]) . '"';
}
echo "\n";
*/
$mall_seq = array();
while($_odata=pmysql_fetch_object($result)){
	if(!$_odata->sabangnet_mall_id){
		$_odata->sabangnet_mall_id = "shop9999";
	}

	$mall_seq[$_odata->sabangnet_mall_id] = $_odata->sabangnet_mall_id;

	$sql_prd = "SELECT * FROM tblorderproduct ";
	$sql_prd.= "WHERE ordercode='".$_odata->ordercode."' ";
	$sql_prd.= "AND productcode not like '%X' ";
	$res_prd = pmysql_query($sql_prd,get_db_conn());
	while($_pdata = pmysql_fetch_object($res_prd)){
		$opt1_name = $opt2_name = "";
		unset($opt1_name_arr);
		if($_pdata->opt1_name){
			$opt1_name_arr = explode(":",$_pdata->opt1_name);
			$opt1_name = trim($opt1_name_arr[1]);
		}
		unset($opt2_name_arr);
		if($_pdata->opt2_name){
			$opt2_name_arr = explode(":",$_pdata->opt2_name);
			$opt2_name = trim($opt2_name_arr[1]);
		}

		$title = $_pdata->productname.$opt1_name.$opt2_name;

		$_linedata[$_odata->sabangnet_mall_id][$title]+=$_pdata->quantity;
	}
}
// 출력할 제휴몰, 자사몰 번호

# 해당 주문에 있는 코드 모두 불러오기 위에 쿼리 결과 안에서 배열 생성으로 변경 2014-08-25 11:00
/*
$mall_seq = array(
				"shop0010",
				"shop0024",
				"shop0098",
				"shop0013",
				"shop0007",
				"shop0012",
				"shop0087",
				"shop0100",
				"shop0021",
				"shop0020",
				"shop0005",
				"91",
				"shop9999"
				);
*/

foreach($mall_seq as $mallv){
	if($_linedata[$mallv]){
		$total_cnt = 0;
		foreach($_linedata[$mallv] as $linek=>$linev){
			if($total_cnt<1){
				$mall_name = $arraySabangnetShopCode[$mallv];
			}else{
				$mall_name = '';
			}
			$total_cnt = $linev+$total_cnt;
			
			echo '"'.doubleQuote($mall_name).'","'.doubleQuote($linek).'","'.doubleQuote($linev).'"' ;
			echo "\n";
		}
		echo '"'.$arraySabangnetShopCode[$mallv].'요약","","'.$total_cnt.'"';
		echo "\n";
		echo '"","",""';
		echo "\n";
	}
}

if($_linedata){
	echo "\n";
	echo '총합,"","'.$tot_cnt.'"';
}


//exdebug($arraySabangnetShopCode);


function doubleQuote($str) {
	return str_replace('"', '""', $str);
}
?>
