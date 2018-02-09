<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$connect_ip	= $_SERVER['REMOTE_ADDR'];
$log_content	= "## 회원 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=member_excel_".date("YmdHis").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$excel_sql = $_POST["excel_sql"];
$est = $_POST["est"];

$field_count = count($est);
$field_name_list = "";

$sql = "SELECT ";
$reserve_num	= "";
foreach ( $est as $key => $val ) {
    $field_name_list .= "<th>" . $arr_admin_member_excel_info[$val][0] . "</th>";
    $sql .= $arr_admin_member_excel_info[$val][1] . ",";
	if (trim($arr_admin_member_excel_info[$val][0]) == utf8encode('통합포인트')) $reserve_num	= $key;
}
$sql = rtrim($sql, ",") . " ";
$sql .= "FROM ( {$excel_sql} ) AS tblmember ";

$result=pmysql_query($sql,get_db_conn());
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
        <?=$field_name_list?>
    </tr>
<?
$num = 0;

// ERP 접속 (김재수)
$oci_conn = GetErpDBConn();

while($row=pmysql_fetch_array($result)) {
    $num++;	
	$erp_mem_reserve	= getErpMeberPoint($row['id'], $oci_conn);
	$mem_reserve	= $erp_mem_reserve[p_err_code]==0?$erp_mem_reserve[p_data]:'0';
?>
    <tr>
        <?php
            for ( $i = 0; $i < $field_count; $i++ ) {
				if ($reserve_num	 !='' && $reserve_num == $i) $row[$i]	= number_format($mem_reserve);
                echo "<td style=mso-number-format:'\@'>" . $row[$i] . "</td>";
            }
        ?>
    </tr>
<?
}
pmysql_free_result($result);

// ERP 접속종료 (김재수)
GetErpDBClose($oci_conn);
?>
</table>
</body>
</html>
