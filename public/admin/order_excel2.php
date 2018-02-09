<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ordercodes=rtrim($_POST["ordercodes"],',');
$paymethod=$_POST["paymethod"];
$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];	//배송/입금일별 주문조회(처리기준)
$search=$_POST["search"];
$mode = $_POST["mode"];	//제휴몰인지 자사몰인지 구분하는 변수

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
$vnum=0;


	header( 'Content-type: application/vnd.ms-excel' );
	Header("Content-Disposition: attachment; filename=order_excel_".date("Ymd",$CurrentTime).".xls"); 
; 
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0,pre-check=0' );
	header( 'Pragma: public' );
	header( 'Content-Description: PHP4 Generated Data' );



echo "<table>";


$menu = array('업무구분','일자','거래처코드','거래처존','품번','색상','사이즈','수량','실판매금액','비고','상품명','옵션','상품코드');
echo "<tr>";
for($i = 0 ; $i < count($menu) ; $i++){
	//if($i!=0) echo ",";
	//echo '"' . doubleQuote($menu[$i]) . '"';
	echo "<td>" . doubleQuote($menu[$i]) . "</td>";
}
echo "</tr>";
echo "\n";

while ($row=pmysql_fetch_object($result)) {


	if($row->sabangnet_mall_id){
		$sabang_query = "select * from tblsewonmallcode where sabangnetshopname = '".$arraySabangnetShopCode[$row->sabangnet_mall_id]."' ";
		$sabang_result = pmysql_query($sabang_query);
		$sabang_data = pmysql_fetch_object($sabang_result);

	}else{
		$sabang_query = "select * from tblsewonmallcode where idx = 14 ";
		$sabang_result = pmysql_query($sabang_query);
		$sabang_data = pmysql_fetch_object($sabang_result);
	}

	if(!$_odata->sabangnet_mall_id){
		$_odata->sabangnet_mall_id = "shop9999";
	}

	
	
	$code = $sabang_data->sewoncode;
	$zone = $arraySabangnetShopCode[$row->sabangnet_mall_id];
	$type = '3';
	$date = substr($row->ordercode, 0, 8);
	$sendername = $row->sender_name;

	if(strstr("B", $row->paymethod[0])) {	//무통장
		$paymethod="무통장";
		if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") $pay="환불";
		elseif (ord($row->bank_date)) $pay="입금완료";
		else $pay="미입금";
	} elseif(strstr("V", $row->paymethod[0])) {	//계좌이체
		$paymethod="실시간계좌이체";
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		elseif ($row->pay_flag=="0000") $pay="결제완료";
	} elseif(strstr("M", $row->paymethod[0])) {	//핸드폰
		$paymethod="핸드폰결제";
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
		elseif ($row->pay_flag=="0000") $pay="결제완료";
	} elseif(strstr("OQ", $row->paymethod[0])) {	//가상계좌
		if(strstr("O", $row->paymethod[0])) $paymethod="가상계좌";
		elseif(strstr("Q", $row->paymethod[0])) $paymethod="가상계좌(매매보호)";
		if (strcmp($row->pay_flag,"0000")!=0) $pay="주문실패";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		elseif ($row->pay_flag=="0000" && ord($row->bank_date)==0) $pay="미입금";
		elseif ($row->pay_flag=="0000" && ord($row->bank_date)) $pay="입금완료";
	} else {
		if(strstr("C", $row->paymethod[0])) $paymethod="신용카드";
		elseif(strstr("P", $row->paymethod[0])) $paymethod="신용카드(매매보호)";
		if (strcmp($row->pay_flag,"0000")!=0) $pay="카드실패";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") $pay="카드승인";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") $pay="결제완료";
		elseif ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
	}

	$pay2 = $paymethod."[{$pay}]";


	switch($row->deli_gbn) {
		case 'S': $deli_gbn="발송준비";  break;
		case 'X': $deli_gbn="배송요청";  break;
		case 'Y': $deli_gbn="배송";  break;
		case 'D': $deli_gbn="취소요청";  break;
		case 'N': $deli_gbn="미처리";  break;
		case 'E': $deli_gbn="환불대기";  break;
		case 'C': $deli_gbn="주문취소";  break;
		case 'R': $deli_gbn="반송";  break;
		case 'H': $deli_gbn="배송(정산보류)";  break;
	}

	$etc = $sendername." / ".$deli_gbn." / ".$row->ordercode;

	$order_query = "select * from tblorderproduct where ordercode = '".$row->ordercode."' ";
	$order_query .= " AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
	$order_result = pmysql_query($order_query);
	
	
	while($order_data = pmysql_fetch_object($order_result)){
		$opt1_name = explode(':',$order_data->opt1_name);
		$opt_name = trim($opt1_name[1]);
		
		$opt2_name = "";
		if($order_data->opt2_name){
			$opt2_name_arr = explode(":",$order_data->opt2_name);
			$opt2_name = trim($opt2_name_arr[1]);
		}
		$opt_names = $opt_name.$opt2_name;
		
		$product_query = "select option1, sewon_option_no, sewon_option_code1, sewon_option_code2 from tblproduct where productcode = '".$order_data->productcode."' ";
		$product_query .= " AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
		$product_result = pmysql_query($product_query);
		$product_data = pmysql_fetch_object($product_result);

		$productS_query = "SELECT option1, sewon_option_no, sewon_option_code1, sewon_option_code2 from tblproduct_sabangnet";
		$productS_query .= " WHERE productcode = '".$order_data->productcode."' ";
		$productS_query.= " AND option1='{$opt_name}' ";
		$productS_result = pmysql_query($productS_query);
		$productS_data = pmysql_fetch_object($productS_result);
		
		$itemno = "";
		$color = "";
		$size = "";

		if($product_data->sewon_option_no){
			$option_val = explode(',',$product_data->option1);
			$sewon_option_no = explode(',',$product_data->sewon_option_no);
			$sewon_option_code1 = explode(',',$product_data->sewon_option_code1);
			$sewon_option_code2 = explode(',',$product_data->sewon_option_code2);

			for($k=0 ; $k < count($option_val) ; $k++ ){
				if($option_val[$k] == $opt_name){
					$itemno = $sewon_option_no[$k-1];
					$color = $sewon_option_code1[$k-1];
					$size = $sewon_option_code2[$k-1];
				}
			}
		}elseif($productS_data->sewon_option_no){
			$itemno = $productS_data->sewon_option_no;
			$color = $productS_data->sewon_option_code1;
			$size = $productS_data->sewon_option_code2;
		}else{
			$itemno = '세원코드 미등록 상품';
			$color = '세원코드 미등록 상품';
			$size = '세원코드 미등록 상품';
		}
		
		if(!$itemno){
			$itemno = '세원코드 미등록 옵션';
		}
		if(!$color){
			$color = '세원코드 미등록 옵션';
		}
		if(!$size){
			$size = '세원코드 미등록 옵션';
		}
		



		
		$data_list = array($type, $date, str_pad($code, 5, "0", STR_PAD_LEFT), $zone, $itemno, $color, $size, $order_data->quantity, ($order_data->price * $order_data->quantity), $etc, $order_data->productname, $opt_names,$order_data->productcode);
			
			$msostyle[2] = ' style="mso-number-format:\@;"';
			$msostyle[5] = ' style="mso-number-format:\@;"';
			$msostyle[6] = ' style="mso-number-format:\@;"';
			$msostyle[12] = ' style="mso-number-format:\@;"';
			echo "<tr>";
			for($i = 0 ; $i < count($data_list) ; $i++){
				//if($i!=0) echo ",";
				echo "<td".$msostyle[$i].">" . doubleQuote($data_list[$i]) . "</td>";
			}
			echo "</tr>";
			echo "\n";
			
		unset($data_list);
		
	}

}
echo "</table>";


function doubleQuote($str) {
	return str_replace('"', '""', $str);
}
?>
