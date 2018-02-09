<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."bankda/lib/bankda.class.php");
include("access.php");

$bankda = new BANKDA();
$bankda->bankListUpdate();
$bankda->all='Y';
$data_list = $bankda->getBankList();

extract($_REQUEST);
$CurrentTime = time();

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=bankmatch_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$excel_info = "0,1,2,3,4,5,6,7,8";
$excel_ok  = $_shopdata->excel_ok;

$excelval=array(
	array("번호"									,&$bknum),				#0
	array("입금완료일"									,&$bkdate),		#1
	array("계좌번호"					,&$bkacctno),		#2
	array("은행명"				                ,&$bkname),			#3
	array("입금금액"									,&$bkinput),		#4
	array("입금자명"							        ,&$bkjukyo),				#5
	array("현재상태"								,&$status_tag),			#6
	array("최종 매칭일"								,&$match_date),				#7
	array("주문번호"							,&$ordercode)				#8
);

$arr_excel = explode(",",$excel_info);
$cnt = count($arr_excel);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<?
echo "<table border=1><tr>";
if($title!="NO") {
	for($i=0;$i<$cnt;$i++) {
            echo "<td>";
            echo $excelval[$arr_excel[$i]][0];
            echo "</td>";
	}
	echo "</tr>";
}

if(is_array($data_list)){ 
	$num = 0;
	foreach($data_list as $data){
		$num++;
		$bknum		= $num;		
		$bkdate		= $data->bkdate;
		$bkacctno	= $data->bkacctno;
		$bkname	= $data->bkname;
		$bkinput		= number_format($data->bkinput);
		$bkjukyo	= $data->bkjukyo;
		$status_tag	= $data->status_tag;
		$match_date	= $data->match_date;
		$ordercode	= $data->ordercode;

		echo "<tr>";
		for($i=0;$i<$cnt;$i++) {
			echo "<td>";
			echo doubleQuote($excelval[$arr_excel[$i]][1]);
			echo "</td>";
		}
		echo "</tr>";
	}
}
echo "</table>";
?>
</body>
</html>
<?
function doubleQuote($str) {
	return str_replace('"', '""', $str);
}
?>
