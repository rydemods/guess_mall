<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=event_photo_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$no     = $_POST["no"];

$sql  = "SELECT * FROM tblboard_promo ";
$sql .= "WHERE promo_idx = {$no} ";
$sql .= "ORDER BY num desc ";

$result=pmysql_query($sql,get_db_conn());

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

<style type="text/css">
img  { padding:0px;margin:0px;border: solid 0px white; width:100; height:100; }
</style>

<table border="1">
    <tr align="center">
        <th>NO</th>
        <th>아이디</th>
        <th>제목</th>
        <th>이미지1</th>
        <th>이미지2</th>
        <th>이미지3</th>
        <th>이미지4</th>
        <th>날짜</th>
    </tr>
<?

$imgDomain = "http://" . $_ShopInfo->getShopurl() . "data/shopimages/board/photo/";

$num = 0;
$width = 100;
$height = 100;
while($row=pmysql_fetch_object($result)) {
    $num++;
?>
    <tr>
      <td align="center"><?=number_format($num)?></td>
      <td><?=$row->mem_id?></td>
      <td><?=trim($row->title)?></td>
      <td nowrap='true' style="width:<?=$width?>;height:<?=$height?>;"><?php if ( $row->vfilename ) { echo "<img src='" . $imgDomain.$row->vfilename . "' width='{$width}' height='{$height}' >"; } else { echo "&nbsp;"; } ?></td>
      <td nowrap='true' style="width:<?=$width?>;height:<?=$height?>;"><?php if ( $row->vfilename2 ) { echo "<img src='" . $imgDomain.$row->vfilename2 . "' width='{$width}' height='{$height}' >"; } else { echo "&nbsp;"; } ?></td>
      <td nowrap='true' style="width:<?=$width?>;height:<?=$height?>;"><?php if ( $row->vfilename3 ) { echo "<img src='" . $imgDomain.$row->vfilename3 . "' width='{$width}' height='{$height}' >"; } else { echo "&nbsp;"; } ?></td>
      <td nowrap='true' style="width:<?=$width?>;height:<?=$height?>;"><?php if ( $row->vfilename4 ) { echo "<img src='" . $imgDomain.$row->vfilename4 . "' width='{$width}' height='{$height}' >"; } else { echo "&nbsp;"; } ?></td>
      <td><?=date("Y-m-d H:i:s", $row->writetime)?></td>
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

