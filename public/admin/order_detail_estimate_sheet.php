<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("access.php");

$ordercode=$_POST["ordercode"];

if($ordercode==NULL) {
	alert_go('잘못된 접근입니다.','c');
}

$sql="SELECT * FROM tbl_estimate_sheet WHERE no='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);

if(!$_ord) {
	alert("해당 주문내역이 존재하지 않습니다.");
}
$isupdate=false;

$pgid_info="";
$pg_type="";

$pg_type=trim($pg_type);

$tax_type=$_shopdata->tax_type;

## 쿠폰 복구 1회
#
$sql="SELECT * FROM tbl_estimate_sheet WHERE no='{$ordercode}'";
$result=pmysql_query($sql,get_db_conn());
$_ord=pmysql_fetch_object($result);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>
<body>
<?=$_ord->id?>
<table border=1>
	<tr>
		<!--<td>구매자</td>-->
		<td>상품명</td>
		<td>이미지</td>
		<td>수량</td>
		<td>가격</td>
		<td>적립금</td>
	</tr>
<?


$ord = $_ord->quantity;

$tinyimage	= (explode("|",substr($_ord->tinyimage,0,strrpos($_ord->tinyimage,"|"))));
$sellprice	= (explode("|",substr($_ord->sellprice,0,strrpos($_ord->sellprice,"|"))));
$quantity	= (explode("|",substr($_ord->quantity,0,strrpos($_ord->quantity,"|"))));
$reserve	= (explode("|",substr($_ord->reserve,0,strrpos($_ord->reserve,"|"))));
$productname	= (explode("|",substr($_ord->productname,0,strrpos($_ord->productname,"|"))));


$allprice =0;
$reserverprice=0;
$deliprice=0;
$rowspan = intval(count(explode("|",substr($ord,0,strrpos($ord,"|")))));
	for($i=0 ; $i<$rowspan  ; $i++){
	
			$allprice += $sellprice[$i];														// 가격*수량
			$reserverprice += intval($sellprice[$i]*$quantity[$i]*$reserve[$i]/100/100)*100 ;	// 100원 미만 절사
?>
		<tr>
			<!--<td ><?=$_ord->id?></td>-->
			<td><?=$productname[$i]?></td>

			<?if( file_exists($Dir.DataDir."shopimages/product/".$tinyimage[$i])  ){?>
				<td><img src="<?=$Dir.DataDir."shopimages/product/".$tinyimage[$i]?>"></img></td>
			<?}else if( file_exists($Dir.$temp[$i*6+2])  ){?>
				<td><img src="<?=$Dir.$tinyimage[$i]?>"></img></td>
			<?}else{?>
				<td><img src="<?=$Dir?>images/no_img.gif"></img></td>
			<?}?>

			<td><?=$quantity[$i]?></td>
			<td><?=number_format($sellprice[$i]*$quantity[$i])?></td>
			<td><?=number_format(intval($sellprice[$i]*$quantity[$i]*$reserve[$i]/100/100)*100)?></td>
		</tr>

		<?

} // for문

?>
<tr>
	<td>
		<?if( $allprice > 100000){ $deliprice=0;?>
		배송비 : 0
		<?}else{ $deliprice = 2500;?>
		배송비 : 2500
		<?}?>
	</td>
	<td>
		총 판매가 : <?=number_format($allprice)?>
	</td>
	<td>
		총 적립금 : <?=number_format($reserverprice)?>
	</td>
	<td colspan=2>
		총 결제 금액 : <?=number_format($allprice+$deliprice)?>
	</td>
	<!--<td><input type=button value="출력 하기" onclick=window.print();></img>-->
	</td>
</tr>

</table>
</body>
</html>