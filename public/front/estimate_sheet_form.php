<?
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//include_once($Dir."lib/order.class.php");
//include("access.php");
//include("calendar.php");
//include_once($Dir."lib/shopdata.php");
//include_once($Dir."lib/product.class.php");


$id = $_POST['rowid'];

$temp = explode("|",$_POST['strBasket']);
$allprice;
$deliprice;
$reserverprice;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>
<body>
<table border=1>
	<tr>
		<td>구매자</td>
		<td>상품명</td>
		<td>이미지</td>
		<td>수량</td>
		<td>가격</td>
		<td>적립금</td>
	</tr>
<?
	for($i=0 ; $i< intval(count($temp)/6) ; $i++){
			$allprice += $temp[$i*6+4]*$temp[$i*6+1];											// 가격*수량
			$reserverprice += intval($temp[$i*6+4]*$temp[$i*6+5]*$temp[$i*6+1]/100/100)*100 ;	// 100원 미만 절사
?>
		<tr>
			<td><?=$id?></td>
			<td><?=$temp[$i*6+3]?></td>

			<?if( file_exists($Dir.DataDir."shopimages/product/".$temp[$i*6+2])  ){?>
				<td><img src="<?=$Dir.DataDir."shopimages/product/".$temp[$i*6+2]?>"></img></td>
			<?}else if( file_exists($Dir.$temp[$i*6+2])  ){?>
				<td><img src="<?=$Dir.$temp[$i*6+2]?>"></img></td>
			<?}else{?>
				<td><img src="<?=$Dir?>images/no_img.gif"></img></td>
			<?}?>

			<td><?=$temp[$i*6+1]?></td>
			<td><?=number_format($temp[$i*6+4]*$temp[$i*6+1])?></td>
			<td><?=number_format(intval($temp[$i*6+4]*$temp[$i*6+5]*$temp[$i*6+1]/100/100)*100)?></td>
		</tr>

		<?

		$sql  = "INSERT INTO tbl_estimate_sheet(id,tinyimage,sellprice,quantity,reserve,productname) VALUES ('{$id}','{$temp[$i*6+2]}','{$temp[$i*6+4]}','{$temp[$i*6+1]}','{$temp[$i*6+5]}','{$temp[$i*6+3]}')";

		//$sql  = "INSERT INTO tbl_estimate_sheet(id,tinyimage,sellprice,quantity,reserve,productname)    VALUES ('".$id."','".$temp[$i*6+2]."','".$temp[$i*6+4]."','".$temp[$i*6+1]."','".$temp[$i*6+5]."','".$temp[$i*6+3]."')";
		//$aa = pmysql_query($insert);

		pmysql_query($sql,get_db_conn());
		exdebug($sql);exit;

		
/**
		$select ="select * from tbl_estimate_sheet where id=".$_POST['rowid']." tinyimage=".$temp[$i*6+2]." sellprice=".$temp[$i*6+4]." quantity=".$temp[$i*6+1]." reserve=".$temp[$i*6+5]." producname=".$temp[$i*6+3]."";

**/
	/**	if(pmysql_query($select,get_db_conn())){
			echo "값 있는건데..??";
		}else{
			**?
			/**
			$sql = "insert into tbl_estimate_sheet(id,tinyimage,sellprice,quantity,reserve,productname)    values('".$id."','".$temp[$i*6+2]."','".$temp[$i*6+4]."','".$temp[$i*6+1]."','".$temp[$i*6+5]."','".$temp[$i*6+3]."')";
			pmysql_query($sql,get_db_conn());
			
			if(pmysql_query($sql,get_db_conn()) == null ){
				echo "성공";
			}else{
				echo "실패";
			}	**/
/**		}
		$a= "select * from tbl_estimate_sheet";
		$ss = pmysql_fetch_array(pmysql_query($a,get_db_conn()));
**/		

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
	<td><input type=button value="출력 하기" onclick=window.print();></img>
	</td>
</tr>

</table>
</body>
</html>