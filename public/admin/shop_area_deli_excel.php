<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=deli_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$sql = "SELECT * FROM tbldeliarea order by no desc";
$result=pmysql_query($sql,get_db_conn());
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border=1>
	<tr>
		<td>특수지역명</td>
		<td>시작 우편번호</td>
		<td>끝 우편번호</td>
		<td>배송비</td>
	</tr>
	<?while($row=pmysql_fetch_object($result)) {?>
	<tr>
		<td style="mso-number-format:\@"><?=$row->area_name?></td>
		<td style="mso-number-format:\@"><?=$row->st_zipcode?></td>
		<td style="mso-number-format:\@"><?=$row->en_zipcode?></td>
		<td style="mso-number-format:\@"><?=$row->deli_price?></td>
	</tr>
	<?}?>
</table>

