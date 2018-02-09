<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$ndate = date('Ymd');

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sellprice_update_excel_$ndate.csv"); 
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
	10=>array("하나은행"),		#10
	11=>array("보성산업"),		#11
	12=>array("농협"),		#12
	13=>array("국민은행"),		#13
	14=>array("기업은행"),		#14
	15=>array("신한은행"),		#15
	16=>array("우리은행"),		#16
	17=>array("미지정 9"),		#17
	18=>array("미지정 10")		#18
);
$arr_excel = explode(",",$excel_info);
$cnt = count($arr_excel);

for($i=0;$i<$cnt;$i++) {
	if($i!=0) echo ",";
	echo iconv('UTF-8', 'EUC-KR',$excelval[$arr_excel[$i]][0]);
}
echo "\n";

foreach($ex_productcode as $ep=>$epv){
	
	if($epv){

		$qry="select * from tblproduct where productcode='".$epv."'";
		$result=pmysql_query($qry);
		$data=pmysql_fetch_object($result);

		$data_csv[0]=$data->productcode;
		$data_csv[1]=$data->brandcdnm;
		$data_csv[2]=$data->self_goods_code;
		$data_csv[3]=$data->productname;
		$data_csv[4]=$data->prodcode;
		$data_csv[5]=$data->colorcode;
		$data_csv[6]=$data->consumerprice;
		$data_csv[7]=$data->sellprice;

			
		for($i=0;$i<$cnt;$i++) {
			if($i!=0) echo ",";
			if ($i == '0') echo "=";
			echo '"' . doubleQuote(iconv('UTF-8', 'EUC-KR', $data_csv[$i])) . '"';
		}

		echo "\n";
	
	}
}
function doubleQuote($str) {
	return str_replace('"', '""', $str);
}

?>