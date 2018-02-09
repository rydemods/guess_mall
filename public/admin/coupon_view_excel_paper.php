<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
 
$coupon_code = (int) $_GET['coupon_code'];

$query ="select * from tblcouponpaper where coupon_code='$coupon_code' order by used desc, coupon_code asc";
$result = pmysql_query($query,get_db_conn());


header( 'Content-type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment; filename=['. strftime( '%y년%m월%d일' ) .'] 쿠폰번호.xls' );
header( 'Content-Description: PHP4 Generated Data' );
?>
<html>
<head>
<title>list</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
</head>
<body>
<table border="1">
<tr><td align = 'center'>쿠폰번호</td><td align = 'center'>사용여부</td></tr>
<?while($row=pmysql_fetch_object($result)) {?>
<tr><td align = 'left'><?=$row->papercode?></td><td align = 'center'><?=$row->used=='N'?'미사용':'사용'?></td></tr>
<?}?>
</table>
</body>
</html>
