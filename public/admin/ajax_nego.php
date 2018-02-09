<?
header("Content-Type: text/html;charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");

$tinyimage	= $_REQUEST['tinyimage'];
$reserve	= $_REQUEST['reserve'];
$productname	= $_REQUEST['productname'];

$sellprice	= $_REQUEST['sellprice'];
$quantity	= $_REQUEST['quantity'];
$prcode		= $_REQUEST['productcode'];

$email			= $_REQUEST['email'];
$tel		= $_REQUEST['tel'];
$id			= $_REQUEST['id'];

$basketidx = $_REQUEST['basketidx'];
$price		= $_REQUEST['price'];
$no = $_REQUEST['no'];
$estimate_price  = str_replace(",","",$price);

//echo $productname;

$check = (preg_match("/^[0-9]+$/",$estimate_price));


if ( $check){
/*	$select ="select * from tbl_estimate_sheet where id='".$id."'  and  productcode='".$prcode."' and   quantity='".$quantity."'";

	$row =pmysql_fetch_object(pmysql_query($select));
	if($row){ */// 회원
		// update 문	$no
		$query = "update tbl_estimate_sheet  set  estimate_price ='".$estimate_price."'  where 
				no='".$no."' ";
	/*			 
	}else{ // 비회원
		// insert 문
		$query = "insert into tbl_estimate_sheet(estimate_price,tinyimage,reserve,productname,date,sellprice,quantity,productcode,email,tel,basketidx) 
		values ('".$estimate_price."','".$tinyimage."','".$reserve."','".$productname."',date(now()),'".$sellprice."','".$quantity."','".$prcode."','".$email."','".$tel."','".$basketidx."')";
	}	
	*/
	//echo $query;
	pmysql_query($query);
	echo 1;
}else{
	echo "숫자를 입력하세요";
}



?>