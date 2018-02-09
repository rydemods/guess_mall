<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");


	$mode=trim($_POST[mode]);

	$productcode = $_POST[productcode];
	$quantity = $_POST[quantity];	

	$sql = "SELECT * FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND productcode='{$productcode}' ";

	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);

	if (strlen($productcode)==18) {
		$vdate = date("YmdHis");
		$sql = "SELECT COUNT(*) as cnt FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($row->cnt>=200) {
			echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
		} else {
			if(strlen($_ShopInfo->getMemid())==0) {
				$sql = "INSERT INTO tblbasket(
				tempkey			,
				productcode		,
				opt1_idx		,
				opt2_idx		,
				optidxs			,
				quantity		,
				package_idx		,
				assemble_idx	,
				assemble_list	,
				date			) VALUES (
				'".$_ShopInfo->getTempkey()."',
				'{$productcode}',
				'0',
				'0',
				'0',
				'{$quantity}',
				'0',
				'0',
				'0',
				'{$vdate}')";
				pmysql_query($sql,get_db_conn());
			}else{
				$sql = "INSERT INTO tblbasket(
				tempkey			,
				productcode		,
				opt1_idx		,
				opt2_idx		,
				optidxs			,
				quantity		,
				package_idx		,
				assemble_idx	,
				assemble_list	,
				date,id) VALUES (
				'".$_ShopInfo->getTempkey()."',
				'{$productcode}',
				'0',
				'0',
				'0',
				'{$quantity}',
				'0',
				'0',
				'0',
				'{$vdate}','".$_ShopInfo->getMemid()."')";
				pmysql_query($sql,get_db_conn());
			}
		}
	}

location.go($_POST[returnUrl]);
?>