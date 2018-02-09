<?php 
/*
INICIS 공통 통보 처리 (RESULT=OK, RESULT=NO)
*/
if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ip=$_SERVER['REMOTE_ADDR'];

$ordercode=$_POST["ordercode"];
$type_msg=$_POST["type_msg"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$paymethod="";
$istaxsave=false;
$date=date("YmdHis");
if(($type_msg=="0400" || $type_msg=="0200") && ord($ordercode) && ord($price) && ord($ok)) {	//가상계좌 입금통보
	######### paymethod=>"O|Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);

	if($ok=="Y" && strstr("OQ", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET bank_date='{$date}', deli_gbn='N' ";
		$sql.= "WHERE ordercode='{$ordercode}' AND price='{$price}' ";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='N' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());
		}
	} else if($ok=="C" && strstr("OQ", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo SET bank_date=NULL ";
		$sql.= "WHERE ordercode='{$ordercode}' AND price='{$price}' ";
		pmysql_query($sql,get_db_conn());
	} else {
		echo "RESULT=NO"; exit;
	}

	if(!pmysql_error()) {
		echo "RESULT=OK";
		$istaxsave=true;
	} else {
		echo "RESULT=NO";
		if(ord(AdminMail)) {
			@mail(AdminMail,"INICIS 가상계좌 입금/취소 처리 안됨","$sql<br>".pmysql_error());
		}
	}

	if($istaxsave) {
		$sql = "SELECT tax_type FROM tblshopinfo ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$tax_type=$row->tax_type;
		pmysql_free_result($result);

		if($tax_type=="Y") {
			if(strstr("OQ", $paymethod[0])) {
				$sql = "SELECT bank_date, pay_admin_proc FROM tblorderinfo ";
				$sql.= "WHERE ordercode='{$ordercode}' ";				
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
				if(strlen($row->bank_date)>=12 && $row->pay_admin_proc=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='N' ";
					$result=pmysql_query($sql,get_db_conn());
					$row=pmysql_fetch_object($result);
					pmysql_free_result($result);
					if($row->cnt>0) {
						$flag="Y";
						include($Dir."lib/taxsave.inc.php");
					}
				} else {
					//현금영수증 취소처리를 해야될까?????
				}
			}
		}
	}
} else {
	echo "RESULT=NO";
}
?>
