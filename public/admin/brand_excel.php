<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=brand_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


//print_r($_POST);

$s_keyword = $_POST['s_keyword'];
$sort_opt = $_POST['sort_opt'];
$mall_type = $_POST['mall_type'];

// 메뉴 노출
$display = array('0'=>'N','1'=>'Y');
// 몰 타입
$mallTypeArr = array('0'=>'전체','1'=>'교육몰','2'=>'기업몰');
// 노출 카테고리 타입
$cateTypeArr = array('1'=>'디지털/가전','2'=>'패션/잡화/기타');


$addQry = 'WHERE 1=1 ';
if( !is_null($s_keyword) && $s_keyword != '' ){
	$addQry.= 'AND UPPER( brandname ) LIKE UPPER( \'%'.trim($s_keyword).'%\' ) ';
}

if($mall_type) $addQry.= "AND mall_type = {$mall_type} ";

if($sort_opt == "display") $orderby = "display_yn desc, brandname asc";
else $orderby = "brandname asc";

$sql = "SELECT * FROM tblproductbrand ";
$sql.= $addQry;
$sql.= "ORDER BY {$orderby} ";
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
        <th>몰 타입</th>
        <th>브랜드명</th>
        <th>메인 노출 위치</th>
        <th>노출 여부</th>
    </tr>
<?
$num = 0;
while($data=pmysql_fetch_object($result)) {
    
    $num++;
?>
    <tr>
      <td align="center"><?=number_format($num)?></td>
      <td><?=$mallTypeArr[$data->mall_type]?></td>
      <td><?=$data->brandname?></td>
      <td><?=$cateTypeArr[$data->category_type]?></td>
      <td><?=$display[$data->display_yn]?></td>
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
