<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=aff_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

// 검색조건으로 받는 모든 값 정의
$search = unserialize($_POST['search']);
//print_r($search);

$af_use		= $search['af_use'];
$af_type	= $search['af_type'];
$af_area	= $search['af_area'];
$af_name	= $search['af_name'];

if($af_use) $where[]="use='".$af_use."'";
if($af_type) $where[]="type='".$af_type."'";
if($af_area) $where[]="area='".$af_area."'";
if($af_name) $where[]="name like '%".$af_name."%'";

#---------------------------------------------------------------
# 제휴 학교/회사 리스트를 불러온다.
#---------------------------------------------------------------
$query="select * from tblaffiliatesinfo ";
if(count($where))$query.=" where ".implode(" and ",$where);
$query.=" order by idx DESC";
$result=pmysql_query($query,get_db_conn());
//echo $query;



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
        <th>구분</th>
        <th>지역</th>
        <th>학교/기업명</th>
        <th>접속경로</th>
        <th>사용여부</th>
        <th>출력여부</th>
        <th>쿠폰</th>
        <th>등록일</th>
    </tr>
<?
$num = 0;
while($data=pmysql_fetch_object($result)) {
    
    $num++;
    if ($data->type == '1')	$type	= "학교";
    if ($data->type == '2')	$type	= "기업";
    $regdt = substr($data->regdate,0,4)."-".substr($data->regdate,4,2)."-".substr($data->regdate,6,2);
    ($data->use > 0) ? $use="Y":$use="N";
    ($data->output > 0) ? $output="Y":$output="N";
?>
    <tr>
      <td align="center"><?=number_format($num)?></td>
      <td><?=$type?></td>
      <td><?=$data->area?></td>
      <td><?=$data->name?></td>
      <td><?=$data->referrer_url?></td>
      <td><?=$use?></td>
      <td><?=$output?></td>
      <td><?=$data->coupon?></td>
      <td><?=$regdt?></td>
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
