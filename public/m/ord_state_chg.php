<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$mode=$_POST['mode'];

switch($mode){
	
	case "cart":  //장바구니에서 주문
		/*
		$qry="update tblbasket set ord_state=false where tempkey='".$_ShopInfo->getTempkey()."'";
		pmysql_query($qry);

		$qry="update tblbasket set ord_state=true where tempkey='".$_ShopInfo->getTempkey()."'";

		if(count($_POST['basketno'])>0){

			$productno= implode("','",$_POST['basketno']);
			
			$qry.=" and basketidx in ('".$productno."')";
		}
		*/
		$sql = "update tblbasket set tempkey='".$_ShopInfo->getTempkey()."' where tempkey = '".$_ShopInfo->getTempkeySelectItem()."'";
		pmysql_query($sql,get_db_conn());
		
		
		if(count($_POST['basketno'])>0){
			$productno= implode("','",$_POST['basketno']);
		}

		$qry = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkeySelectItem()."' WHERE basketidx not in ('".$productno."') AND tempkey='".$_ShopInfo->getTempkey()."'";
		pmysql_query($qry);
		
		
		if(pmysql_query($qry)){
			echo "s";
		}else{
			echo "f";
		}

		break;
}
?>