<?
// 제휴사 가격 전체 추출
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ndate = date('Ymd');

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=allprice_update_excel_$ndate.csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$product_code_all=$_REQUEST["product_code_all"];

$ex_productcode=explode("||",$product_code_all);

$excel_info = "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";

$excelval=array(
	0=>array("상품코드"),				#0
	1=>array("브랜드"),		#1
	2=>array("상품코드"),		#2
	3=>array("상품명"),		#3
	4=>array("품번"),			#4
	5=>array("컬러"),		#5
	6=>array("정가"),				#6
	7=>array("판매가"),			#7
	8=>array("구분"),			#8
	9=>array("동서")	,		#9
	10=>array("하나은행"),		#9
	11=>array("보성산업"),		#9
	12=>array("농협"),		#9
	13=>array("국민은행"),		#9
	14=>array("기업은행"),		#9
	15=>array("신한은행"),		#9
	16=>array("우리은행"),		#9
	17=>array("미지정 9"),		#9
	18=>array("미지정 10")		#9
);
$arr_excel = explode(",",$excel_info);
$cnt = count($arr_excel);

for($i=0;$i<$cnt;$i++) {
	if($i!=0) echo ",";
	echo iconv('UTF-8', 'EUC-KR',$excelval[$arr_excel[$i]][0]);
}
echo "\n";

$sql = "SELECT * ";
$sql.= "FROM tblproduct ";
//$sql.= "where sale_price1=0";
$sql.= "ORDER BY pridx desc ";

if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
	//exdebug($sql);
	//exit;
}

$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {

		$data_csv[0]=$row->productcode;
		$data_csv[1]=$row->brandcdnm;
		$data_csv[2]=$row->self_goods_code;
		$data_csv[3]=$row->productname;
		$data_csv[4]=$row->prodcode;
		$data_csv[5]=$row->colorcode;
		$data_csv[6]=$row->consumerprice;
		$data_csv[7]=$row->sellprice;
		$data_csv[8]='';
		$data_csv[9]=$row->sale_price1;
		$data_csv[10]=$row->sale_price2;
		$data_csv[11]=$row->sale_price3;
		$data_csv[12]=$row->sale_price4;
		$data_csv[13]=$row->sale_price5;
		$data_csv[14]=$row->sale_price6;
		$data_csv[15]=$row->sale_price7;
		$data_csv[16]=$row->sale_price8;
		$data_csv[17]=$row->sale_price9;
		$data_csv[18]=$row->sale_price10;

		for($i=0;$i<$cnt;$i++) {
			if($i!=0) echo ",";
			if ($i == '0') echo "=";
			echo '"' . doubleQuote(iconv('UTF-8', 'EUC-KR', $data_csv[$i])) . '"';
		}

		echo "\n";
	
}
function doubleQuote($str) {
	return str_replace('"', '""', $str);
}

?>