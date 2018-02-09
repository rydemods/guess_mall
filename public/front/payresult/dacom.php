<?php 
/*
LG������ ���� �뺸 ó�� (RESULT=OK, RESULT=NO)
*/
if($_SERVER['SERVER_ADDR']!=$_SERVER['REMOTE_ADDR']) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ip=$_SERVER['REMOTE_ADDR'];

$ordercode=$_POST["ordercode"];
$paytype=$_POST["paytype"];
$txtype=$_POST["txtype"];
$cflag=$_POST["cflag"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$paymethod="";
$istaxsave=false;
$date=date("YmdHis");

if($paytype=="SC0040" && ord($ordercode) && ord($price) && ord($ok)) {	//������� �Ա��뺸
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
			@mail(AdminMail,"LG������ ������� �Ա�ó�� �ȵ�","$sql<br>".pmysql_error());
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
					//���ݿ����� ���ó���� �ؾߵɱ�?????
				}
			}
		}
	}
} else if((strstr("NCR", $txtype)) && ord($ordercode) && ord($ok)) {//�ڵ�����Ȯ��/����Ȯ��/���
	#### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"!C", deli_gbn=>"Y", escrow_result=>"N" #####
	$sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
		$escrow_result=$row->escrow_result;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if($ok=="Y" && strstr("QP", $paymethod[0])) {			//����Ȯ��
		$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"LG������ ����ũ�� ����Ȯ��ó�� �ȵ�","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else if($ok=="C" && strstr("QP", $paymethod[0])) {	//�������
		$sql = "UPDATE tblorderinfo SET ";
		$sql.= "deli_gbn		= 'H' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"LG������ ����ũ�� �������ó�� �ȵ�","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($txtype=="X" && ord($ordercode)) {		//����ũ�� ������� �ԱݿϷ�� �ǿ� ���ؼ� ������
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", (deli_gbn=>"N|S|X" OR (deli_gbn=>"D" AND ord(deli_date)==0))  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, bank_date, deli_gbn, deli_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$bank_date=$row->bank_date;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);
	if(strstr("Q", $paymethod[0])) {
		$sql = "SELECT tax_type FROM tblshopinfo ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$tax_type=$row->tax_type;
		pmysql_free_result($result);

		if($tax_type=="Y") {	//���ݿ����� �ڵ� ����
			$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='{$ordercode}' AND type='Y' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($row->cnt>0) {
				$flag="C";
				include($Dir."lib/taxsave.inc.php");
			}
		}

		if (strlen($bank_date)==14) {
			$deliupdate =" deli_gbn='E' ";	//ȯ�Ҵ��
			$up_deli_gbn="E";
		} else {
			$deliupdate = " deli_gbn='C' ";
			$up_deli_gbn="C";
		}

		$sql = "UPDATE tblorderinfo SET {$deliupdate} ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='{$up_deli_gbn}' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			pmysql_query($sql,get_db_conn());
			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"LG������ ����ũ�� ������ó�� �ȵ�","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($txtype=="D" && ord($ordercode)) {	//������Ұ�� (����ũ�� ������� ȯ���뺸)
	######### paymethod=>"Q", pay_flag=>"0000", pay_admin_proc=>"N", deli_gbn=>"E"  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$bank_date=$row->bank_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	pmysql_free_result($result);

	if(strstr("Q", $paymethod[0])) {
		$sql = "UPDATE tblorderinfo ";
		$sql.= "SET pay_admin_proc='C', deli_gbn='C', bank_date='".substr($bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='{$ordercode}' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='{$ordercode}' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			pmysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(ord(AdminMail)) {
				@mail(AdminMail,"LG������ ������� ȯ�ҿϷ�ó�� �ȵ�","$sql<br>".pmysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO";
	}
}
?>
