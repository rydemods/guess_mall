<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sellprice_update_excel.csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$sql = "select * from tblproduct ";
$result=pmysql_query($sql);
while($row=pmysql_fetch_object($result)) {
		$product_code_all .= $row->productcode."||";
}

$ex_productcode=explode("||",$product_code_all);

$excel_info = "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";

$sql = "select * from tblcompanygroup ";
$result=pmysql_query($sql);
$num=0;
while($row=pmysql_fetch_object($result)) {
		$group_name[$num] = $row->group_name;
		$num++;
}
/*
echo $num;
echo $group_name[0];

exit;
print_r($group_name);
for($num ; $num > 0 ; $num--){
echo $num;
}

exit;
*/
$excelval=array(
	0=>array("상품코드"),				#0
	1=>array("브랜드"),		#1
	2=>array("상품코드"),		#2
	3=>array("상품명"),		#3
	4=>array("품번"),			#4
	5=>array("컬러"),		#5
	6=>array("정가"),				#6
	7=>array("판매가"),			#7
	8=>array("변경될 판매가"),			#8
	9=>array($group_name[0])	,		#9
	10=>array($group_name[1]),		#9
	11=>array($group_name[2]),		#9
	12=>array($group_name[3]),		#9
	13=>array($group_name[4]),		#9
	14=>array($group_name[5]),		#9
	15=>array($group_name[6]),		#9
	16=>array($group_name[7]),		#9
	17=>array($group_name[8]),		#9
	18=>array($group_name[9])		#9
);
//print_r($excelval);
//exit;
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