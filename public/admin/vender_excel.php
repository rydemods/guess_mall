<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=vender_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


//print_r($_POST);

$disabled   = $_POST['disabled'];
$s_check    = $_POST['s_check'];
$search     = $_POST['search'];

$qry = "WHERE delflag='N' ";
if($disabled=="Y") $qry.= "AND disabled='0' ";
else if($disabled=="N") $qry.= "AND disabled='1' ";
if(ord($search)) {
    if($s_check=="id") $qry.= "AND id='{$search}' ";
    else if($s_check=="com_name") $qry.= "AND com_name LIKE '%{$search}%' ";
}

$sql = "SELECT * FROM tblvenderinfo {$qry} ";
$result=pmysql_query($sql,get_db_conn());
//echo $sql;



?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
        <th>번호</th>
        <th>업체ID</th>
        <th>회사명</th>
        <th>회사전화</th>
        <th>담당자명</th>
        <th>휴대전화</th>
        <th>승인</th>
    </tr>
<?
$num = 0;
while($data=pmysql_fetch_object($result)) {
    
    $num++;
    if($data->disabled == "0") $allow = "Y";
    else $allow = "N";
?>
    <tr>
      <td align="center"><?=number_format($num)?></td>
      <td><?=$data->id?></td>
      <td><?=$data->com_name?></td>
      <td><?=$data->com_tel?></td>
      <td><?=$data->p_name?></td>
      <td><?=$data->p_mobile?></td>
      <td><?=$allow?></td>
    </tr>
<?
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);
?>
